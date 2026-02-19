<?php
/**
 * Helper functions for World Publications Awards
 */

// Function to get country name by ID
if (!function_exists('getCountryName')) {
function getCountryName($country_id) {
    global $pdo;
    
    if (!$pdo) {
        return 'Unknown';
    }
    
    try {
        $stmt = $pdo->prepare("SELECT name FROM countries WHERE id = ?");
        $stmt->execute([$country_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['name'] : 'Unknown';
    } catch (PDOException $e) {
        return 'Unknown';
    }
}
}

// Function to get country name by ISO code
if (!function_exists('getCountryNameByISO')) {
function getCountryNameByISO(?string $isoCode): string
{
    global $pdo;
    
    if (empty($isoCode) || !$pdo) {
        return 'Unknown';
    }
    
    try {
        $stmt = $pdo->prepare("SELECT name FROM countries WHERE iso_code = ?");
        $stmt->execute([strtoupper($isoCode)]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['name'] : 'Unknown';
    } catch (PDOException $e) {
        return 'Unknown';
    }
}
}

// Function to get country flag by ID (using local flags)
if (!function_exists('getCountryFlag')) {
function getCountryFlag(?string $isoCode, int $size = 24): string
{
    if (empty($isoCode)) {
        return ''; // fail silently, no fatal error
    }

    $isoCode = strtolower($isoCode);

    // Ensure proper format (lowercase, 2 letters)
    $isoCode = preg_replace('/[^a-z]/', '', $isoCode);
    if (strlen($isoCode) !== 2) {
        return ''; // Invalid ISO code
    }

    // Check if local flag exists, otherwise fallback to CDN
    $localFlagPath = "assets/flags/" . $isoCode . ".png";
    
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $localFlagPath)) {
        // Use local flag
        return '<img 
            src="' . $localFlagPath . '" 
            alt="' . strtoupper($isoCode) . ' flag"
            loading="lazy"
            style="width:' . $size . 'px; height:auto; vertical-align:middle; margin-right:6px;"
        >';
    } else {
        // Fallback to CDN if local flag doesn't exist
        return '<img 
            src="https://flagcdn.com/w' . $size . '/' . $isoCode . '.png"
            alt="' . strtoupper($isoCode) . ' flag"
            loading="lazy"
            style="width:' . $size . 'px; height:auto; vertical-align:middle; margin-right:6px;"
        >';
    }
}
}




// Function to convert country code to flag emoji
if (!function_exists('getFlagEmoji')) {
function getFlagEmoji($countryCode) {
    $countryCode = strtoupper($countryCode);
    $base = ord('A') - 1;
    $offset = ord($countryCode[0]) + ord($countryCode[1]) - $base * 2;
    $char1 = 0x1F1A5 + ord($countryCode[0]) - $base;
    $char2 = 0x1F1A5 + ord($countryCode[1]) - $base;
    
    return html_entity_decode('&#' . $char1 . '&#' . $char2 . ';');
}
}

// Function to get all active countries with their flags
if (!function_exists('getAllCountriesWithFlags')) {
function getAllCountriesWithFlags(): array
{
    global $pdo;
    
    if (!$pdo) {
        return [];
    }
    
    try {
        $sql = "
            SELECT name, iso_code
            FROM countries
            WHERE is_active = 1
            ORDER BY name ASC
        ";
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
}

// Enhanced function to render flag with more options
if (!function_exists('renderFlag')) {
function renderFlag(?string $isoCode, int $size = 40): string
{
    if (empty($isoCode)) {
        return '';
    }

    $isoCode = strtolower($isoCode);

    return '<img 
        src="https://flagcdn.com/w' . $size . '/' . $isoCode . '.png"
        alt="' . strtoupper($isoCode) . ' flag"
        loading="lazy"
        style="width:' . $size . 'px; height:auto; vertical-align:middle; margin-right:10px;"
    >';
}
}

// Function to validate email
if (!function_exists('isValidEmail')) {
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
}

// Function to sanitize input
if (!function_exists('sanitizeInput')) {
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}
}

// Function to generate slug
if (!function_exists('generateSlug')) {
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = trim(preg_replace('/[\s-]+/', '-', $text));
    return $text;
}
}

// Function to format large numbers
if (!function_exists('formatNumber')) {
function formatNumber($number) {
    if ($number >= 1000000) {
        return round($number / 1000000, 1) . 'M';
    } elseif ($number >= 1000) {
        return round($number / 1000, 1) . 'K';
    }
    return $number;
}
}

// Function to get user role
if (!function_exists('getUserRole')) {
function getUserRole($user_id) {
    global $pdo;
    
    if (!$pdo) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['role'] : false;
    } catch (PDOException $e) {
        return false;
    }
}
}

// Function to check if user is admin
if (!function_exists('isAdmin')) {
function isAdmin($user_id) {
    $role = getUserRole($user_id);
    return $role === 'admin';
}
}

// Function to encrypt data
if (!function_exists('encryptData')) {
function encryptData($data, $key = null) {
    if (!$key) {
        $key = defined('ENCRYPTION_KEY') ? constant('ENCRYPTION_KEY') : 'your_default_encryption_key_here';
    }
    
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    
    return base64_encode($iv . $encrypted);
}
}

// Function to decrypt data
if (!function_exists('decryptData')) {
function decryptData($encryptedData, $key = null) {
    if (!$key) {
        $key = defined('ENCRYPTION_KEY') ? constant('ENCRYPTION_KEY') : 'your_default_encryption_key_here';
    }
    
    $data = base64_decode($encryptedData);
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($data, 0, $ivLength);
    $encrypted = substr($data, $ivLength);
    
    return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
}
}

// Function to validate IP address
if (!function_exists('isValidIP')) {
function isValidIP($ip) {
    return filter_var($ip, FILTER_VALIDATE_IP) !== false;
}
}

// Function to get client IP address
if (!function_exists('getClientIP')) {
function getClientIP() {
    $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
}

// Function to validate URL
if (!function_exists('isValidUrl')) {
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}
}

// Function to get top nominees by votes
if (!function_exists('getTopNominees')) {
function getTopNominees($limit = 10) {
    global $pdo;
    
    if (!$pdo) {
        return [];
    }
    
    try {
        $sql = "SELECT n.*, c.name as category_name, co.name as country_name 
                FROM nominees n 
                LEFT JOIN categories c ON n.category_id = c.id 
                LEFT JOIN countries co ON n.country_id = co.id 
                ORDER BY n.total_votes DESC 
                LIMIT ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
}

// Function to get nominees by category
if (!function_exists('getNomineesByCategory')) {
function getNomineesByCategory($categoryId) {
    global $pdo;
    
    if (!$pdo) {
        return [];
    }
    
    try {
        $sql = "SELECT n.*, c.name as category_name, co.name as country_name 
                FROM nominees n 
                LEFT JOIN categories c ON n.category_id = c.id 
                LEFT JOIN countries co ON n.country_id = co.id 
                WHERE n.category_id = ?
                ORDER BY n.total_votes DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
}

// Function to get winners by category (top 3 nominees by votes)
if (!function_exists('getCategoryWinners')) {
function getCategoryWinners($categoryId) {
    global $pdo;
    
    if (!$pdo) {
        return [];
    }
    
    try {
        $sql = "SELECT n.*, c.name as category_name, co.name as country_name 
                FROM nominees n 
                LEFT JOIN categories c ON n.category_id = c.id 
                LEFT JOIN countries co ON n.country_id = co.id 
                WHERE n.category_id = ? AND n.is_active = 1
                ORDER BY n.total_votes DESC
                LIMIT 3";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
}

// Function to truncate text
if (!function_exists('truncateText')) {
function truncateText($text, $length = 100) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}
}

// Function to generate OTP code
if (!function_exists('generateOTP')) {
function generateOTP() {
    return rand(100000, 999999); // 6-digit OTP
}
}

// Function to send OTP via email (using mail() function as fallback)
if (!function_exists('sendOTPEmail')) {
function sendOTPEmail($email, $otp) {
    $subject = 'Your OTP for World Publications Awards Login';
    $message = "
        <html>
        <body>
            <h2>Your OTP for World Publications Awards</h2>
            <p>Hello,</p>
            <p>Your one-time password (OTP) for accessing the nominee portal is: <strong>{$otp}</strong></p>
            <p>Please use this code to complete your login. The code is valid for 10 minutes.</p>
            <p>If you didn't request this login, please ignore this email.</p>
            <br>
            <p>Best regards,<br>World Publications Awards Team</p>
        </body>
        </html>
    ";
    
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: noreply@worldpublicationawards.org',
        'Reply-To: noreply@worldpublicationawards.org'
    ];
    
    // In development environments, suppress mail errors and return true
    // In production, you would want to configure a proper SMTP server
    $result = @mail($email, $subject, $message, implode("\r\n", $headers));
    
    // For development, return true regardless of actual delivery
    // In production, return the actual result
    return $result !== false;
}
}

// Function to send email using PHPMailer (recommended for production)
// Note: This requires PHPMailer to be installed via Composer
// For development/testing, the basic mail() function is used

// Enhanced function to send OTP via email using PHPMailer exclusively
if (!function_exists('sendOTPEmailEnhanced')) {
function sendOTPEmailEnhanced($email, $otp) {
    $subject = 'Your OTP for World Publications Awards Login';
    $message = "
        <html>
        <body>
            <h2>Your OTP for World Publications Awards</h2>
            <p>Hello,</p>
            <p>Your one-time password (OTP) for accessing the nominee portal is: <strong>{$otp}</strong></p>
            <p>Please use this code to complete your login. The code is valid for 10 minutes.</p>
            <p>If you didn't request this login, please ignore this email.</p>
            <br>
            <p>Best regards,<br>World Publications Awards Team</p>
        </body>
        </html>
    ";
    
    // Use PHPMailer exclusively
    if (file_exists(__DIR__ . '/../phpmailer/src/PHPMailer.php')) {
        try {
            // Include PHPMailer classes
            require_once __DIR__ . '/../phpmailer/src/PHPMailer.php';
            require_once __DIR__ . '/../phpmailer/src/SMTP.php';
            require_once __DIR__ . '/../phpmailer/src/Exception.php';
            
            // Try SSL on port 465 first (more reliable)
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Server settings - SSL on port 465
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'wezimosiuoa@gmail.com';
            $mail->Password = 'lkjcjwukldudvpho';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS; // SSL
            $mail->Port = 465;
            
            // Enable verbose debugging
            $mail->SMTPDebug = 0; // Set to 2 for detailed debug info
            $mail->Debugoutput = 'error_log';
            
            // Recipients
            $mail->setFrom('wezimosiuoa@gmail.com', 'World Publications Awards');
            $mail->addAddress($email);
            $mail->addReplyTo('wezimosiuoa@gmail.com', 'World Publications Awards');
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->AltBody = strip_tags($message);
            
            $result = $mail->send();
            error_log("OTP Email sent via PHPMailer (SSL) to: $email, Result: " . ($result ? 'SUCCESS' : 'FAILED'));
            return $result;
            
        } catch (Exception $e) {
            error_log("PHPMailer SSL Error: " . $e->getMessage());
            
            // Try TLS on port 587 as fallback
            try {
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                
                // Server settings - TLS on port 587
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'wezimosiuoa@gmail.com';
                $mail->Password = 'lkjcjwukldudvpho';
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; // TLS
                $mail->Port = 587;
                
                // Enable verbose debugging
                $mail->SMTPDebug = 0;
                $mail->Debugoutput = 'error_log';
                
                // Recipients
                $mail->setFrom('wezimosiuoa@gmail.com', 'World Publications Awards');
                $mail->addAddress($email);
                $mail->addReplyTo('wezimosiuoa@gmail.com', 'World Publications Awards');
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $message;
                $mail->AltBody = strip_tags($message);
                
                $result = $mail->send();
                error_log("OTP Email sent via PHPMailer (TLS) to: $email, Result: " . ($result ? 'SUCCESS' : 'FAILED'));
                return $result;
                
            } catch (Exception $e2) {
                error_log("PHPMailer TLS Error: " . $e2->getMessage());
                return false;
            }
        }
    } else {
        error_log("PHPMailer not found, cannot send email");
        return false;
    }
}
}

// Function to test email configuration
if (!function_exists('testEmailConfiguration')) {
function testEmailConfiguration() {
    $test_email = 'test@worldpublicationawards.org';
    $test_otp = '123456';
    
    echo "<h3>Testing Email Configuration:</h3>";
    echo "Sending test OTP to: $test_email<br>";
    
    $result = sendOTPEmailEnhanced($test_email, $test_otp);
    
    if ($result) {
        echo "<span style='color: green;'>✓ Email sent successfully</span><br>";
        echo "Email method used: <strong>PHPMailer with Gmail SMTP</strong><br>";
    } else {
        echo "<span style='color: red;'>✗ Email sending failed</span><br>";
        echo "Please check your PHPMailer configuration and Gmail SMTP settings.<br>";
    }
    
    return $result;
}
}

// Function to store OTP token
if (!function_exists('storeOTP')) {
    function storeOTP($email, $otp) {
        global $pdo;

        if (!$pdo) {
            return false;
        }

        try {
            // Always work in UTC
            $nowUtc = new DateTime('now', new DateTimeZone('UTC'));
            $expiresUtc = (clone $nowUtc)->modify('+10 minutes')->format('Y-m-d H:i:s');

            // Delete any existing OTPs for this email
            $deleteStmt = $pdo->prepare(
                "DELETE FROM otp_tokens WHERE email = ?"
            );
            $deleteStmt->execute([$email]);

            // Insert new OTP with UTC expiry
            $insertStmt = $pdo->prepare(
                "INSERT INTO otp_tokens (email, otp, expires_at)
                 VALUES (?, ?, ?)"
            );

            return $insertStmt->execute([
                $email,
                $otp,
                $expiresUtc
            ]);

        } catch (PDOException $e) {
            return false;
        }
    }
}


// Function to validate OTP
if (!function_exists('validateOTP')) {
    function validateOTP($email, $otp) {
        global $pdo;

        if (!$pdo) {
            return false;
        }

        try {
            // Use UTC for validation
            $stmt = $pdo->prepare(
                "SELECT id 
                 FROM otp_tokens 
                 WHERE email = ? 
                   AND otp = ? 
                   AND expires_at > UTC_TIMESTAMP()
                   AND used = 0
                 LIMIT 1"
            );

            $stmt->execute([$email, $otp]);
            $otpId = $stmt->fetchColumn();

            if ($otpId) {
                // Mark OTP as used (UTC timestamp)
                $updateStmt = $pdo->prepare(
                    "UPDATE otp_tokens 
                     SET used = 1, used_at = UTC_TIMESTAMP() 
                     WHERE id = ?"
                );
                $updateStmt->execute([$otpId]);

                return true;
            }

            return false;

        } catch (PDOException $e) {
            return false;
        }
    }
}


// Function to ensure nominee has a user account
if (!function_exists('ensureNomineeUserAccount')) {
function ensureNomineeUserAccount($email) {
    global $pdo;
    
    if (!$pdo) {
        return false;
    }
    
    try {
        // Check if user already exists with this email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            return $user['id'];
        }
        
        // Check if this email exists in nominees table
        $stmt = $pdo->prepare("SELECT * FROM nominees WHERE email = ? OR contact_person_email = ?");
        $stmt->execute([$email, $email]);
        $nominee = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$nominee) {
            return false;
        }
        
        // Create a user account for the nominee
        $username = explode('@', $email)[0]; // Use part before @ as username
        $password = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT); // Auto-generated secure password
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'nominee')");
        $result = $stmt->execute([$username, $email, $password]);
        
        if ($result) {
            return $pdo->lastInsertId();
        }
        
        return false;
    } catch (PDOException $e) {
        return false;
    }
}}
?>