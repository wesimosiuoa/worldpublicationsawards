<?php
session_start();

include_once 'dbcon.inc.php';

// For security, enable CORS only for your domain in production
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    try {
        // Query the database for the user
        $stmt = $pdo->prepare("SELECT id, username, password, role, is_active FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if user exists and password is correct
        if ($user && password_verify($password, $user['password']) && $user['is_active']) {
            
            // Set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Update last login time
            $updateStmt = $pdo->prepare("UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ]
            ]);
        } else {
            //echo json_encode(['success' => false, 'message' => 'Invalid credentials or account inactive']);
            showErrorMessage('Invalid credentials or account inactive', 'Error', 'Login Failed' );
        }
    } catch (PDOException $e) {
        // echo json_encode(['success' => false, 'message' => 'Database error occurred']);
        showWarningMessage('Database error occurred', 'Warning', 'Login Failed' );
    }
} else {
    // echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    showErrorMessage('Invalid request method', 'Error', 'Login Failed' );
}
?>