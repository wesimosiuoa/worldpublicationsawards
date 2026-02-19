<?php
include 'includes/dbcon.inc.php';

try {
    // Create users table with roles
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        role ENUM('admin', 'stakeholder', 'user') DEFAULT 'user',
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "Users table with roles created successfully\n";
    
    // Insert a default admin user
    $default_username = 'admin';
    $default_password = password_hash('admin123', PASSWORD_DEFAULT);
    $default_email = 'admin@worldpublicationawards.com';
    
    // Check if admin user already exists
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $checkStmt->execute([$default_username]);
    $existingUser = $checkStmt->fetch();
    
    if (!$existingUser) {
        // Insert the default admin user
        $userStmt = $pdo->prepare("INSERT INTO users (username, email, password, first_name, last_name, role, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $userStmt->execute([$default_username, $default_email, $default_password, 'System', 'Administrator', 'admin', 1]);
        
        echo "Default admin user created successfully\n";
        echo "Username: $default_username\n";
        echo "Password: admin123\n";
    } else {
        echo "Admin user already exists\n";
    }
    
    echo "Users table with roles setup completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage() . "\n";
}
?>