<?php
include 'header.php';

$action = $_GET['action'] ?? '';
$category_id = $_GET['id'] ?? 0;

$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        // Add new category
        $award_id = (int)$_POST['award_id'];
        $name = trim($_POST['name']);
        $slug = trim($_POST['slug']);
        $description = trim($_POST['description']);
        $category_type = $_POST['category_type'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $display_order = (int)$_POST['display_order'];

        if (empty($name) || empty($slug)) {
            $message = 'Category name and slug are required.';
            $message_type = 'error';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO categories (award_id, name, slug, description, category_type, is_active, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $result = $stmt->execute([$award_id, $name, $slug, $description, $category_type, $is_active, $display_order]);

                if ($result) {
                    $message = 'Category added successfully.';
                    $message_type = 'success';
                } else {
                    $message = 'Error adding category.';
                    $message_type = 'error';
                }
            } catch (PDOException $e) {
                $message = 'Error: ' . $e->getMessage();
                $message_type = 'error';
            }
        }
    } elseif (isset($_POST['edit_category'])) {
        // Update category
        $category_id = (int)$_POST['category_id'];
        $award_id = (int)$_POST['award_id'];
        $name = trim($_POST['name']);
        $slug = trim($_POST['slug']);
        $description = trim($_POST['description']);
        $category_type = $_POST['category_type'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $display_order = (int)$_POST['display_order'];

        if (empty($name) || empty($slug)) {
            $message = 'Category name and slug are required.';
            $message_type = 'error';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE categories SET award_id=?, name=?, slug=?, description=?, category_type=?, is_active=?, display_order=? WHERE id=?");
                $result = $stmt->execute([$award_id, $name, $slug, $description, $category_type, $is_active, $display_order, $category_id]);

                if ($result) {
                    $message = 'Category updated successfully.';
                    $message_type = 'success';
                } else {
                    $message = 'Error updating category.';
                    $message_type = 'error';
                }
            } catch (PDOException $e) {
                $message = 'Error: ' . $e->getMessage();
                $message_type = 'error';
            }
        }
    } elseif (isset($_POST['delete_category'])) {
        // Delete category
        $category_id = (int)$_POST['category_id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id=?");
            $result = $stmt->execute([$category_id]);

            if ($result) {
                $message = 'Category deleted successfully.';
                $message_type = 'success';
            } else {
                $message = 'Error deleting category.';
                $message_type = 'error';
            }
        } catch (PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// Fetch categories with award names
$categories_stmt = $pdo->query("
    SELECT c.*, a.name as award_name 
    FROM categories c 
    LEFT JOIN awards a ON c.award_id = a.id 
    ORDER BY c.display_order, c.name
");
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch awards for dropdown
$awards_stmt = $pdo->query("SELECT id, name FROM awards ORDER BY name");
$awards = $awards_stmt->fetchAll(PDO::FETCH_ASSOC);

// If editing, fetch category data
$edit_category = null;
if ($action === 'edit' && $category_id) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $edit_category = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories - Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2>Manage Categories</h2>
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $message_type === 'error' ? 'danger' : 'success'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <!-- Add/Edit Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><?php echo $action === 'edit' && $edit_category ? 'Edit Category' : 'Add New Category'; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="award_id" class="form-label">Award</label>
                                        <select name="award_id" id="award_id" class="form-control" required>
                                            <?php foreach ($awards as $award): ?>
                                                <option value="<?php echo $award['id']; ?>" 
                                                    <?php echo ($edit_category && $edit_category['award_id'] == $award['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($award['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Category Name</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo $edit_category ? htmlspecialchars($edit_category['name']) : ''; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="slug" class="form-label">Slug (URL-friendly)</label>
                                        <input type="text" class="form-control" id="slug" name="slug" 
                                               value="<?php echo $edit_category ? htmlspecialchars($edit_category['slug']) : ''; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="category_type" class="form-label">Category Type</label>
                                        <select name="category_type" id="category_type" class="form-control" required>
                                            <option value="publication" <?php echo ($edit_category && $edit_category['category_type'] === 'publication') ? 'selected' : ''; ?>>Publication</option>
                                            <option value="journalist" <?php echo ($edit_category && $edit_category['category_type'] === 'journalist') ? 'selected' : ''; ?>>Journalist</option>
                                            <option value="organization" <?php echo ($edit_category && $edit_category['category_type'] === 'organization') ? 'selected' : ''; ?>>Organization</option>
                                            <option value="special" <?php echo ($edit_category && $edit_category['category_type'] === 'special') ? 'selected' : ''; ?>>Special</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo $edit_category ? htmlspecialchars($edit_category['description']) : ''; ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="display_order" class="form-label">Display Order</label>
                                        <input type="number" class="form-control" id="display_order" name="display_order" 
                                               value="<?php echo $edit_category ? $edit_category['display_order'] : '0'; ?>">
                                    </div>
                                    
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                               <?php echo (!$edit_category || $edit_category['is_active']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" name="category_id" value="<?php echo $edit_category ? $edit_category['id'] : ''; ?>">
                            <button type="submit" name="<?php echo $action === 'edit' && $edit_category ? 'edit_category' : 'add_category'; ?>" class="btn btn-primary">
                                <?php echo $action === 'edit' && $edit_category ? 'Update Category' : 'Add Category'; ?>
                            </button>
                            
                            <?php if ($action === 'edit' && $edit_category): ?>
                                <a href="categories.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Categories List -->
                <div class="card">
                    <div class="card-header">
                        <h5>All Categories</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Award</th>
                                        <th>Type</th>
                                        <th>Active</th>
                                        <th>Order</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?php echo $category['id']; ?></td>
                                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                                            <td><?php echo htmlspecialchars($category['award_name'] ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $category['category_type'] === 'publication' ? 'primary' : ($category['category_type'] === 'journalist' ? 'success' : ($category['category_type'] === 'organization' ? 'info' : 'secondary')); ?>">
                                                    <?php echo ucfirst($category['category_type']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $category['is_active'] ? 'Yes' : 'No'; ?></td>
                                            <td><?php echo $category['display_order']; ?></td>
                                            <td>
                                                <a href="?action=edit&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form method="POST" action="" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                                    <button type="submit" name="delete_category" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>