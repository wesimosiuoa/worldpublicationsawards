<?php
include 'header.php'; 

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../index.php');
    exit();
}

// Check if user has admin or stakeholder role
$user_role = $_SESSION['role'] ?? '';
if ($user_role !== 'admin' && $user_role !== 'stakeholder') {
    header('Location: ../index.php');
    exit();
}

include '../includes/dbcon.inc.php';
include '../includes/messages.php';

// Check if user has admin role
if ($user_role !== 'admin') {
    ?>
        <script> alert('You do not have permission to access this page.'); window.location.href = '../index.php'; </script>
    <?php
}

// Handle form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            // Add new user
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = trim($_POST['role'] ?? 'user');
            $password = $_POST['password'] ?? '';
            
            if (!empty($username) && !empty($email) && !empty($password)) {
                try {
                    // Check if username or email already exists
                    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                    $checkStmt->execute([$username, $email]);
                    $existingUser = $checkStmt->fetch();
                    
                    if ($existingUser) {
                        $message = 'Username or email already exists.';
                        $message_type = 'danger';
                    } else {
                        // Hash the password
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        // Insert user
                        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                        $result = $stmt->execute([$username, $email, $hashed_password, $role]);
                        
                        if ($result) {
                            $message = 'User added successfully!';
                            $message_type = 'success';
                        } else {
                            $message = 'Error adding user.';
                            $message_type = 'danger';
                        }
                    }
                } catch (PDOException $e) {
                    $message = 'Database error: ' . $e->getMessage();
                    $message_type = 'danger';
                }
            } else {
                $message = 'Please fill in all required fields.';
                $message_type = 'warning';
            }
        } 
        elseif ($_POST['action'] === 'edit') {
            // Edit existing user
            $id = (int)($_POST['id'] ?? 0);
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = trim($_POST['role'] ?? 'user');
            $password = $_POST['password'] ?? '';
            
            if ($id > 0 && !empty($username) && !empty($email)) {
                try {
                    // Check if username or email already exists for other users
                    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
                    $checkStmt->execute([$username, $email, $id]);
                    $existingUser = $checkStmt->fetch();
                    
                    if ($existingUser) {
                        $message = 'Username or email already exists.';
                        $message_type = 'danger';
                    } else {
                        // Update user (password is optional)
                        if (!empty($password)) {
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, password=?, role=? WHERE id=?");
                            $result = $stmt->execute([$username, $email, $hashed_password, $role, $id]);
                        } else {
                            $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
                            $result = $stmt->execute([$username, $email, $role, $id]);
                        }
                        
                        if ($result) {
                            $message = 'User updated successfully!';
                            $message_type = 'success';
                        } else {
                            $message = 'Error updating user.';
                            $message_type = 'danger';
                        }
                    }
                } catch (PDOException $e) {
                    $message = 'Database error: ' . $e->getMessage();
                    $message_type = 'danger';
                }
            } else {
                $message = 'Please fill in all required fields.';
                $message_type = 'warning';
            }
        }
        elseif ($_POST['action'] === 'delete') {
            // Delete user
            $id = (int)($_POST['id'] ?? 0);
            
            // Prevent admin from deleting themselves
            $current_user_id = $_SESSION['user_id'] ?? 0;
            if ($id == $current_user_id) {
                $message = 'You cannot delete your own account.';
                $message_type = 'danger';
            } else if ($id > 0) {
                try {
                    // Delete user
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                    $result = $stmt->execute([$id]);
                    
                    if ($result) {
                        $message = 'User deleted successfully!';
                        $message_type = 'success';
                    } else {
                        $message = 'Error deleting user.';
                        $message_type = 'danger';
                    }
                } catch (PDOException $e) {
                    $message = 'Database error: ' . $e->getMessage();
                    $message_type = 'danger';
                }
            }
        }
    }
}

// Fetch all users
$usersStmt = $pdo->query("SELECT id, username, email, role, created_at, updated_at FROM users ORDER BY created_at DESC");
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

// Get user to edit if in edit mode
$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - World Publications Awards</title>
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

<!-- USERS MANAGE HERO -->
<section class="bg-dark text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="fw-bold">Manage Users</h1>
                <p class="lead">Add, edit, or remove users</p>
            </div>
            
        </div>
    </div>
</section>

<!-- USERS MANAGE CONTENT -->
<section class="py-5">
    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- ADD/EDIT MODAL -->
        <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel"><?php echo $edit_user ? 'Edit User' : 'Add New User'; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="userForm" method="POST" action="users.php<?php echo $edit_user ? '?edit=' . $edit_user['id'] : ''; ?>">
                            <input type="hidden" name="action" value="<?php echo $edit_user ? 'edit' : 'add'; ?>">
                            <?php if ($edit_user): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_user['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Username *</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($edit_user['username'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($edit_user['email'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="user" <?php echo (isset($edit_user['role']) && $edit_user['role'] === 'user') ? 'selected' : ''; ?>>
                                        User
                                    </option>
                                    <option value="admin" <?php echo (isset($edit_user['role']) && $edit_user['role'] === 'admin') ? 'selected' : ''; ?>>
                                        Admin
                                    </option>
                                    <option value="stakeholder" <?php echo (isset($edit_user['role']) && $edit_user['role'] === 'stakeholder') ? 'selected' : ''; ?>>
                                        Stakeholder
                                    </option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label"><?php echo $edit_user ? 'New Password (leave blank to keep current)' : 'Password *'; ?></label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       <?php echo $edit_user ? '' : 'required'; ?>>
                                <?php if ($edit_user): ?>
                                    <div class="form-text">Leave blank to keep the current password</div>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" form="userForm" class="btn btn-primary">
                            <?php echo $edit_user ? 'Update User' : 'Add User'; ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Trigger modal if editing -->
        <?php if ($edit_user): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var userModal = new bootstrap.Modal(document.getElementById('userModal'));
                userModal.show();
            });
        </script>
        <?php endif; ?>
        
        <!-- BUTTON TO OPEN ADD MODAL -->
        <div class="mb-3 d-flex justify-content-between">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                Add New User
            </button>
        </div>
        
        <!-- USERS LIST -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Users</h5>
                <span class="badge bg-secondary"><?php echo count($users); ?> users</span>
            </div>
            <div class="card-body">
                <!-- Search -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search users...">
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped" id="usersTable">
                        <thead>
                            <tr>
                                <th><a href="#" class="text-decoration-none text-dark">ID</a></th>
                                <th><a href="#" class="text-decoration-none text-dark">Username</a></th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge 
                                        <?php 
                                        if ($user['role'] === 'admin') echo 'bg-danger'; 
                                        elseif ($user['role'] === 'stakeholder') echo 'bg-warning text-dark'; 
                                        else echo 'bg-secondary'; 
                                        ?>">
                                        <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <a href="users.php?edit=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-info me-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo addslashes(htmlspecialchars($user['username'])); ?>')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmDelete(id, name) {
    if (confirm(`Are you sure you want to delete the user "${name}"?\nThis action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'delete';
        form.appendChild(actionInput);
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        form.appendChild(idInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Filtering functionality
function filterUsers() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    
    const tableRows = document.querySelectorAll('#usersTable tbody tr');
    
    tableRows.forEach(row => {
        const cells = row.cells;
        const usernameText = cells[1].textContent.toLowerCase();
        const emailText = cells[2].textContent.toLowerCase();
        const roleText = cells[3].textContent.toLowerCase();
        
        let showRow = true;
        
        // Apply search filter
        if (searchInput && !usernameText.includes(searchInput) && !emailText.includes(searchInput) && !roleText.includes(searchInput)) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

// Add event listener to search
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchInput').addEventListener('input', filterUsers);
});
</script>

</body>
</html>