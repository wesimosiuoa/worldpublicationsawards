<?php

/* =========================
   🔒 SECURITY HEADERS
========================= */
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: no-referrer");

/* =========================
   🔒 FORCE HTTPS
========================= */
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
    header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
}

/* =========================
   🔒 SECURE SESSION SETTINGS
========================= */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');

session_start();

include_once 'dbcon.inc.php';

header('Content-Type: application/json');

/* =========================
   🔒 CSRF VALIDATION
   (Make sure token is generated in login form page)
========================= */
if (!isset($_POST['csrf_token']) || 
    !isset($_SESSION['csrf_token']) || 
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    
    showErrorMessage('Invalid CSRF token', 'Error', 'Login Failed');
    exit;
}

/* =========================
   🔒 REQUEST METHOD CHECK
========================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    showErrorMessage('Invalid request method', 'Error', 'Login Failed');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    showErrorMessage('Invalid credentials or account inactive', 'Error', 'Login Failed');
    exit;
}

try {

    /* =========================
       🔒 FETCH USER
    ========================= */
    $stmt = $pdo->prepare("
        SELECT id, username, password, role, is_active, 
               failed_attempts, lock_until
        FROM users 
        WHERE username = ? OR email = ?
        LIMIT 1
    ");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    /* =========================
       🔒 TIMING ATTACK PROTECTION
    ========================= */
    $dummyHash = '$2y$10$abcdefghijklmnopqrstuv1234567890123456789012';

    if ($user) {
        $validPassword = password_verify($password, $user['password']);
    } else {
        password_verify($password, $dummyHash);
        $validPassword = false;
    }

    /* =========================
       🔒 ACCOUNT LOCK CHECK
    ========================= */
    if ($user && $user['lock_until'] && strtotime($user['lock_until']) > time()) {
        showErrorMessage('Account temporarily locked. Try again later.', 'Error', 'Login Failed');
        exit;
    }

    /* =========================
       ✅ SUCCESSFUL LOGIN
    ========================= */
    if ($user && $validPassword && $user['is_active']) {

        // Reset failed attempts
        $pdo->prepare("UPDATE users SET failed_attempts = 0, lock_until = NULL WHERE id = ?")
            ->execute([$user['id']]);

        // 🔐 Prevent session fixation
        session_regenerate_id(true);

        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Update last login
        $pdo->prepare("UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = ?")
            ->execute([$user['id']]);

        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]
        ]);

        exit;
    }

    /* =========================
       ❌ FAILED LOGIN HANDLING
    ========================= */
    if ($user) {
        $failedAttempts = $user['failed_attempts'] + 1;

        if ($failedAttempts >= 5) {
            $lockUntil = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            $pdo->prepare("
                UPDATE users 
                SET failed_attempts = ?, lock_until = ?
                WHERE id = ?
            ")->execute([$failedAttempts, $lockUntil, $user['id']]);
        } else {
            $pdo->prepare("
                UPDATE users 
                SET failed_attempts = ?
                WHERE id = ?
            ")->execute([$failedAttempts, $user['id']]);
        }
    }

    /* =========================
       🔒 LOG FAILED ATTEMPT
       (Create this table if not exists)
    ========================= */
    $pdo->prepare("
        INSERT INTO login_attempts 
        (username_attempted, ip_address, user_agent, created_at)
        VALUES (?, ?, ?, NOW())
    ")->execute([
        $username,
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);

    showErrorMessage('Invalid credentials or account inactive', 'Error', 'Login Failed');

} catch (PDOException $e) {

    showWarningMessage('Database error occurred', 'Warning', 'Login Failed');
}
?>