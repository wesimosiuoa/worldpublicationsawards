<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


/**
 * Render country flag (GLOBAL, SAFE, CDN-BASED)
 *
 * @param int|string|null $value  country_id OR ISO code
 * @param int $size               desired display size (px)
 * @return string
 */


require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';


function render_country_flag($value = null, int $size = 24): string
{
    global $pdo;

    if (empty($value)) {
        return '';
    }

    $isoCode = null;

    // CASE 1: ISO code directly (e.g. "US")
    if (is_string($value) && strlen($value) === 2) {
        $isoCode = strtoupper($value);
    }

    // CASE 2: country_id
    if (is_numeric($value) && $pdo) {
        try {
            $stmt = $pdo->prepare(
                "SELECT iso_code 
                 FROM countries 
                 WHERE id = ? AND is_active = 1 
                 LIMIT 1"
            );
            $stmt->execute([(int)$value]);
            $isoCode = $stmt->fetchColumn();
        } catch (PDOException $e) {
            return '';
        }
    }

    if (!$isoCode || strlen($isoCode) !== 2) {
        return '';
    }

    $isoCode = strtolower($isoCode);

    // FlagCDN allowed widths
    $allowedSizes = [20, 40, 80, 160, 320, 640];
    $cdnSize = 40;

    foreach ($allowedSizes as $allowed) {
        if ($size <= $allowed) {
            $cdnSize = $allowed;
            break;
        }
    }

    return '<img
        src="https://flagcdn.com/w' . $cdnSize . '/' . $isoCode . '.png"
        alt="' . strtoupper($isoCode) . ' flag"
        loading="lazy"
        style="width:' . (int)$size . 'px; height:auto; vertical-align:middle; margin-right:6px;"
    >';
}

function sendEmail ($email, $subject, $message)
{   
    include_once 'messages.php';
    try{
        $mail = new PHPMailer(true);
        $mail -> isSMTP();
        $mail -> Host =  'smtp.gmail.com';
        $mail -> SMTPAuth = true;
        $mail -> Username = 'wezimosiuoa@gmail.com';
        $mail -> Password = 'exyv qdzk yyan gelw'; 
        $mail -> SMTPSecure = 'ssl';
        $mail -> Port = 465;

        $mail -> setFrom('wezimosiuoa@gmail.com');
        $mail -> addAddress($email);

        $mail -> isHTML(true);
        $mail -> Subject = $subject;
        $mail -> Body = $message;

        $mail -> send();

        if (function_exists('showSuccessMessage')) {
            showSuccessMessage("Email sent successfully");
        }
    }catch(Exception $err){
        if (function_exists('showErrorMessage')) {
            showErrorMessage("Email could not be sent. Mailer Error: " . $err->getMessage());
        }
    }
}


//calculate amount raised this year and last year, and total amount raised, note: nominee takes only 1.9 from each vote (3.00)
function calculateAmountRaised($nomineeId) {
    global $pdo;

    $currentYear = date('Y');
    $lastYear = $currentYear - 1;

    try {
        // Calculate amount raised this year
        $stmtThisYear = $pdo->prepare(
            "SELECT COUNT(*) 
             FROM votes 
             WHERE nominee_id = ? 
             AND YEAR(created_at) = ?"
        );
        $stmtThisYear->execute([$nomineeId, $currentYear]);
        $votesThisYear = $stmtThisYear->fetchColumn();
        $amountThisYear = $votesThisYear * 1.9;

        // Calculate amount raised last year
        $stmtLastYear = $pdo->prepare(
            "SELECT COUNT(*) 
             FROM votes 
             WHERE nominee_id = ? 
             AND YEAR(vote_date) = ?"
        );
        $stmtLastYear->execute([$nomineeId, $lastYear]);
        $votesLastYear = $stmtLastYear->fetchColumn();
        $amountLastYear = $votesLastYear * 1.9;

        // Calculate total amount raised
        $totalVotes = $votesThisYear + $votesLastYear;
        $totalAmount = $totalVotes * 1.9;

        return [
            'this_year' => number_format($amountThisYear, 2),
            'last_year' => number_format($amountLastYear, 2),
            'total' => number_format($totalAmount, 2)
        ];
    } catch (PDOException $e) {
        return [
            'this_year' => '0.00',
            'last_year' => '0.00',
            'total' => '0.00'
        ];
    }
}

//calculate total votes for a nominee this year and last year and total votes, note: each vote is 3.00 but we only care about the count of votes, not the amount raised
function calculateTotalVotes($nomineeId) {
    global $pdo;

    $currentYear = date('Y');
    $lastYear = $currentYear - 1;

    try {
        // Calculate votes this year
        $stmtThisYear = $pdo->prepare(
            "SELECT COUNT(*) 
             FROM votes 
             WHERE nominee_id = ? 
             AND YEAR(created_at) = ?"
        );
        $stmtThisYear->execute([$nomineeId, $currentYear]);
        $votesThisYear = $stmtThisYear->fetchColumn();

        // Calculate votes last year
        $stmtLastYear = $pdo->prepare(
            "SELECT COUNT(*) 
             FROM votes 
             WHERE nominee_id = ? 
             AND YEAR(vote_date) = ?"
        );
        $stmtLastYear->execute([$nomineeId, $lastYear]);
        $votesLastYear = $stmtLastYear->fetchColumn();

        // Calculate total votes
        $totalVotes = $votesThisYear + $votesLastYear;

        return [
            'this_year' => (int)$votesThisYear,
            'last_year' => (int)$votesLastYear,
            'total' => (int)$totalVotes
        ];
    } catch (PDOException $e) {
        return [
            'this_year' => 0,
            'last_year' => 0,
            'total' => 0
        ];
    }
}