<?php
// Comprehensive Email Debugging Test
echo "<h2>📧 Email Debugging Test</h2>";

// Test 1: Check PHPMailer files
echo "<h3>1. PHPMailer Installation Check:</h3>";
$required_files = [
    'phpmailer/src/PHPMailer.php',
    'phpmailer/src/SMTP.php', 
    'phpmailer/src/Exception.php'
];

foreach ($required_files as $file) {
    $exists = file_exists($file);
    echo "$file: " . ($exists ? "✅ FOUND" : "❌ MISSING") . "<br>";
}

// Test 2: Test basic PHPMailer functionality
echo "<h3>2. PHPMailer Basic Functionality Test:</h3>";
if (file_exists('phpmailer/src/PHPMailer.php')) {
    require_once 'phpmailer/src/PHPMailer.php';
    require_once 'phpmailer/src/SMTP.php';
    require_once 'phpmailer/src/Exception.php';
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        echo "✅ PHPMailer class instantiated successfully<br>";
        
        // Test SMTP configuration
        echo "<h3>3. SMTP Configuration Test:</h3>";
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'wezimosiuoa@gmail.com';
        $mail->Password = 'lkjcjwukldudvpho';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        echo "Host: smtp.gmail.com<br>";
        echo "Port: 587<br>";
        echo "Username: wezimosiuoa@gmail.com<br>";
        echo "Encryption: TLS<br>";
        
        // Test connection
        echo "<h3>4. SMTP Connection Test:</h3>";
        $mail->SMTPDebug = 2; // Enable verbose debug output
        $mail->Debugoutput = function($str, $level) {
            echo "Debug [$level]: " . htmlspecialchars($str) . "<br>";
        };
        
        $mail->setFrom('wezimosiuoa@gmail.com', 'Test Sender');
        $mail->addAddress('wezimosiuoa@gmail.com');
        $mail->isHTML(true);
        $mail->Subject = 'Test Email from World Publications Awards';
        $mail->Body = '<h3>Test Success!</h3><p>If you receive this email, PHPMailer is working correctly.</p>';
        
        echo "Attempting to send test email...<br>";
        echo "<pre>";
        
        if ($mail->send()) {
            echo "</pre>";
            echo "✅ <strong>EMAIL SENT SUCCESSFULLY!</strong><br>";
            echo "Check your Gmail inbox for the test message.<br>";
        } else {
            echo "</pre>";
            echo "❌ <strong>EMAIL FAILED TO SEND</strong><br>";
        }
        
    } catch (Exception $e) {
        echo "❌ PHPMailer Error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ PHPMailer files not found. Cannot test email functionality.<br>";
}

// Test 5: Check server configuration
echo "<h3>5. Server Configuration Check:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "OpenSSL Extension: " . (extension_loaded('openssl') ? "✅ ENABLED" : "❌ DISABLED") . "<br>";
echo "Allow URL fopen: " . (ini_get('allow_url_fopen') ? "✅ ENABLED" : "❌ DISABLED") . "<br>";

// Test 6: Test with your actual sendOTPEmailEnhanced function
echo "<h3>6. Testing sendOTPEmailEnhanced Function:</h3>";
include 'includes/helpers.php';

if (function_exists('sendOTPEmailEnhanced')) {
    $test_result = sendOTPEmailEnhanced('wezimosiuoa@gmail.com', '123456');
    echo "sendOTPEmailEnhanced result: " . ($test_result ? "✅ SUCCESS" : "❌ FAILED") . "<br>";
    
    if (!$test_result) {
        echo "Check error log for detailed information.<br>";
    }
} else {
    echo "❌ sendOTPEmailEnhanced function not found<br>";
}

// Test 7: Common Issues Checklist
echo "<h3>7. Common Issues Checklist:</h3>";
echo "<ul>";
echo "<li>✅ Gmail account credentials correct</li>";
echo "<li>✅ Gmail 'Less secure app access' enabled OR App Password used</li>";
echo "<li>✅ Port 587 not blocked by firewall</li>";
echo "<li>✅ OpenSSL extension enabled in PHP</li>";
echo "<li>✅ PHPMailer files properly included</li>";
echo "</ul>";

echo "<h3>🔧 Troubleshooting Steps:</h3>";
echo "<ol>";
echo "<li>Check your Gmail account security settings</li>";
echo "<li>Verify the App Password is correct (not your regular password)</li>";
echo "<li>Check if your ISP/firewall blocks port 587</li>";
echo "<li>Review PHP error logs for detailed error messages</li>";
echo "<li>Try using SSL on port 465 instead of TLS on port 587</li>";
echo "</ol>";
?>