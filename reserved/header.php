<?php
// This header.php file sets up necessary includes and session data
// and outputs the HTML header and navigation.

session_start();

// Include necessary files
if (file_exists('includes/dbcon.inc.php')) {
    include 'includes/dbcon.inc.php';
}
if (file_exists('includes/helpers.php')) {
    include 'includes/helpers.php';
}
if (file_exists('includes/messages.php')) {
    include 'includes/messages.php';
}

// Get user role if logged in
$user_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$user_role = $user_logged_in ? ($_SESSION['role'] ?? '') : '';

// Set page title if not already set
if (!isset($page_title)) {
    $page_title = 'World Publications Awards';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'World Publications Awards'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/custom.css" rel="stylesheet">
    
    <!-- Popup Styles -->
    <?php addPopupStyles(); ?>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-trophy me-2"></i>World Publications Awards
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#categories">Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#nominees">Nominees</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#top-voted">Top Voted</a>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                <?php if ($user_logged_in): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($user_role === 'admin' || $user_role === 'stakeholder'): ?>
                                <li><a class="dropdown-item" href="admin/dashboard.php"><i class="fas fa-cogs me-2"></i>Admin Panel</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="includes/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Flash messages and popup messages -->
<?php 
displayFlashMessage();
displayPopupMessage();
?>
</body>
</html>