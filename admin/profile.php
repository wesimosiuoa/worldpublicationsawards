<?php
include 'header.php';

// Get current user data
$user_id = $_SESSION['user_id'] ?? 0;
$message = '';
$message_type = '';

if (!$user_id) {
    header('Location: ../login.php');
    exit();
}

// Check if first_name, last_name, and is_active columns exist
$hasNameColumns = false;
$hasIsActiveColumn = false;
try {
    $checkStmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'first_name'");
    $hasNameColumns = $checkStmt->rowCount() > 0;
} catch (PDOException $e) {
    $hasNameColumns = false;
}

try {
    $checkStmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'is_active'");
    $hasIsActiveColumn = $checkStmt->rowCount() > 0;
} catch (PDOException $e) {
    $hasIsActiveColumn = false;
}

// Fetch user data
if ($hasNameColumns && $hasIsActiveColumn) {
    $stmt = $pdo->prepare("SELECT id, username, email, first_name, last_name, role, is_active, created_at, updated_at FROM users WHERE id = ?");
} elseif ($hasNameColumns) {
    $stmt = $pdo->prepare("SELECT id, username, email, first_name, last_name, role, created_at, updated_at FROM users WHERE id = ?");
} elseif ($hasIsActiveColumn) {
    $stmt = $pdo->prepare("SELECT id, username, email, role, is_active, created_at, updated_at FROM users WHERE id = ?");
} else {
    $stmt = $pdo->prepare("SELECT id, username, email, role, created_at, updated_at FROM users WHERE id = ?");
}
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Set default values if columns don't exist
if (!$hasNameColumns) {
    $user['first_name'] = 'User';
    $user['last_name'] = 'Account';
}
if (!$hasIsActiveColumn) {
    $user['is_active'] = 1; // Default to active
}

if (!$user) {
    header('Location: ../login.php');
    exit();
}

// Handle form submission for profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    $errors = [];
    
    if ($hasNameColumns && empty($first_name)) {
        $errors[] = 'First name is required';
    }
    
    if ($hasNameColumns && empty($last_name)) {
        $errors[] = 'Last name is required';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    } else {
        // Check if email is already taken by another user
        $emailStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $emailStmt->execute([$email, $user_id]);
        if ($emailStmt->fetch()) {
            $errors[] = 'Email is already taken by another user';
        }
    }
    
    // Password validation
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors[] = 'Current password is required to change password';
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = 'Current password is incorrect';
        }
        
        if (strlen($new_password) < 6) {
            $errors[] = 'New password must be at least 6 characters long';
        }
        
        if ($new_password !== $confirm_password) {
            $errors[] = 'New password and confirmation do not match';
        }
    }
    
    if (empty($errors)) {
        try {
            if (!empty($new_password)) {
                // Update with new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                if ($hasNameColumns) {
                    $updateStmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $result = $updateStmt->execute([$first_name, $last_name, $email, $hashed_password, $user_id]);
                } else {
                    $updateStmt = $pdo->prepare("UPDATE users SET email = ?, password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $result = $updateStmt->execute([$email, $hashed_password, $user_id]);
                }
            } else {
                // Update without password change
                if ($hasNameColumns) {
                    $updateStmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $result = $updateStmt->execute([$first_name, $last_name, $email, $user_id]);
                } else {
                    $updateStmt = $pdo->prepare("UPDATE users SET email = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $result = $updateStmt->execute([$email, $user_id]);
                }
            }
            
            if ($result) {
                $message = 'Profile updated successfully!';
                $message_type = 'success';
                
                // Update session data
                $_SESSION['email'] = $email;
                
                // Refresh user data
                if ($hasNameColumns && $hasIsActiveColumn) {
                    $stmt = $pdo->prepare("SELECT id, username, email, first_name, last_name, role, is_active, created_at, updated_at FROM users WHERE id = ?");
                } elseif ($hasNameColumns) {
                    $stmt = $pdo->prepare("SELECT id, username, email, first_name, last_name, role, created_at, updated_at FROM users WHERE id = ?");
                } elseif ($hasIsActiveColumn) {
                    $stmt = $pdo->prepare("SELECT id, username, email, role, is_active, created_at, updated_at FROM users WHERE id = ?");
                } else {
                    $stmt = $pdo->prepare("SELECT id, username, email, role, created_at, updated_at FROM users WHERE id = ?");
                }
                $stmt->execute([$user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Set default values if columns don't exist
                if (!$hasNameColumns) {
                    $user['first_name'] = 'User';
                    $user['last_name'] = 'Account';
                }
                if (!$hasIsActiveColumn) {
                    $user['is_active'] = 1; // Default to active
                }
            } else {
                $message = 'Error updating profile.';
                $message_type = 'danger';
            }
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $message_type = 'danger';
        }
    } else {
        $message = implode('<br>', $errors);
        $message_type = 'danger';
    }
}
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2>User Profile</h2>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <!-- Profile Info Card -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Profile Information</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" 
                                     style="width: 100px; height: 100px; font-size: 2.5rem;">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                            </div>
                            <h4><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h4>
                            <p class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></p>
                            <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'stakeholder' ? 'warning' : 'secondary'); ?>">
                                <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                            </span>
                            <hr>
                            <p class="mb-1"><i class="fas fa-envelope me-2 text-muted"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                            <p class="mb-1"><i class="fas fa-calendar-plus me-2 text-muted"></i> Member since <?php echo date('M j, Y', strtotime($user['created_at'])); ?></p>
                            <p class="mb-0"><i class="fas fa-sync me-2 text-muted"></i> Last updated <?php echo date('M j, Y', strtotime($user['updated_at'])); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Edit Profile Form -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Profile</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="row">
                                    <?php if ($hasNameColumns): ?>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="first_name" class="form-label">First Name *</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                                   value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="last_name" class="form-label">Last Name *</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                                   value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="first_name" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                                   value="<?php echo htmlspecialchars($user['first_name']); ?>" disabled>
                                            <div class="form-text">First name cannot be changed (missing column in database)</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="last_name" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                                   value="<?php echo htmlspecialchars($user['last_name']); ?>" disabled>
                                            <div class="form-text">Last name cannot be changed (missing column in database)</div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" 
                                           value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                    <div class="form-text">Username cannot be changed</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <input type="text" class="form-control" id="role" 
                                           value="<?php echo ucfirst(htmlspecialchars($user['role'])); ?>" disabled>
                                </div>
                                
                                <hr>
                                <h5 class="mb-3">Change Password</h5>
                                <p class="text-muted">Leave these fields blank if you don't want to change your password.</p>
                                
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="new_password" class="form-label">New Password</label>
                                            <input type="password" class="form-control" id="new_password" name="new_password">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="dashboard.php" class="btn btn-secondary me-md-2">Cancel</a>
                                    <button type="submit" name="update_profile" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>