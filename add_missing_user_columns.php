<?php
// Add missing columns to users table
include 'includes/dbcon.inc.php';

try {
    // Check if first_name column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'first_name'");
    if ($stmt->rowCount() == 0) {
        echo "Adding first_name column... ";
        $pdo->exec("ALTER TABLE users ADD COLUMN first_name VARCHAR(50) NOT NULL DEFAULT '' AFTER email");
        echo "Done.<br>";
    } else {
        echo "first_name column already exists.<br>";
    }
    
    // Check if last_name column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_name'");
    if ($stmt->rowCount() == 0) {
        echo "Adding last_name column... ";
        $pdo->exec("ALTER TABLE users ADD COLUMN last_name VARCHAR(50) NOT NULL DEFAULT '' AFTER first_name");
        echo "Done.<br>";
    } else {
        echo "last_name column already exists.<br>";
    }
    
    // Update existing users to have default names
    echo "Updating existing users with default names... ";
    $pdo->exec("UPDATE users SET first_name = 'User', last_name = 'Account' WHERE first_name = '' OR last_name = '' OR first_name IS NULL OR last_name IS NULL");
    echo "Done.<br>";
    
    echo "<h3 style='color: green;'>All missing columns added successfully!</h3>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>