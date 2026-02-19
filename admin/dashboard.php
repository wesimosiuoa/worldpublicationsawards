<?php
// session_start();

// Check if user is logged in
// if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
//     header('Location: ../index.php');
//     exit();
// }

// // Check if user has admin or stakeholder role
// $user_role = $_SESSION['role'] ?? '';
// if ($user_role !== 'admin' && $user_role !== 'stakeholder') {
//     header('Location: ../index.php');
//     exit();
// }

// include '../includes/dbcon.inc.php';
// include '../includes/messages.php';
include 'header.php'; 
// Get statistics for dashboard
$totalNominees = $pdo->query("SELECT COUNT(*) FROM nominees WHERE is_active = 1")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories WHERE is_active = 1")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalVotes = $pdo->query("SELECT COUNT(*) FROM votes")->fetchColumn() ?: 0;
$totalAmountRaised = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM votes")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - World Publications Awards</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="../assets/css/custom.css" rel="stylesheet">
</head>
<body>


<div style="margin-top:80px;"></div>

<!-- ADMIN DASHBOARD CONTENT -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- STATS CARDS -->
            <div class="col-md-3">
                <div class="card text-center bg-primary text-white">
                    <div class="card-body">
                        <h3 class="fw-bold"><?php echo $totalNominees; ?></h3>
                        <p class="mb-0">Active Nominees</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <h3 class="fw-bold"><?php echo $totalCategories; ?></h3>
                        <p class="mb-0">Active Categories</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-center bg-info text-white">
                    <div class="card-body">
                        <h3 class="fw-bold"><?php echo $totalUsers; ?></h3>
                        <p class="mb-0">Users</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-center bg-warning text-dark">
                    <div class="card-body">
                        <h3 class="fw-bold"><?php echo $totalVotes; ?></h3>
                        <p class="mb-0">Total Votes</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-center bg-secondary text-white">
                    <div class="card-body">
                        <h3 class="fw-bold">$<?php echo number_format($totalAmountRaised, 2); ?></h3>
                        <p class="mb-0">Total Amount Raised</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-secondary text-white">
                    <div class="card-body">
                        <h3 class="fw-bold"><?php echo $totalBlogs = $pdo->query("SELECT COUNT(*) FROM 	blog_posts")->fetchColumn() ?: 0  ; ?></h3>
                        <p class="mb-0">Blogs</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ADMIN ACTIONS -->
        <div class="row mt-5">
            <div class="col-12">
                <h3>Admin Actions</h3>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card h-100 text-center">
                            <div class="card-body">
                                <h5 class="card-title">Manage Nominees</h5>
                                <p class="card-text">Add, edit, or remove nominees</p>
                                <a href="nominees.php" class="btn btn-primary">Manage</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card h-100 text-center">
                            <div class="card-body">
                                <h5 class="card-title">Manage Categories</h5>
                                <p class="card-text">Add, edit, or remove categories</p>
                                <a href="categories.php" class="btn btn-primary">Manage</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card h-100 text-center">
                            <div class="card-body">
                                <h5 class="card-title">View Reports</h5>
                                <p class="card-text">View voting reports and analytics</p>
                                <a href="reports.php" class="btn btn-primary">View</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card h-100 text-center">
                            <div class="card-body">
                                <h5 class="card-title">Manage Users</h5>
                                <p class="card-text">View and manage user accounts</p>
                                <a href="users.php" class="btn btn-primary">Manage</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 text-center">
                            <div class="card-body">
                                <h5 class="card-title">Manage Blogs</h5>
                                <p class="card-text">Add, edit, or remove blogs</p>
                                <a href="blog.php" class="btn btn-primary">Manage</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5>World Publications Awards</h5>
                <p class="mb-0">Recognizing excellence in global journalism.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> World Publications Awards. All rights reserved.</p>
                <p class="mb-0">Admin Panel</p>
            </div>
        </div>
    </div>
</footer>

</body>
</html>