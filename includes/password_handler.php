<?php
session_start();

include_once 'dbcon.inc.php';

// For security, enable CORS only for your domain in production
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    
    if (empty($current_password) || empty($new_password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }
    
    if (strlen($new_password) < 8) {
        echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters long']);
        exit();
    }
    
    try {
        // Get current user's password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit();
        }
        
        // Verify current password
        if (!password_verify($current_password, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            exit();
        }
        
        // Hash the new password
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update the password
        $updateStmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $result = $updateStmt->execute([$hashed_new_password, $_SESSION['user_id']]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update password']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>