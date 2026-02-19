<?php
/**
 * Setup Script for Social Media Links Table
 * 
 * This script creates the nominees_social_media_links table
 * if it doesn't already exist.
 * 
 * Usage: Access this file directly in your browser or run it via command line
 * Important: Ensure this file is protected or deleted after setup
 */

// Start session and include database connection
session_start();
include 'includes/dbcon.inc.php';

// Only allow admin users or localhost
$is_localhost = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1', 'localhost']);
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if (!$is_localhost && !$is_admin) {
    die('Access denied. This setup script can only be accessed from localhost or by administrators.');
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup'])) {
    try {
        // Create the table
        $sql = "
        CREATE TABLE IF NOT EXISTS `nominees_social_media_links` (
          `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `nominee_id` INT(11) NOT NULL,
          `platform_name` VARCHAR(100) NOT NULL COMMENT 'e.g., YouTube, Twitter, Facebook, Instagram, LinkedIn, TikTok, Website, Blog',
          `link` VARCHAR(500) NOT NULL COMMENT 'The full URL of the social media profile',
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          
          CONSTRAINT `fk_nominees_social_media_nominee_id` 
            FOREIGN KEY (`nominee_id`) 
            REFERENCES `nominees` (`id`) 
            ON DELETE CASCADE 
            ON UPDATE CASCADE,
          
          UNIQUE KEY `uk_nominee_platform` (`nominee_id`, `platform_name`),
          
          KEY `idx_nominee_id` (`nominee_id`),
          KEY `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $pdo->exec($sql);
        $message = 'Social media links table created successfully!';
        $message_type = 'success';
        
    } catch(PDOException $e) {
        $message = 'Error creating table: ' . htmlspecialchars($e->getMessage());
        $message_type = 'danger';
    }
}

// Check if table already exists
$table_exists = false;
try {
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'nominees_social_media_links'");
    $stmt->execute();
    $table_exists = $stmt->rowCount() > 0;
} catch(Exception $e) {
    // Continue
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Social Media Links Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-cog me-2"></i>Social Media Links Table Setup</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-4">
                            <h5><i class="fas fa-info-circle me-2"></i>Current Status</h5>
                            <?php if ($table_exists): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Table already exists!</strong> 
                                    The nominees_social_media_links table is ready to use.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Table does not exist yet.</strong> 
                                    Click the button below to create it.
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <h5><i class="fas fa-database me-2"></i>Table Details</h5>
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Table Name:</strong> nominees_social_media_links</li>
                                <li class="list-group-item"><strong>Columns:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>id (Primary Key, Auto Increment)</li>
                                        <li>nominee_id (Foreign Key to nominees.id)</li>
                                        <li>platform_name (VARCHAR 100)</li>
                                        <li>link (VARCHAR 500)</li>
                                        <li>created_at (TIMESTAMP)</li>
                                        <li>updated_at (TIMESTAMP)</li>
                                    </ul>
                                </li>
                                <li class="list-group-item"><strong>Constraints:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>Foreign Key: nominee_id → nominees.id (CASCADE DELETE/UPDATE)</li>
                                        <li>Unique Index: (nominee_id, platform_name)</li>
                                    </ul>
                                </li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <h5><i class="fas fa-shield-alt me-2"></i>Security Features</h5>
                            <div class="alert alert-info">
                                <ul class="mb-0">
                                    <li><strong>CSRF Protection:</strong> All forms include CSRF token validation</li>
                                    <li><strong>Session Verification:</strong> User must be logged in as nominee</li>
                                    <li><strong>Input Sanitization:</strong> All inputs are sanitized with htmlspecialchars()</li>
                                    <li><strong>URL Validation:</strong> Links are validated using FILTER_VALIDATE_URL</li>
                                    <li><strong>Ownership Verification:</strong> Users can only manage their own links</li>
                                    <li><strong>Prepared Statements:</strong> All queries use parameterized statements (PDO)</li>
                                    <li><strong>Authorization Check:</strong> Only nominees can access the form</li>
                                </ul>
                            </div>
                        </div>

                        <?php if (!$table_exists): ?>
                            <form method="POST" action="">
                                <button type="submit" name="setup" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-plus me-2"></i>Create Social Media Links Table
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-success">
                                The table is ready! You can now use the social media links feature in the nominee profile.
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-muted">
                        <small>
                            <i class="fas fa-lock me-1"></i>
                            This page can only be accessed from localhost or by authenticated administrators.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
