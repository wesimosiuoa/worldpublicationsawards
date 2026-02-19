<?php
// Check database structure to see if otp_tokens table exists
include 'includes/dbcon.inc.php';

try {
    echo "<pre>";
    // Check if otp_tokens table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'otp_tokens'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "✓ otp_tokens table exists\n";
        
        // Get table structure
        $stmt = $pdo->query("DESCRIBE otp_tokens");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "otp_tokens table structure:\n";
        foreach ($columns as $column) {
            echo "  - {$column['Field']} ({$column['Type']}, {$column['Null']}, {$column['Key']})\n";
        }
    } else {
        echo "✗ otp_tokens table does NOT exist\n";
    }
    
    // Check nominees table structure
    $stmt = $pdo->query("SHOW TABLES LIKE 'nominees'");
    $nomineesTableExists = $stmt->rowCount() > 0;
    
    if ($nomineesTableExists) {
        echo "\n✓ nominees table exists\n";
        
        // Get nominees table structure
        $stmt = $pdo->query("DESCRIBE nominees");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "nominees table structure:\n";
        foreach ($columns as $column) {
            echo "  - {$column['Field']} ({$column['Type']}, {$column['Null']}, {$column['Key']})\n";
        }
    } else {
        echo "\n✗ nominees table does NOT exist\n";
    }
    
    // Check users table structure
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $usersTableExists = $stmt->rowCount() > 0;
    
    if ($usersTableExists) {
        echo "\n✓ users table exists\n";
        
        // Get users table structure
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "users table structure:\n";
        foreach ($columns as $column) {
            echo "  - {$column['Field']} ({$column['Type']}, {$column['Null']}, {$column['Key']})\n";
        }
    } else {
        echo "\n✗ users table does NOT exist\n";
    }
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "<pre>Database error: " . $e->getMessage() . "</pre>\n";
}
?>