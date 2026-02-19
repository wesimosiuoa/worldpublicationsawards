<?php 
include 'header.php';

// Get current user details
$stmt = $pdo->prepare("SELECT id, username, email, first_name, last_name, role, is_active, created_at, updated_at FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: dashboard.php');
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // Validate input
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error_message = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        try {
            // Check if email is already used by another user
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $checkStmt->execute([$email, $_SESSION['user_id']]);
            $existingUser = $checkStmt->fetch();
            
            if ($existingUser) {
                $error_message = 'Email address is already in use.';
            } else {
                // Update user profile
                $updateStmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $result = $updateStmt->execute([$first_name, $last_name, $email, $_SESSION['user_id']]);
                
                if ($result) {
                    $success_message = 'Profile updated successfully.';
                    // Refresh user data
                    $stmt = $pdo->prepare("SELECT id, username, email, first_name, last_name, role, is_active, created_at, updated_at FROM users WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    $error_message = 'Failed to update profile.';
                }
            }
        } catch (PDOException $e) {
            $error_message = 'Database error occurred.';
        }
    }
}
?>
<div class="container">
    <a href="dashboard.php" class="btn btn-outline-dark me-2">Back to Dashboard</a>

<!-- PROFILE CONTENT -->
<section class="py-5">
    <div class="container-fluid px-0">
        <div class="row g-0">
            <div class="col-12">
                <div class="row g-0">
                    <div class="col-md-8">
                        <div class="card border-0">
                            <div class="card-header">
                                <h5 class="mb-0">Profile Information</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($error_message)): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                                <?php endif; ?>
                                
                                <?php if (!empty($success_message)): ?>
                                    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                                <?php endif; ?>
                                
                                <form method="POST" action="profile.php">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="first_name" class="form-label">First Name</label>
                                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                                       value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="last_name" class="form-label">Last Name</label>
                                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                                       value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="username" class="form-label">Username</label>
                                                <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                                <div class="form-text">Username cannot be changed</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email Address</label>
                                                <input type="email" class="form-control" id="email" name="email" 
                                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="role" class="form-label">Role</label>
                                                <input type="text" class="form-control" id="role" value="<?php echo ucfirst(htmlspecialchars($user['role'])); ?>" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <input type="text" class="form-control" id="status" 
                                                       value="<?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="created_at" class="form-label">Member Since</label>
                                        <input type="text" class="form-control" id="created_at" 
                                               value="<?php echo date('M j, Y', strtotime($user['created_at'])); ?>" disabled>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Update Profile</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 ps-md-4">
                        <!-- Change Password Section -->
                        <div class="card border-0">
                            <div class="card-header">
                                <h5 class="mb-0">Change Password</h5>
                            </div>
                            <div class="card-body">
                                <form id="changePasswordForm">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <div class="form-text">8+ chars</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    <button type="submit" class="btn btn-warning w-100">Change Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


</div>
<!-- FOOTER -->
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

<script>
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        // Basic validation
        if (newPassword !== confirmPassword) {
            alert('New passwords do not match');
            return;
        }
        
        if (newPassword.length < 8) {
            alert('New password must be at least 8 characters long');
            return;
        }
        
        // In a real application, you would send this to a password update endpoint
        fetch('../includes/password_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'current_password=' + encodeURIComponent(currentPassword) + 
                  '&new_password=' + encodeURIComponent(newPassword)
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Password changed successfully');
                document.getElementById('changePasswordForm').reset();
            } else {
                alert(data.message || 'Failed to change password');
            }
        })
        .catch(error => {
            alert('An error occurred while changing password');
        });
    });
</script>

<?php include 'footer.php'; ?>