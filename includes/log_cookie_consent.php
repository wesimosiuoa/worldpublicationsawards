<?php
require_once 'dbcon.inc.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Method not allowed");
}

$consent = $_POST['consent'] ?? null;

if (!in_array($consent, ['accepted', 'rejected'])) {
    http_response_code(400);
    exit("Invalid consent value");
}

$ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
$consent_date = date('Y-m-d H:i:s');
$consent_version = '2026.1';

$stmt = $conn->prepare("
    INSERT INTO cookie_consent_logs 
    (consent_status, ip_address, user_agent, consent_date, consent_version) 
    VALUES (?, ?, ?, ?, ?)
");

$stmt->bind_param("sssss", $consent, $ip, $user_agent, $consent_date, $consent_version);
$stmt->execute();

echo "ok";
?>

    <script>console.log("Cookies:", document.cookie);</script>
<?php