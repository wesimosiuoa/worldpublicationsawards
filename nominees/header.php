<?php
// Nominee Dashboard for World Publications Awards
session_start();
include '../includes/dbcon.inc.php';
//include '../includes/helpers.php';
// Check if user is logged in and is a nominee


$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$email = $_SESSION['email'] ?? '';
$role = $_SESSION['role'];


echo "Welcome, $username! Your email: $email, Role: $role, User ID: $user_id";

// Get nominee information based on user's email
$stmt = $pdo->prepare("SELECT * FROM nominees WHERE email = ? OR contact_person_email = ?");
$stmt->execute([$email, $email]);
$nominee = $stmt->fetch(PDO::FETCH_ASSOC);

// Get nominee's votes and stats if nominee exists
$nominee_stats = null;
if ($nominee) {
    $stmt = $pdo->prepare("
        SELECT 
            n.*,
            c.name as category_name,
            co.name as country_name,
            COUNT(v.id) as vote_count,
            COALESCE(SUM(v.amount), 0) as total_raised
        FROM nominees n
        LEFT JOIN categories c ON n.category_id = c.id
        LEFT JOIN countries co ON n.country_id = co.id
        LEFT JOIN votes v ON n.id = v.nominee_id
        WHERE n.id = ?
        GROUP BY n.id
    ");
    $stmt->execute([$nominee['id']]);
    $nominee_stats = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nominee Dashboard - World Publications Awards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>

<!-- Header -->
    <header class="bg-dark text-white py-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0">
                    <a href="nominee-dashboard.php" class="text-white text-decoration-none">
                        <i class="fas fa-trophy me-2"></i>World Publications Awards
                    </a>
                </h1>
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" id="navbarDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($username); ?>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle me-2"></i>Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../includes/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>