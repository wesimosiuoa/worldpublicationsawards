<?php
// Login Page for World Publications Awards
session_start();

// If user is already logged in, redirect to appropriate dashboard
// if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
//     $user_role = $_SESSION['role'] ?? 'user';
//     if ($user_role === 'admin') {
//         header('Location: admin/dashboard.php');
//         exit();
//     } else if ($user_role === 'nominee') {
//         header('Location: nominee-dashboard.php');
//         exit();
//     } else {
//         header('Location: index.php');
//         exit();
//     }
// }

// Include database connection and helpers before any output
include 'includes/dbcon.inc.php';
include 'includes/helpers.php';
include 'includes/fn.inc.php';

$error_message = '';
$success_message = '';
$step = $_GET['step'] ?? 'login'; // 'login' or 'verify_otp'
$email = '';

// Configure email settings for OTP sending
ini_set('SMTP', 'localhost');
ini_set('smtp_port', 25);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['request_otp'])) {
        // Step 1: Request OTP for nominee login
        $email = trim($_POST['email'] ?? '');
        
        if (empty($email) || !isValidEmail($email)) {
            $error_message = 'Please enter a valid email address.';
        } else {
            // Check if email belongs to a nominee
            try {
                // Ensure nominee user account exists
                $userId = ensureNomineeUserAccount($email);
                
                if (!$userId) {
                    // Check if email exists in nominees table
                    $stmt = $pdo->prepare("SELECT * FROM nominees WHERE email = ? OR contact_person_email = ?");
                    $stmt->execute([$email, $email]);
                    $nominee = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$nominee) {
                        $error_message = 'No nominee found with this email address.';
                    } else {
                        // Create a nominee user account
                        $userId = ensureNomineeUserAccount($email);
                        
                        if (!$userId) {
                            $error_message = 'Failed to create nominee account. Please contact support.';
                        } else {
                            // Generate and send OTP
                            $otp = generateOTP();
                            if (storeOTP($email, $otp)) {
                                // Attempt to send via email
                                sendEmail($email, 'OTP for World Publications Awards Login', "Your OTP is: $otp");
                                // echo "Email sent successfully";
                                // if (sendOTPEmailEnhanced($email, $otp)) {
                                //     $success_message = "An OTP has been sent to your email address. Please check your inbox.";
                                // } else {
                                //     // Fallback: Show OTP for development
                                //     $success_message = "OTP has been generated. For testing purposes, your OTP is: <strong>$otp</strong> (This should be sent via email in production).";
                                // }
                                $step = 'verify_otp';
                            } else {
                                $error_message = 'Failed to generate OTP. Please try again.';
                            }
                        }
                    }
                } else {
                    // Nominee user account already exists, generate and send OTP
                    $otp = generateOTP();
                    if (storeOTP($email, $otp)) {
                        // Attempt to send via email
                        sendEmail($email, 'OTP for World Publications Awards Login', "Your OTP is: $otp");
                        // echo "Email sent successfully";
                        $step = 'verify_otp';
                    } else {
                        $error_message = 'Failed to generate OTP. Please try again.';
                    }
                }
            } catch (PDOException $e) {
                $error_message = 'Database error occurred: ' . $e->getMessage();
            }
        }
    } elseif (isset($_POST['verify_otp'])) {
        // Step 2: Verify OTP
        $email = trim($_POST['email'] ?? '');
        $otp = trim($_POST['otp'] ?? '');
        
        if (empty($email) || empty($otp)) {
            $error_message = 'Please enter both email and OTP.';
        } else {
            if (validateOTP($email, $otp)) {
                // OTP is valid, log in the user
                try {
                    // Find the user by email
                        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'nominee'");
                        $stmt->execute([$email]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($user) {
                        $_SESSION['loggedin'] = true;
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['email'] = $user['email'] ?? $email; // Use provided email if column doesn't exist
                        $_SESSION['role'] = $user['role'];
                        

                        ?>
                        
                        <?php
                        header('Location: nominees/nominee-dashboard.php');
                        exit();
                    } else {
                        $error_message = 'User account not found. Please contact support.';
                    }
                } catch (PDOException $e) {
                    $error_message = 'Database error occurred: ' . $e->getMessage();
                }
            } else {
                $error_message = 'Invalid or expired OTP. Please try again.';
            }
        }
    } else {
        // Traditional login
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (!empty($username) && !empty($password)) {
            try {
                // Check if the users table has an email column
                $columnCheck = $pdo->query("SHOW COLUMNS FROM users LIKE 'email'");
                $hasEmailColumn = $columnCheck->rowCount() > 0;
                
                // Prepare statement to prevent SQL injection
                if ($hasEmailColumn) {
                    $stmt = $pdo->prepare("SELECT id, username, password, role, email FROM users WHERE username = ? OR email = ?");
                    $stmt->execute([$username, $username]);
                } else {
                    $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
                    $stmt->execute([$username]);
                }
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && password_verify($password, $user['password'])) {
                    // Successful login
                    $_SESSION['loggedin'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    
                    // Set email in session if column exists
                    if ($hasEmailColumn && isset($user['email'])) {
                        $_SESSION['email'] = $user['email'];
                    }
                    
                    // Redirect based on role
                    if ($user['role'] === 'admin' || $user['role'] === 'stakeholder') {
                        
                        header('Location: admin/dashboard.php');
                    } /*else if ($user['role'] === 'nominee') {
                        header('Location: nominees/nominee-dashboard.php');
                    } */
                    else {
                        header('Location: index.php');
                    }
                    exit();
                } else {
                    $error_message = 'Invalid username or password';
                }
            } catch (PDOException $e) {
                $error_message = 'Database error occurred: ' . $e->getMessage();
            }
        } else {
            $error_message = 'Please enter both username and password';
        }
    }
}

// Now include header after all potential redirects
$page_title = 'Login - World Publications Awards';
include 'includes/header.php';
?>

<!-- Login Hero Section -->
<section class="bg-dark text-white py-5">
    <div class="container text-center">
        <h1 class="fw-bold">Stakeholder Login</h1>
        <p class="lead mt-3">
            Access your World Publications Awards account
        </p>
    </div>
</section>

<!-- Login Content -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <h3 class="card-title text-center mb-4 fw-bold">Sign In</h3>
                        
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($step === 'login'): ?>
                            <!-- Traditional Login Form -->
                            <form method="POST" action="" class="needs-validation" novalidate>
                                <div class="mb-4">
                                    <label for="username" class="form-label fw-semibold">Username or Email</label>
                                    <input type="text" class="form-control form-control-lg" id="username" name="username" 
                                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                                    <div class="invalid-feedback">
                                        Please enter your username or email
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="password" class="form-label fw-semibold">Password</label>
                                    <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                                    <div class="invalid-feedback">
                                        Please enter your password
                                    </div>
                                </div>
                                
                                <div class="mb-4 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                
                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login
                                    </button>
                                </div>
                            </form>
                            
                            <!-- <div class="text-center">
                                <p class="mb-0">Don't have an account?</p>
                                <a href="register.php" class="btn btn-outline-primary">
                                    <i class="fas fa-user-plus me-2"></i>Create Account
                                </a>
                            </div> -->
                            
                            <div class="text-center mt-4">
                                <p class="mb-0">Are you a nominee?</p>
                                <a href="?step=request_otp" class="btn btn-outline-success">
                                    <i class="fas fa-key me-2"></i>Nominee Login
                                </a>
                            </div>
                        <?php elseif ($step === 'request_otp'): ?>
                            <!-- Nominee OTP Request Form -->
                            <form method="POST" action="" class="needs-validation" novalidate>
                                <div class="mb-4">
                                    <label for="email" class="form-label fw-semibold">Email Address</label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($email); ?>" required>
                                    <div class="invalid-feedback">
                                        Please enter your email address
                                    </div>
                                    <div class="form-text">
                                        Enter the email address associated with your nominee account
                                    </div>
                                </div>
                                
                                <div class="d-grid mb-4">
                                    <button type="submit" name="request_otp" class="btn btn-primary btn-lg fw-semibold">
                                        <i class="fas fa-key me-2"></i>Request OTP
                                    </button>
                                </div>
                            </form>
                            
                            <div class="text-center">
                                <a href="?step=login" class="btn btn-link">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Regular Login
                                </a>
                            </div>
                        <?php elseif ($step === 'verify_otp'): ?>
                            <!-- OTP Verification Form -->
                            <form method="POST" action="" class="needs-validation" novalidate>
                                <div class="mb-4">
                                    <label for="email" class="form-label fw-semibold">Email Address</label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($email); ?>" readonly>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="otp" class="form-label fw-semibold">One-Time Password (OTP)</label>
                                    <input type="text" class="form-control form-control-lg" id="otp" name="otp" 
                                           placeholder="Enter 6-digit OTP" maxlength="6" required>
                                    <div class="invalid-feedback">
                                        Please enter the 6-digit OTP
                                    </div>
                                    <div class="form-text">
                                        Enter the OTP sent to your email address
                                    </div>
                                </div>
                                
                                <div class="d-grid mb-4">
                                    <button type="submit" name="verify_otp" class="btn btn-success btn-lg fw-semibold">
                                        <i class="fas fa-lock me-2"></i>Verify & Login
                                    </button>
                                </div>
                                
                                <div class="text-center">
                                    <a href="?step=request_otp" class="btn btn-link">
                                        <i class="fas fa-redo me-2"></i>Resend OTP
                                    </a>
                                    <a href="?step=login" class="btn btn-link">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Regular Login
                                    </a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>