<?php 




    include '../includes/dbcon.inc.php';
    include '../includes/helpers.php';

    header('Content-Type: application/json');

    // Only allow POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    // Read JSON body
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON payload']);
        exit;
    }

    // Extract required fields
    $orderID    = $data['orderID'] ?? null;
    $nominee_id = (int)($data['nominee_id'] ?? 0);

    // Basic validation
    if (!$orderID || $nominee_id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid payment data']);
        exit;
    }

    // Validate nominee exists and active
    $checkNomineeStmt = $pdo->prepare(
        "SELECT id FROM nominees WHERE id = ? AND is_active = 1"
    );
    $checkNomineeStmt->execute([$nominee_id]);

    if (!$checkNomineeStmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Nominee not found']);
        exit;
    }
    // =============================
// PAYPAL VERIFICATION SECTION
// =============================

// Your sandbox credentials
    $clientId = "ATaPxMLK8HSXmee6813Sy5fs4I2PmiAruEb_LOb3Kwk7LbNBNNN5S0nvN_4d-CA5w2SCo1hcBAR99isa";
    $secret   = "EC8_Vsm4TKaHoUn0vT7mcZWcuY2eLqpURONLO9AWvr8FMQUxFhWFlWUmKfpzbS866MjZ429ONDeTOhsS";

    // 1️⃣ Get Access Token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secret);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept: application/json",
        "Accept-Language: en_US"
    ]);

    $tokenResponse = json_decode(curl_exec($ch), true);
    $accessToken = $tokenResponse['access_token'] ?? null;

    if (!$accessToken) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'PayPal auth failed']);
        exit;
    }

    // 2️⃣ Verify Order
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderID");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $accessToken"
    ]);

    $orderData = json_decode(curl_exec($ch), true);

    // Validate payment
    if (
        !$orderData ||
        $orderData['status'] !== 'COMPLETED'
    ) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Payment not completed']);
        exit;
    }

    // Extract real amount from PayPal
    $paypalAmount = (float)$orderData['purchase_units'][0]['amount']['value'];
    $currency = $orderData['purchase_units'][0]['amount']['currency_code'];

    // Validate expected payment
    if ($paypalAmount != 3.00 || $currency !== 'USD') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid payment amount']);
        exit;
    }

    // Prevent duplicate order
    $checkOrder = $pdo->prepare("SELECT id FROM paypal_transactions WHERE order_id = ?");
    $checkOrder->execute([$orderID]);

    if ($checkOrder->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Order already processed']);
        exit;
    }



    // Extract verified data
    $paypalAmount = (float)$orderData['purchase_units'][0]['amount']['value'];
    $currency     = $orderData['purchase_units'][0]['amount']['currency_code'];
    $payerEmail   = $orderData['payer']['email_address'] ?? null;
    $payerName    = isset($orderData['payer']['name'])
        ? $orderData['payer']['name']['given_name'] . ' ' . $orderData['payer']['name']['surname']
        : null;
    $payerCountry = $orderData['payer']['address']['country_code'] ?? null;
    

    // Validate expected payment amount
    if ($paypalAmount != 3.00 || $currency !== 'USD') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid payment amount']);
        exit;
    }

    $checkOrderStmt = $pdo->prepare("SELECT id FROM paypal_transactions WHERE order_id = ?");
    $checkOrderStmt->execute([$orderID]);

    if ($checkOrderStmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Order already processed']);
        exit;
    }


    /* =====================================================
   DATABASE TRANSACTION (Atomic Operation)
===================================================== */

    $ip_address = getClientIP();

    try {
        $pdo->beginTransaction();

        // 1️⃣ Insert vote
        $voteStmt = $pdo->prepare("
            INSERT INTO votes (nominee_id, voter_ip, voter_email, amount, created_at, voter_country, voter_country_code)
            VALUES (?, ?, ?, ?, NOW(), ?, ?)
        ");
        $voteStmt->execute([
            $nominee_id,
            $ip_address,
            $payerEmail,
            $paypalAmount,
            $payerCountry,
            $orderData['payer']['address']['country_code'] ?? null
        ]);

        // 2️⃣ Update nominee totals
        $updateStmt = $pdo->prepare("
            UPDATE nominees
            SET total_votes = total_votes + 1,
                total_amount_raised = total_amount_raised + ?
            WHERE id = ?
        ");
        $updateStmt->execute([
            $paypalAmount,
            $nominee_id
        ]);

        // 3️⃣ Store transaction record
        $txnStmt = $pdo->prepare("
            INSERT INTO paypal_transactions 
            (order_id, nominee_id, amount, status)
            VALUES (?, ?, ?, ?)
        ");
        $txnStmt->execute([
            $orderID,
            $nominee_id,
            $paypalAmount,
            $orderData['status']
        ]);

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Vote recorded successfully',
            'transaction_id' => $orderID
        ]);

    } catch (PDOException $e) {

        $pdo->rollBack();
        error_log('Vote processing error: ' . $e->getMessage());

        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Server error while recording vote'
        ]);
    }
?>