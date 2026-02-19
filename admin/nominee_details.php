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

// Check if user has admin role
if ($user_role !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Get nominee ID from URL parameter
$nominee_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($nominee_id <= 0) {
    header('Location: nominees.php');
    exit();
}

// Fetch nominee details from database
$stmt = $pdo->prepare(
    "SELECT n.*, c.name as category_name, co.name as country_name, co.iso_code as country_iso_code 
    FROM nominees n 
    LEFT JOIN categories c ON n.category_id = c.id 
    LEFT JOIN countries co ON n.country_id = co.id 
    WHERE n.id = ?
");
$stmt->execute([$nominee_id]);
$nominee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$nominee) {
    header('Location: nominees.php');
    exit();
}

// Handle form submission for updates
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $country_id = (int)($_POST['country_id'] ?? 0);
    $website_url = trim($_POST['website_url'] ?? '');
    $nominee_type = trim($_POST['nominee_type'] ?? 'organization');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    if (!empty($name) && $category_id > 0 && $country_id > 0) {
        try {
            // Handle logo upload
            $logo_filename = $nominee['logo'] ?? '';
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
                $file_ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                
                if (in_array($file_ext, $allowed_exts)) {
                    // Remove old logo if exists
                    $old_logo = $nominee['logo'] ?? '';
                    if (!empty($old_logo)) {
                        $old_path = '../assets/images/' . $old_logo;
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
            }
            
            // Handle slug generation
            $slug = trim($_POST['slug'] ?? '');
            if (empty($slug)) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            }
            
            // Update nominee
            $stmt = $pdo->prepare("UPDATE nominees SET name=?, description=?, category_id=?, country_id=?, slug=?, logo=?, website_url=?, nominee_type=?, is_active=?, is_featured=?, updated_at=CURRENT_TIMESTAMP WHERE id=?");
            $result = $stmt->execute([$name, $description, $category_id, $country_id, $slug, $logo_filename, $website_url, $nominee_type, $is_active, $is_featured, $nominee_id]);
            
            if ($result) {
                $message = 'Nominee updated successfully!';
                $message_type = 'success';
                
                // Refresh nominee data
                $stmt = $pdo->prepare(
                    "SELECT n.*, c.name as category_name, co.name as country_name, co.iso_code as country_iso_code 
                    FROM nominees n 
                    LEFT JOIN categories c ON n.category_id = c.id 
                    LEFT JOIN countries co ON n.country_id = co.id 
                    WHERE n.id = ?
                ");
                $stmt->execute([$nominee_id]);
                $nominee = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $message = 'Error updating nominee.';
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

// Fetch categories for dropdown
$categoriesStmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch countries for dropdown
$countriesStmt = $pdo->query("SELECT * FROM countries ORDER BY name");
$countries = $countriesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nominee Details - World Publications Awards</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="../assets/css/custom.css" rel="stylesheet">
</head>
<body>
    <?php 
        include 'header.php';
    ?>

<div style="margin-top:80px;"></div>

<!-- NOMINEE DETAILS HERO -->
<section class="bg-dark text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="fw-bold">Nominee Details</h1>
                <p class="lead"><?php echo htmlspecialchars($nominee['name']); ?></p>
            </div>
            
        </div>
    </div>
</section>

<!-- NOMINEE DETAILS CONTENT -->
<section class="py-5">
    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8">
                <!-- NOMINEE INFORMATION CARD -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Nominee Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($nominee['name']); ?></p>
                                <p><strong>Category:</strong> <?php echo htmlspecialchars($nominee['category_name']); ?></p>
                                <p><strong>Country:</strong> <?php echo render_country_flag($nominee['country_id']) . ' ' . htmlspecialchars($nominee['country_name']); ?></p>
                                <p><strong>Type:</strong> <?php echo ucfirst(htmlspecialchars($nominee['nominee_type'])); ?></p>
                                <p><strong>Slug:</strong> <?php echo htmlspecialchars($nominee['slug']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong> 
                                    <span class="badge <?php echo $nominee['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo $nominee['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </p>
                                <p><strong>Featured:</strong> 
                                    <span class="badge <?php echo $nominee['is_featured'] ? 'bg-warning text-dark' : 'bg-light'; ?>">
                                        <?php echo $nominee['is_featured'] ? 'Yes' : 'No'; ?>
                                    </span>
                                </p>
                                <p><strong>Votes:</strong> <?php echo number_format($nominee['total_votes']); ?></p>
                                <p><strong>Amount Raised:</strong> $<?php echo number_format($nominee['total_amount_raised'], 2); ?></p>
                            </div>
                        </div>
                        
                        <?php if (!empty($nominee['website_url'])): ?>
                        <p><strong>Website:</strong> <a href="<?php echo htmlspecialchars($nominee['website_url']); ?>" target="_blank"><?php echo htmlspecialchars($nominee['website_url']); ?></a></p>
                        <?php endif; ?>
                        
                        <p><strong>Description:</strong></p>
                        <p><?php echo htmlspecialchars($nominee['description']); ?></p>
                        
                        <?php if (!empty($nominee['logo'])): ?>
                        <p><strong>Logo:</strong></p>
                        <img src="../assets/images/<?php echo htmlspecialchars($nominee['logo']); ?>" 
                             alt="Nominee Logo" class="img-thumbnail" style="max-height: 150px;">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- EDIT NOMINEE FORM -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Edit Nominee</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nominee Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($nominee['name']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" class="form-control" id="slug" name="slug" 
                                       value="<?php echo htmlspecialchars($nominee['slug']); ?>" 
                                       placeholder="Auto-generated from name">
                                <div class="form-text">Will be auto-generated from name if left empty</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($nominee['description']); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category *</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                                <?php echo $nominee['category_id'] == $category['id'] ? 'selected' : ''; ?>>
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
                                                <?php echo $nominee['country_id'] == $country['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($country['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="file" class="form-control" id="logo" name="logo" accept=".jpg,.jpeg,.png,.gif,.svg">
                                <?php if (!empty($nominee['logo'])): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">Current logo: <?php echo htmlspecialchars($nominee['logo']); ?></small><br>
                                        <img src="../assets/images/<?php echo htmlspecialchars($nominee['logo']); ?>" 
                                             alt="Current Logo" class="img-thumbnail mt-1" style="max-height: 100px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="website_url" class="form-label">Website URL</label>
                                <input type="url" class="form-control" id="website_url" name="website_url" 
                                       value="<?php echo htmlspecialchars($nominee['website_url'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="nominee_type" class="form-label">Nominee Type</label>
                                <select class="form-select" id="nominee_type" name="nominee_type">
                                    <option value="organization" <?php echo $nominee['nominee_type'] === 'organization' ? 'selected' : ''; ?>>
                                        Organization
                                    </option>
                                    <option value="individual" <?php echo $nominee['nominee_type'] === 'individual' ? 'selected' : ''; ?>>
                                        Individual
                                    </option>
                                </select>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                       <?php echo $nominee['is_active'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" 
                                       <?php echo $nominee['is_featured'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_featured">Featured</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Update Nominee</button>
                        </form>
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