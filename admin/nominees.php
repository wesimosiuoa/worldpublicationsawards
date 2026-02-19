<?php
session_start();

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
include '../includes/helpers.php';
include 'fn.inc.php';
include 'header.php';

// Check if user has admin role (for editing capabilities)
$is_admin = ($user_role === 'admin');

// Handle form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_admin) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $category_id = (int)($_POST['category_id'] ?? 0);
            $country_id = (int)($_POST['country_id'] ?? 0);
            $email = trim($_POST['email'] ?? '');
            $contact_person_email = trim($_POST['contact_person_email'] ?? '');
            $website_url = trim($_POST['website_url'] ?? '');
            $nominee_type = trim($_POST['nominee_type'] ?? 'organization');
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            
            if (!empty($name) && $category_id > 0 && $country_id > 0) {
                try {
                    // Handle logo upload if in edit mode or adding
                    $logo_filename = '';
                    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
                        $file_ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                        
                        if (in_array($file_ext, $allowed_exts)) {
                            // Remove old logo if editing
                            if ($_POST['action'] === 'edit' && !empty($_POST['existing_logo'])) {
                                $old_path = '../assets/images/' . basename($_POST['existing_logo']);
                                if (file_exists($old_path)) {
                                    unlink($old_path);
                                }
                            }
                            
                            $logo_filename = uniqid() . '_' . basename($_FILES['logo']['name']);
                            $upload_path = '../assets/images/' . $logo_filename;
                            
                            if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                                // Successfully uploaded
                            } else {
                                $message = 'Error uploading logo file.';
                                $message_type = 'danger';
                            }
                        } else {
                            $message = 'Invalid file type. Only JPG, PNG, GIF, and SVG files are allowed.';
                            $message_type = 'danger';
                        }
                    } else {
                        // Keep existing logo if no new file uploaded
                        $logo_filename = $_POST['existing_logo'] ?? '';
                    }
                    
                    // Handle slug generation
                    $slug = trim($_POST['slug'] ?? '');
                    if (empty($slug)) {
                        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
                    }
                    
                    if ($_POST['action'] === 'add') {
                        // Add new nominee
                        $stmt = $pdo->prepare("INSERT INTO nominees (name, description, category_id, country_id, email, contact_person_email, slug, logo, website_url, nominee_type, is_active, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $result = $stmt->execute([$name, $description, $category_id, $country_id, $email, $contact_person_email, $slug, $logo_filename, $website_url, $nominee_type, $is_active, $is_featured]);
                    } else {
                        // Edit existing nominee
                        $nominee_id = (int)($_POST['id'] ?? 0);
                        if ($nominee_id > 0) {
                            $stmt = $pdo->prepare("UPDATE nominees SET name=?, description=?, category_id=?, country_id=?, email=?, contact_person_email=?, slug=?, logo=?, website_url=?, nominee_type=?, is_active=?, is_featured=?, updated_at=CURRENT_TIMESTAMP WHERE id=?");
                            $result = $stmt->execute([$name, $description, $category_id, $country_id, $email, $contact_person_email, $slug, $logo_filename, $website_url, $nominee_type, $is_active, $is_featured, $nominee_id]);
                        } else {
                            $result = false;
                        }
                    }
                    
                    if ($result) {
                        $message = $_POST['action'] === 'add' ? 'Nominee added successfully!' : 'Nominee updated successfully!';
                        $message_type = 'success';
                        

                        //send notification to voters about new nominee 
                        require_once '../notifications/notify_voters.php';
                        $subject = $_POST['action'] === 'add' ? 'New Nominee Added: ' . $name : 'Nominee Updated: ' . $name;
                        $message = $_POST['action'] === 'add' ? 'A new nominee has been added to the World Publications Awards: ' . $name . '. Check it out and cast your vote!' : 'A nominee has been updated in the World Publications Awards: ' . $name . '. Check out the changes and cast your vote!';

                        notifyVoters($subject, $message);

                    } else {
                        $message = $_POST['action'] === 'add' ? 'Error adding nominee.' : 'Error updating nominee.';
                        $message_type = 'danger';
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
        elseif ($_POST['action'] === 'delete' && $is_admin) {
            // Delete nominee
            $id = (int)($_POST['id'] ?? 0);
            
            if ($id > 0) {
                try {
                    // Get current logo to delete file
                    $getLogoStmt = $pdo->prepare("SELECT logo FROM nominees WHERE id = ?");
                    $getLogoStmt->execute([$id]);
                    $nominee_data = $getLogoStmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Delete nominee
                    $stmt = $pdo->prepare("DELETE FROM nominees WHERE id = ?");
                    $result = $stmt->execute([$id]);
                    
                    if ($result) {
                        // Delete logo file if it exists
                        if (!empty($nominee_data['logo'])) {
                            $logo_path = '../assets/images/' . $nominee_data['logo'];
                            if (file_exists($logo_path)) {
                                unlink($logo_path);
                            }
                        }
                        
                        $message = 'Nominee deleted successfully!';
                        $message_type = 'success';
                    } else {
                        $message = 'Error deleting nominee.';
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

// Get nominee to edit if in edit mode
$edit_nominee = null;
if (isset($_GET['edit']) && $is_admin) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare(
        "SELECT n.*, c.name as category_name, co.name as country_name, co.iso_code 
        FROM nominees n 
        LEFT JOIN categories c ON n.category_id = c.id 
        LEFT JOIN countries co ON n.country_id = co.id 
        WHERE n.id = ?"
    );
    $stmt->execute([$edit_id]);
    $edit_nominee = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all nominees with category and country names
$search = trim($_GET['search'] ?? '');
$category_filter = (int)($_GET['category'] ?? 0);
$status_filter = $_GET['status'] ?? '';
$featured_filter = $_GET['featured'] ?? '';

$sql = "SELECT n.*, c.name as category_name, co.name as country_name, co.iso_code 
        FROM nominees n 
        LEFT JOIN categories c ON n.category_id = c.id 
        LEFT JOIN countries co ON n.country_id = co.id";
$params = [];

// Add search condition
if (!empty($search)) {
    $sql .= " WHERE (n.name LIKE ? OR n.description LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

// Add category filter
if ($category_filter > 0) {
    $where_condition = !empty($search) ? " AND n.category_id = ?" : " WHERE n.category_id = ?";
    $sql .= $where_condition;
    $params[] = $category_filter;
}

// Add status filter
if ($status_filter !== '') {
    $where_condition = (!empty($search) || $category_filter > 0) ? " AND n.is_active = ?" : " WHERE n.is_active = ?";
    $sql .= $where_condition;
    $params[] = $status_filter;
}

// Add featured filter
if ($featured_filter !== '') {
    $where_condition = (!empty($search) || $category_filter > 0 || $status_filter !== '') ? " AND n.is_featured = ?" : " WHERE n.is_featured = ?";
    $sql .= $where_condition;
    $params[] = $featured_filter;
}

// Add sorting
$order_by = $_GET['order_by'] ?? 'n.created_at';
$order_dir = $_GET['order_dir'] ?? 'DESC';

// Validate order_by to prevent SQL injection
$allowed_orders = ['n.name', 'n.total_votes', 'n.total_amount_raised', 'n.created_at', 'c.name', 'co.name'];
if (!in_array($order_by, $allowed_orders)) {
    $order_by = 'n.created_at';
}

// Validate order_dir to prevent SQL injection
$order_dir = strtoupper($order_dir) === 'ASC' ? 'ASC' : 'DESC';

$sql .= " ORDER BY {$order_by} {$order_dir}";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$nominees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all categories for filter
$categoriesStmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all countries for the add/edit form
$countriesStmt = $pdo->query("SELECT * FROM countries ORDER BY name");
$countries = $countriesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Nominees - World Publications Awards</title>
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

<!-- NOMINEES MANAGE HERO -->
<section class="bg-dark text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="fw-bold">Manage Nominees</h1>
                <p class="lead">Add, edit, or remove nominees</p>
            </div>
            
        </div>
    </div>
</section>

<!-- NOMINEES MANAGE CONTENT -->
<section class="py-5">
    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- ADD/EDIT MODAL -->
        <?php if ($is_admin): ?>
        <div class="modal fade" id="nomineeModal" tabindex="-1" aria-labelledby="nomineeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="nomineeModalLabel"><?php echo $edit_nominee ? 'Edit Nominee' : 'Add New Nominee'; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="nomineeForm" method="POST" action="nominees.php<?php echo $edit_nominee ? '?edit=' . $edit_nominee['id'] : ''; ?>" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="<?php echo $edit_nominee ? 'edit' : 'add'; ?>">
                            <?php if ($edit_nominee): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_nominee['id']; ?>">
                                <input type="hidden" name="existing_logo" value="<?php echo htmlspecialchars($edit_nominee['logo'] ?? ''); ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nominee Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo htmlspecialchars($edit_nominee['name'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="slug" class="form-label">Slug</label>
                                        <input type="text" class="form-control" id="slug" name="slug" 
                                               value="<?php echo htmlspecialchars($edit_nominee['slug'] ?? ''); ?>" 
                                               placeholder="Auto-generated from name">
                                        <div class="form-text">Will be auto-generated from name if left empty</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category *</label>
                                        <select class="form-select" id="category_id" name="category_id" required>
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" 
                                                        <?php echo (isset($edit_nominee['category_id']) && $edit_nominee['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="country_id" class="form-label">Country *</label>
                                        <select class="form-select" id="country_id" name="country_id" required>
                                            <option value="">Select Country</option>
                                            <?php foreach ($countries as $country): ?>
                                                <option value="<?php echo $country['id']; ?>" 
                                                        <?php echo (isset($edit_nominee['country_id']) && $edit_nominee['country_id'] == $country['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($country['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="nominee_type" class="form-label">Nominee Type</label>
                                        <select class="form-select" id="nominee_type" name="nominee_type">
                                            <option value="organization" <?php echo (isset($edit_nominee['nominee_type']) && $edit_nominee['nominee_type'] === 'organization') ? 'selected' : ''; ?>>
                                                Organization
                                            </option>
                                            <option value="individual" <?php echo (isset($edit_nominee['nominee_type']) && $edit_nominee['nominee_type'] === 'individual') ? 'selected' : ''; ?>>
                                                Individual
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="logo" class="form-label">Logo</label>
                                        <input type="file" class="form-control" id="logo" name="logo" accept=".jpg,.jpeg,.png,.gif,.svg">
                                        <?php if ($edit_nominee && !empty($edit_nominee['logo'])): ?>
                                            <div class="mt-2">
                                                <small class="text-muted">Current logo: <?php echo htmlspecialchars($edit_nominee['logo']); ?></small><br>
                                                <img src="../assets/images/<?php echo htmlspecialchars($edit_nominee['logo']); ?>" 
                                                     alt="Current Logo" class="img-thumbnail mt-1" style="max-height: 100px;">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Nominee Email</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($edit_nominee['email'] ?? ''); ?>">
                                        <div class="form-text">Email for nominee login access</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="contact_person_email" class="form-label">Contact Person Email</label>
                                        <input type="email" class="form-control" id="contact_person_email" name="contact_person_email" 
                                               value="<?php echo htmlspecialchars($edit_nominee['contact_person_email'] ?? ''); ?>">
                                        <div class="form-text">Primary contact person email</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="website_url" class="form-label">Website URL</label>
                                        <input type="url" class="form-control" id="website_url" name="website_url" 
                                               value="<?php echo htmlspecialchars($edit_nominee['website_url'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                                       <?php echo (isset($edit_nominee['is_active']) && $edit_nominee['is_active']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_active">Active</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" 
                                                       <?php echo (isset($edit_nominee['is_featured']) && $edit_nominee['is_featured']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_featured">Featured</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($edit_nominee['description'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" form="nomineeForm" class="btn btn-primary">
                            <?php echo $edit_nominee ? 'Update Nominee' : 'Add Nominee'; ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Trigger modal if editing -->
        <?php if ($edit_nominee): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var nomineeModal = new bootstrap.Modal(document.getElementById('nomineeModal'));
                nomineeModal.show();
            });
        </script>
        <?php endif; ?>
        <?php endif; ?>
        
        <!-- BUTTON TO OPEN ADD MODAL -->
        <?php if ($is_admin): ?>
        <div class="mb-3 d-flex justify-content-between">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nomineeModal">
                Add New Nominee
            </button>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#winnersModal">
                View Winners
            </button>
        </div>
        <?php endif; ?>
        
        <!-- NOMINEES LIST -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Nominees</h5>
                <span class="badge bg-secondary"><?php echo count($nominees); ?> nominees</span>
            </div>
            <div class="card-body">
                <!-- Filters and Search -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-select" id="categoryFilter" onchange="applyFilters()">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="statusFilter" onchange="applyFilters()">
                            <option value="">All Statuses</option>
                            <option value="1" <?php echo $status_filter === '1' ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo $status_filter === '0' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="featuredFilter" onchange="applyFilters()">
                            <option value="">All Featured</option>
                            <option value="1" <?php echo $featured_filter === '1' ? 'selected' : ''; ?>>Featured</option>
                            <option value="0" <?php echo $featured_filter === '0' ? 'selected' : ''; ?>>Not Featured</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search nominees..." 
                                   value="<?php echo htmlspecialchars($search); ?>" onkeypress="handleKeyPress(event)">
                            <button class="btn btn-outline-secondary" type="button" onclick="applyFilters()">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-outline-secondary" type="button" onclick="clearFilters()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Sorting -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <small class="text-muted">Sort by: 
                            <a href="?order_by=n.name&order_dir=<?php echo $order_by === 'n.name' && $order_dir === 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>&featured=<?php echo $featured_filter; ?>" 
                               class="text-decoration-none <?php echo $order_by === 'n.name' ? 'fw-bold text-dark' : 'text-muted'; ?>">
                                Name <?php echo $order_by === 'n.name' ? ($order_dir === 'ASC' ? '↑' : '↓') : ''; ?>
                            </a> | 
                            <a href="?order_by=n.total_votes&order_dir=<?php echo $order_by === 'n.total_votes' && $order_dir === 'DESC' ? 'ASC' : 'DESC'; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>&featured=<?php echo $featured_filter; ?>" 
                               class="text-decoration-none <?php echo $order_by === 'n.total_votes' ? 'fw-bold text-dark' : 'text-muted'; ?>">
                                Votes <?php echo $order_by === 'n.total_votes' ? ($order_dir === 'ASC' ? '↑' : '↓') : ''; ?>
                            </a> | 
                            <a href="?order_by=n.total_amount_raised&order_dir=<?php echo $order_by === 'n.total_amount_raised' && $order_dir === 'DESC' ? 'ASC' : 'DESC'; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>&featured=<?php echo $featured_filter; ?>" 
                               class="text-decoration-none <?php echo $order_by === 'n.total_amount_raised' ? 'fw-bold text-dark' : 'text-muted'; ?>">
                                Amount Raised <?php echo $order_by === 'n.total_amount_raised' ? ($order_dir === 'ASC' ? '↑' : '↓') : ''; ?>
                            </a> | 
                            <a href="?order_by=c.name&order_dir=<?php echo $order_by === 'c.name' && $order_dir === 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>&featured=<?php echo $featured_filter; ?>" 
                               class="text-decoration-none <?php echo $order_by === 'c.name' ? 'fw-bold text-dark' : 'text-muted'; ?>">
                                Category <?php echo $order_by === 'c.name' ? ($order_dir === 'ASC' ? '↑' : '↓') : ''; ?>
                            </a> | 
                            <a href="?order_by=co.name&order_dir=<?php echo $order_by === 'co.name' && $order_dir === 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>&featured=<?php echo $featured_filter; ?>" 
                               class="text-decoration-none <?php echo $order_by === 'co.name' ? 'fw-bold text-dark' : 'text-muted'; ?>">
                                Country <?php echo $order_by === 'co.name' ? ($order_dir === 'ASC' ? '↑' : '↓') : ''; ?>
                            </a> | 
                            <a href="?order_by=n.created_at&order_dir=<?php echo $order_by === 'n.created_at' && $order_dir === 'DESC' ? 'ASC' : 'DESC'; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>&featured=<?php echo $featured_filter; ?>" 
                               class="text-decoration-none <?php echo $order_by === 'n.created_at' ? 'fw-bold text-dark' : 'text-muted'; ?>">
                                Date <?php echo $order_by === 'n.created_at' ? ($order_dir === 'ASC' ? '↑' : '↓') : ''; ?>
                            </a>
                        </small>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th><a href="?order_by=n.name&order_dir=<?php echo $order_by === 'n.name' && $order_dir === 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>&featured=<?php echo $featured_filter; ?>" 
                                       class="text-decoration-none text-dark">Name</a></th>
                                <th><a href="?order_by=c.name&order_dir=<?php echo $order_by === 'c.name' && $order_dir === 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>&featured=<?php echo $featured_filter; ?>" 
                                       class="text-decoration-none text-dark">Category</a></th>
                                <th><a href="?order_by=co.name&order_dir=<?php echo $order_by === 'co.name' && $order_dir === 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>&featured=<?php echo $featured_filter; ?>" 
                                       class="text-decoration-none text-dark">Country</a></th>
                                <th><a href="?order_by=n.total_votes&order_dir=<?php echo $order_by === 'n.total_votes' && $order_dir === 'DESC' ? 'ASC' : 'DESC'; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>&featured=<?php echo $featured_filter; ?>" 
                                       class="text-decoration-none text-dark">Votes</a></th>
                                <th><a href="?order_by=n.total_amount_raised&order_dir=<?php echo $order_by === 'n.total_amount_raised' && $order_dir === 'DESC' ? 'ASC' : 'DESC'; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>&featured=<?php echo $featured_filter; ?>" 
                                       class="text-decoration-none text-dark">Raised</a></th>
                                <th>Email</th>
                                <th><a href="?order_by=n.created_at&order_dir=<?php echo $order_by === 'n.created_at' && $order_dir === 'DESC' ? 'ASC' : 'DESC'; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>&featured=<?php echo $featured_filter; ?>" 
                                       class="text-decoration-none text-dark">Date</a></th>
                                <th>Active</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($nominees as $nominee): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($nominee['id']); ?></td>
                                <td>
                                    <?php if (!empty($nominee['logo'])): ?>
                                        <img src="../assets/images/<?php echo htmlspecialchars($nominee['logo']); ?>" 
                                             alt="<?php echo htmlspecialchars($nominee['name']); ?>" 
                                             style="height: 30px; width: 30px; object-fit: contain;" class="me-2">
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($nominee['name']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($nominee['category_name']); ?></td>
                                <td><?php echo render_country_flag($nominee['country_id']) . ' ' . htmlspecialchars($nominee['country_name']); ?></td>
                                <td><?php echo number_format($nominee['total_votes']); ?></td>
                                <td>$<?php echo number_format($nominee['total_amount_raised'], 2); ?></td>
                                <td>
                                    <?php if (!empty($nominee['email'])): ?>
                                        <span class="badge bg-info text-dark">
                                            <i class="fas fa-envelope me-1"></i>
                                            <?php echo htmlspecialchars(substr($nominee['email'], 0, 15)) . (strlen($nominee['email']) > 15 ? '...' : ''); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($nominee['created_at'])); ?></td>
                                <td>
                                    <span class="badge <?php echo $nominee['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo $nominee['is_active'] ? 'Yes' : 'No'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $nominee['is_featured'] ? 'bg-warning text-dark' : 'bg-light'; ?>">
                                        <?php echo $nominee['is_featured'] ? 'Yes' : 'No'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($is_admin): ?>
                                        <a href="nominees.php?edit=<?php echo $nominee['id']; ?>" class="btn btn-sm btn-outline-info me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="nominee_details.php?id=<?php echo $nominee['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?php echo $nominee['id']; ?>, '<?php echo addslashes(htmlspecialchars($nominee['name'])); ?>')" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">Limited access</span>
                                    <?php endif; ?>
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

<!-- WINNERS MODAL -->
<div class="modal fade" id="winnersModal" tabindex="-1" aria-labelledby="winnersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="winnersModalLabel">Award Winners</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php
                    // Get all categories
                    $allCategoriesStmt = $pdo->query("SELECT * FROM categories ORDER BY name");
                    $allCategories = $allCategoriesStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($allCategories as $category):
                        // Get top 3 nominees in this category by votes
                        $winnersStmt = $pdo->prepare(
                            "SELECT n.*, c.name as category_name, co.name as country_name, co.iso_code as country_iso_code 
                            FROM nominees n 
                            LEFT JOIN categories c ON n.category_id = c.id 
                            LEFT JOIN countries co ON n.country_id = co.id 
                            WHERE n.category_id = ? AND n.is_active = 1
                            ORDER BY n.total_votes DESC
                            LIMIT 3"
                        );
                        $winnersStmt->execute([$category['id']]);
                        $winners = $winnersStmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><?php echo htmlspecialchars($category['name']); ?></h5>
            </div>
            <div class="card-body">
                <?php if (empty($winners)): ?>
                    <p class="text-muted">No nominees in this category</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($winners as $index => $winner): 
                            $position = $index + 1;
                            $positionClass = $position === 1 ? 'bg-warning text-dark' : ($position === 2 ? 'bg-secondary text-white' : 'bg-info text-white');
                            $positionLabel = $position === 1 ? '1st Place 🥇' : ($position === 2 ? '2nd Place 🥈' : '3rd Place 🥉');
                        ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">
                                            <span class="badge <?php echo $positionClass; ?> me-2"><?php echo $positionLabel; ?></span>
                                            <?php echo htmlspecialchars($winner['name']); ?>
                                        </h6>
                                        <small class="text-muted">
                                            <?php echo render_country_flag($winner['country_id']) . ' ' . htmlspecialchars($winner['country_name']); ?>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold"><?php echo number_format($winner['total_votes']); ?> votes</div>
                                        <small class="text-muted">$<?php echo number_format($winner['total_amount_raised'], 2); ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-primary" onclick="window.open('../generate_winners_pdf.php', '_blank');">
        <i class="fas fa-file-pdf"></i> Export to PDF
    </button>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>

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
    if (confirm(`Are you sure you want to delete the nominee "${name}"?\nThis action cannot be undone.`)) {
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

function applyFilters() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const featuredFilter = document.getElementById('featuredFilter').value;
    const searchInput = document.getElementById('searchInput').value;
    
    let url = 'nominees.php';
    const params = [];
    
    if (categoryFilter) params.push('category=' + encodeURIComponent(categoryFilter));
    if (statusFilter) params.push('status=' + encodeURIComponent(statusFilter));
    if (featuredFilter) params.push('featured=' + encodeURIComponent(featuredFilter));
    if (searchInput) params.push('search=' + encodeURIComponent(searchInput));
    
    // Preserve sorting parameters
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('order_by')) params.push('order_by=' + urlParams.get('order_by'));
    if (urlParams.get('order_dir')) params.push('order_dir=' + urlParams.get('order_dir'));
    
    if (params.length > 0) {
        url += '?' + params.join('&');
    }
    
    window.location.href = url;
}

function clearFilters() {
    window.location.href = 'nominees.php';
}

function handleKeyPress(event) {
    if (event.key === 'Enter') {
        applyFilters();
    }
}

// Auto-refresh the modal when it's closed to clear form data
document.addEventListener('hide.bs.modal', function (event) {
    if (event.target.id === 'nomineeModal') {
        document.getElementById('nomineeForm').reset();
    }
});
</script>

</body>
</html>