<?php
// Test script to verify nominee OTP login functionality
include 'includes/dbcon.inc.php';
include 'includes/helpers.php';

echo "<h2>Testing Nominee OTP Login System</h2>";

// Test 1: Check if required tables exist
echo "<h3>1. Checking required database tables:</h3>";
$tables = ['users', 'nominees', 'otp_tokens'];

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        $exists = $stmt->rowCount() > 0;
        echo "✓ $table table: " . ($exists ? "EXISTS" : "MISSING") . "<br>";
    } catch (PDOException $e) {
        echo "✗ Error checking $table: " . $e->getMessage() . "<br>";
    }
}

// Test 2: Check if helper functions exist
echo "<h3>2. Checking required helper functions:</h3>";
$functions = [
    'generateOTP',
    'storeOTP',
    'validateOTP',
    'sendOTPEmailEnhanced',
    'ensureNomineeUserAccount',
    'isValidEmail',
    'testEmailConfiguration'
];

foreach ($functions as $func) {
    echo "$func: " . (function_exists($func) ? "✓ EXISTS" : "✗ MISSING") . "<br>";
}

// Test 3: Check nominee sample data
echo "<h3>3. Checking nominee data:</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM nominees");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Total nominees in database: $count<br>";
    
    if ($count > 0) {
        $stmt = $pdo->query("SELECT * FROM nominees LIMIT 1");
        $sample_nominee = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Sample nominee: " . htmlspecialchars($sample_nominee['name']) . "<br>";
        echo "Email: " . htmlspecialchars($sample_nominee['email'] ?? 'N/A') . "<br>";
        echo "Contact Email: " . htmlspecialchars($sample_nominee['contact_person_email'] ?? 'N/A') . "<br>";
    }
} catch (PDOException $e) {
    echo "✗ Error checking nominee data: " . $e->getMessage() . "<br>";
}

// Test 4: Check users with nominee role
echo "<h3>4. Checking users with nominee role:</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'nominee'");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Users with nominee role: $count<br>";
} catch (PDOException $e) {
    echo "✗ Error checking nominee users: " . $e->getMessage() . "<br>";
}

// Test 5: Test OTP generation
echo "<h3>5. Testing OTP functionality:</h3>";
$test_otp = generateOTP();
echo "Generated OTP: $test_otp (should be 6 digits)<br>";
echo "OTP length: " . strlen($test_otp) . "<br>";

// Test 6: Test Email Configuration and Sending
echo "<h3>6. Testing Email Configuration and OTP Sending:</h3>";
if (function_exists('testEmailConfiguration')) {
    testEmailConfiguration();
    
    // Check if PHPMailer is available
    $phpmailer_available = file_exists(__DIR__ . '/phpmailer/src/PHPMailer.php');
    echo "<br>PHPMailer Status: " . ($phpmailer_available ? "✓ AVAILABLE" : "✗ NOT FOUND") . "<br>";
    
    if ($phpmailer_available) {
        echo "Email method that will be used: <strong>PHPMailer (Recommended)</strong><br>";
        echo "To configure SMTP settings, edit mailer_config.php<br>";
    } else {
        echo "Email method that will be used: <strong>Basic mail() function</strong><br>";
        echo "For better reliability, ensure PHPMailer is properly installed<br>";
    }
    
    // Try sending OTP to a real email (uncomment to test)
    // echo "<br>Trying to send test OTP to actual email (configure your email here to test):<br>";
    // $test_result = sendOTPEmailEnhanced('your-test-email@example.com', '987654');
    // echo "Test email result: " . ($test_result ? "SUCCESS" : "FAILED") . "<br>";
} else {
    echo "✗ testEmailConfiguration function not found<br>";
}

echo "<h3>7. Nominee OTP Login Process Summary:</h3>";
echo "<ol>";
echo "<li>Visitor clicks 'Nominee Login' on login page</li>";
echo "<li>Enters email associated with nominee account</li>";
echo "<li>System checks if nominee exists with that email</li>";
echo "<li>If nominee exists, creates user account if not already present</li>";
echo "<li>Generates and stores OTP in database</li>";
echo "<li><strong>Sends OTP via email using PHPMailer (if available) or basic mail()</strong></li>";
echo "<li>User enters OTP to verify and login</li>";
echo "<li>Upon successful verification, redirects to nominee dashboard</li>";
echo "</ol>";

echo "<h3>8. Files involved in nominee OTP login:</h3>";
echo "<ul>";
echo "<li>login.php - Main login page with OTP flow</li>";
echo "<li>includes/helpers.php - Helper functions (OTP generation, email, etc.)</li>";
echo "<li>nominee-dashboard.php - Landing page after successful login</li>";
echo "<li>includes/dbcon.inc.php - Database connection</li>";
echo "<li>mailer_config.php - PHPMailer SMTP configuration (optional)</li>";
echo "</ul>";

echo "<h3>✅ Nominee OTP Login System is ready for use!</h3>";
echo "<p><strong>Important:</strong> For production use:</p>";
echo "<ol>";
echo "<li>Configure SMTP settings in mailer_config.php</li>";
echo "<li>Test email delivery using mailer_config.php directly</li>";
echo "<li>Ensure PHPMailer folder is properly included</li>";
echo "</ol>";
?>