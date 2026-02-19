<?php
// This header.php file sets up necessary includes and session data
// and outputs the HTML header and navigation for the admin panel.

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Include necessary files
if (file_exists('../includes/dbcon.inc.php')) {
    include '../includes/dbcon.inc.php';
}
if (file_exists('../includes/helpers.php')) {
    include '../includes/helpers.php';
}
if (file_exists('../includes/messages.php')) {
    include '../includes/messages.php';
}

// Get user role if logged in
$user_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$user_role = $user_logged_in ? ($_SESSION['role'] ?? '') : '';
$user_id = $_SESSION['user_id'] ?? null;
$user_username = $_SESSION['username'] ?? '';

// Set page title if not already set
if (!isset($page_title)) {
    $page_title = 'Admin Dashboard - World Publications Awards';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Admin Dashboard - World Publications Awards'; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Admin CSS -->
    <link href="../assets/css/custom.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            Admin Dashboard
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#adminNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="nominees.php">Manage Nominees</a></li>
                <li class="nav-item"><a class="nav-link" href="categories.php">Manage Categories</a></li>
                <li class="nav-item"><a class="nav-link" href="reports.php">Reports</a></li>
                <li class="nav-item"><a class="nav-link" href="users.php">Manage Users</a></li>
                <li class="nav-item"><a class="nav-link" href="traffic.php">Traffic Analytics</a></li>
                <!-- <li class="nav-item"><a class="nav-link" href="web-traffic/traffic.php">Traffic Analytics</a></li> -->
                <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
            </ul>
            
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <span class="badge bg-warning text-dark"><?php echo ucfirst(htmlspecialchars($user_role)); ?></span>
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../includes/logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div style="margin-top:80px;"></div>

    <!-- Flash messages -->
    <?php displayFlashMessage(); ?>
</body>
</html>