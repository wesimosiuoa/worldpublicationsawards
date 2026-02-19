<?php
/**
 * PHPMailer Configuration for World Publications Awards
 * 
 * Configure your SMTP settings here for reliable email delivery
 */

// SMTP Configuration
$mailer_config = [
    'smtp_host' => 'localhost',        // Your SMTP server (e.g., smtp.gmail.com, smtp.sendgrid.net)
    'smtp_port' => 587,                // Port (587 for TLS, 465 for SSL, 25 for unencrypted)
    'smtp_username' => 'wezimosiuoa@gmail.com', // Your SMTP username
    'smtp_password' => 'lkjcjwukldudvpho', // Your SMTP password
    'smtp_encryption' => 'tls',        // Encryption method: 'tls', 'ssl', or false
    'from_email' => 'noreply@worldpublicationawards.org',
    'from_name' => 'World Publications Awards',
    'reply_to' => 'noreply@worldpublicationawards.org'
];

// Common SMTP configurations for popular services:

// Gmail SMTP
/*
$mailer_config = [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'your-gmail@gmail.com',
    'smtp_password' => 'your-app-password', // Use App Password, not regular password
    'smtp_encryption' => 'tls',
    'from_email' => 'your-gmail@gmail.com',
    'from_name' => 'World Publications Awards',
    'reply_to' => 'your-gmail@gmail.com'
];
*/

// SendGrid SMTP
/*
$mailer_config = [
    'smtp_host' => 'smtp.sendgrid.net',
    'smtp_port' => 587,
    'smtp_username' => 'apikey', // Always 'apikey' for SendGrid
    'smtp_password' => 'your-sendgrid-api-key',
    'smtp_encryption' => 'tls',
    'from_email' => 'noreply@worldpublicationawards.org',
    'from_name' => 'World Publications Awards',
    'reply_to' => 'noreply@worldpublicationawards.org'
];
*/

// Amazon SES SMTP
/*
$mailer_config = [
    'smtp_host' => 'email-smtp.us-east-1.amazonaws.com', // Change region as needed
    'smtp_port' => 587,
    'smtp_username' => 'your-ses-smtp-username',
    'smtp_password' => 'your-ses-smtp-password',
    'smtp_encryption' => 'tls',
    'from_email' => 'noreply@worldpublicationawards.org',
    'from_name' => 'World Publications Awards',
    'reply_to' => 'noreply@worldpublicationawards.org'
];
*/

// Mailgun SMTP
/*
$mailer_config = [
    'smtp_host' => 'smtp.mailgun.org',
    'smtp_port' => 587,
    'smtp_username' => 'your-mailgun-smtp-username',
    'smtp_password' => 'your-mailgun-smtp-password',
    'smtp_encryption' => 'tls',
    'from_email' => 'noreply@worldpublicationawards.org',
    'from_name' => 'World Publications Awards',
    'reply_to' => 'noreply@worldpublicationawards.org'
];
*/

// Test the configuration
function testMailerConfig($config) {
    echo "<h2>Testing PHPMailer Configuration</h2>";
    
    echo "<h3>Current Configuration:</h3>";
    echo "SMTP Host: " . $config['smtp_host'] . "<br>";
    echo "SMTP Port: " . $config['smtp_port'] . "<br>";
    echo "SMTP Encryption: " . $config['smtp_encryption'] . "<br>";
    echo "From Email: " . $config['from_email'] . "<br>";
    echo "From Name: " . $config['from_name'] . "<br>";
    
    // Test email
    $test_email = 'test@worldpublicationawards.org';
    $test_subject = 'Test Email from World Publications Awards';
    $test_message = '<h3>Test Email</h3><p>This is a test email to verify your PHPMailer configuration.</p>';
    
    echo "<h3>Sending Test Email:</h3>";
    echo "To: $test_email<br>";
    
    // Include PHPMailer
    require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/phpmailer/src/SMTP.php';
    require_once __DIR__ . '/phpmailer/src/Exception.php';
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $config['smtp_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['smtp_username'];
        $mail->Password   = $config['smtp_password'];
        $mail->SMTPSecure = $config['smtp_encryption'] === 'tls' ? 
                           PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS : 
                           ($config['smtp_encryption'] === 'ssl' ? 
                           PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : 
                           false);
        $mail->Port       = $config['smtp_port'];
        
        // Recipients
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($test_email);
        $mail->addReplyTo($config['reply_to'], $config['from_name']);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $test_subject;
        $mail->Body    = $test_message;
        $mail->AltBody = strip_tags($test_message);
        
        $result = $mail->send();
        
        if ($result) {
            echo "<span style='color: green;'>✓ Test email sent successfully via PHPMailer!</span><br>";
            echo "Check your email inbox for the test message.<br>";
        } else {
            echo "<span style='color: red;'>✗ Test email failed to send</span><br>";
            echo "Please check your SMTP configuration.<br>";
        }
        
    } catch (Exception $e) {
        echo "<span style='color: red;'>✗ PHPMailer Error: " . $e->getMessage() . "</span><br>";
        echo "Please verify your SMTP settings.<br>";
    }
}

// Run test if accessed directly
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    testMailerConfig($mailer_config);
}
?>