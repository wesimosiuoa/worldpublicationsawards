<?php
// Registration Page for World Publications Awards
session_start();

// If user is already logged in, redirect to appropriate page
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $user_role = $_SESSION['role'] ?? 'user';
    if ($user_role === 'admin') {
        header('Location: admin/dashboard.php');
        exit();
    } else {
        header('Location: index.php');
        exit();
    }
}

$page_title = 'Register - World Publications Awards';
include 'includes/header.php';

$error_messages = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username)) {
        $error_messages[] = 'Username is required';
    } elseif (strlen($username) < 3) {
        $error_messages[] = 'Username must be at least 3 characters long';
    }
    
    if (empty($email)) {
        $error_messages[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_messages[] = 'Invalid email format';
    }
    
    if (empty($password)) {
        $error_messages[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $error_messages[] = 'Password must be at least 6 characters long';
    }
    
    if ($password !== $confirm_password) {
        $error_messages[] = 'Passwords do not match';
    }
    
    // Check if username or email already exists
    if (empty($error_messages)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $error_messages[] = 'Username or email already exists';
            }
        } catch (PDOException $e) {
            $error_messages[] = 'Database error occurred';
        }
    }
    
    // If no errors, create the user
    if (empty($error_messages)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
            $result = $stmt->execute([$username, $email, $hashed_password]);
            
            if ($result) {
                $success_message = 'Registration successful! You can now login.';
            } else {
                $error_messages[] = 'Registration failed. Please try again.';
            }
        } catch (PDOException $e) {
            $error_messages[] = 'Database error occurred';
        }
    }
}
?>

<!-- Register Hero Section -->
<section class="bg-dark text-white py-5">
    <div class="container text-center">
        <h1 class="fw-bold">Create Account</h1>
        <p class="lead mt-3">
            Join the World Publications Awards community
        </p>
    </div>
</section>

<!-- Register Content -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <h3 class="card-title text-center mb-4 fw-bold">Sign Up</h3>
                        
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($error_messages)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($error_messages as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (empty($success_message)): ?>
                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="username" class="form-label fw-semibold">Username</label>
                                <input type="text" class="form-control form-control-lg" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                                <div class="invalid-feedback">
                                    Please enter a username
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                <div class="invalid-feedback">
                                    Please enter a valid email
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                                <div class="invalid-feedback">
                                    Please enter a password
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label fw-semibold">Confirm Password</label>
                                <input type="password" class="form-control form-control-lg" id="confirm_password" name="confirm_password" required>
                                <div class="invalid-feedback">
                                    Please confirm your password
                                </div>
                            </div>
                            
                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                                    <i class="fas fa-user-plus me-2"></i>Register
                                </button>
                            </div>
                        </form>
                        <?php else: ?>
                            <div class="text-center">
                                <a href="login.php" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login Now
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-center">
                            <p class="mb-0">Already have an account?</p>
                            <a href="login.php" class="btn btn-outline-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>