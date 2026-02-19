<?php
include 'header.php';
// Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../includes/dbcon.inc.php';
include '../includes/messages.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'create_post') {
            // Create new blog post
            $title = trim($_POST['title']);
            $content = trim($_POST['content']);
            $excerpt = trim($_POST['excerpt']);
            $category = trim($_POST['category']);
            $tags = trim($_POST['tags']);
            $is_published = isset($_POST['is_published']) ? 1 : 0;
            
            // Generate slug from title
            $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower(trim($title)));
            
            // Check if slug already exists
            $checkSlug = $pdo->prepare("SELECT id FROM blog_posts WHERE slug = ?");
            $checkSlug->execute([$slug]);
            
            if ($checkSlug->rowCount() > 0) {
                // Append timestamp to make it unique
                $slug .= '-' . time();
            }
            
            // Handle featured image upload if provided
            $featured_image = null;
            if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
                $uploadDir = '../uploads/blog/';
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileName = time() . '_' . basename($_FILES['featured_image']['name']);
                $targetFile = $uploadDir . $fileName;
                
                // Validate and move uploaded file
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                $allowedTypes = ['jpg', 'png', 'jpeg', 'gif'];
                
                if (in_array($imageFileType, $allowedTypes)) {
                    if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $targetFile)) {
                        $featured_image = 'uploads/blog/' . $fileName;
                    } else {
                        setFlashMessage("Error uploading featured image.", 'error');
                    }
                } else {
                    setFlashMessage("Invalid image type. Only JPG, JPEG, PNG, and GIF files are allowed.", 'error');
                }
            }
            
            try {
                $stmt = $pdo->prepare("INSERT INTO blog_posts (title, slug, content, excerpt, featured_image, author_id, category, tags, is_published, published_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $published_at = $is_published ? date('Y-m-d H:i:s') : null;
                $stmt->execute([$title, $slug, $content, $excerpt, $featured_image, $_SESSION['user_id'], $category, $tags, $is_published, $published_at]);
                
                setFlashMessage("Blog post created successfully!", 'success');

                // require '../includes/fn.inc.php';
                // require_once '../notifications/notify_voters.php';
                
                // $subject = 'New Blog Post: ' . $title;
                // $message = 'A new blog post has been published on the World Publications Awards: ' . $title . '. Check it out and stay informed about the latest news!';
                // notifyVoters($subject, $message);
            } catch (PDOException $e) {
                setFlashMessage("Error creating blog post: " . $e->getMessage(), 'error');
            }
        } elseif ($_POST['action'] == 'update_post') {
            // Update existing blog post
            $post_id = (int)$_POST['post_id'];
            $title = trim($_POST['title']);
            $content = trim($_POST['content']);
            $excerpt = trim($_POST['excerpt']);
            $category = trim($_POST['category']);
            $tags = trim($_POST['tags']);
            $is_published = isset($_POST['is_published']) ? 1 : 0;
            
            // Generate slug from title
            $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower(trim($title)));
            
            // Check if slug already exists (excluding current post)
            $checkSlug = $pdo->prepare("SELECT id FROM blog_posts WHERE slug = ? AND id != ?");
            $checkSlug->execute([$slug, $post_id]);
            
            if ($checkSlug->rowCount() > 0) {
                // Append timestamp to make it unique
                $slug .= '-' . time();
            }
            
            // Handle featured image upload if provided
            $featured_image = null;
            if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
                $uploadDir = '../uploads/blog/';
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileName = time() . '_' . basename($_FILES['featured_image']['name']);
                $targetFile = $uploadDir . $fileName;
                
                // Validate and move uploaded file
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                $allowedTypes = ['jpg', 'png', 'jpeg', 'gif'];
                
                if (in_array($imageFileType, $allowedTypes)) {
                    if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $targetFile)) {
                        $featured_image = 'uploads/blog/' . $fileName;
                        
                        // Delete old image if exists
                        $getPost = $pdo->prepare("SELECT featured_image FROM blog_posts WHERE id = ?");
                        $getPost->execute([$post_id]);
                        $post = $getPost->fetch();
                        if ($post && $post['featured_image']) {
                            $oldImagePath = '../' . $post['featured_image'];
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }
                    } else {
                        setFlashMessage("Error uploading featured image.", 'error');
                    }
                } else {
                    setFlashMessage("Invalid image type. Only JPG, JPEG, PNG, and GIF files are allowed.", 'error');
                }
            }
            
            try {
                if ($featured_image) {
                    $stmt = $pdo->prepare("UPDATE blog_posts SET title = ?, slug = ?, content = ?, excerpt = ?, featured_image = ?, category = ?, tags = ?, is_published = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$title, $slug, $content, $excerpt, $featured_image, $category, $tags, $is_published, $post_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE blog_posts SET title = ?, slug = ?, content = ?, excerpt = ?, category = ?, tags = ?, is_published = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$title, $slug, $content, $excerpt, $category, $tags, $is_published, $post_id]);
                }
                
                setFlashMessage("Blog post updated successfully!", 'success');
            } catch (PDOException $e) {
                setFlashMessage("Error updating blog post: " . $e->getMessage(), 'error');
            }
        } elseif ($_POST['action'] == 'delete_post') {
            // Delete blog post
            $post_id = (int)$_POST['post_id'];
            
            try {
                // Get post to delete image file
                $getPost = $pdo->prepare("SELECT featured_image FROM blog_posts WHERE id = ?");
                $getPost->execute([$post_id]);
                $post = $getPost->fetch();
                
                $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
                $stmt->execute([$post_id]);
                
                // Delete image file if exists
                if ($post && $post['featured_image']) {
                    $imagePath = '../' . $post['featured_image'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                
                setFlashMessage("Blog post deleted successfully!", 'success');
            } catch (PDOException $e) {
                setFlashMessage("Error deleting blog post: " . $e->getMessage(), 'error');
            }
        }
    }
    
    header('Location: blog.php');
    exit;
}

// Fetch all blog posts
$stmt = $pdo->query("SELECT bp.*, u.username FROM blog_posts bp JOIN users u ON bp.author_id = u.id ORDER BY bp.created_at DESC");
$posts = $stmt->fetchAll();

// Handle edit post (if ID is in URL)
$editPost = null;
if (isset($_GET['edit'])) {
    $postId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->execute([$postId]);
    $editPost = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blog Posts - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Using Quill Editor instead of TinyMCE to avoid API key requirement -->
</head>
<body>
    
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Blog Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createPostModal">
                            <i class="fas fa-plus"></i> New Post
                        </button>
                    </div>
                </div>
                
                <?php displayFlashMessage(); ?>
                
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Published Date</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($post['id']); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($post['title']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars(substr($post['excerpt'], 0, 50)) . (strlen($post['excerpt']) > 50 ? '...' : ''); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($post['username']); ?></td>
                                <td><?php echo htmlspecialchars($post['category']); ?></td>
                                <td>
                                    <?php if ($post['is_published']): ?>
                                        <span class="badge bg-success">Published</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $post['published_at'] ? htmlspecialchars(date('M j, Y', strtotime($post['published_at']))) : '-'; ?></td>
                                <td><?php echo htmlspecialchars(date('M j, Y', strtotime($post['created_at']))); ?></td>
                                <td>
                                    <a href="?edit=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                        <input type="hidden" name="action" value="delete_post">
                                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Create/Edit Post Modal -->
    <div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createPostModalLabel"><?php echo $editPost ? 'Edit Post' : 'Create New Post'; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?php echo $editPost ? 'update_post' : 'create_post'; ?>">
                        <?php if ($editPost): ?>
                            <input type="hidden" name="post_id" value="<?php echo $editPost['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo $editPost ? htmlspecialchars($editPost['title']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-control" id="category" name="category">
                                <option value="">Select Category</option>
                                <option value="Award News" <?php echo ($editPost && $editPost['category'] == 'Award News') ? 'selected' : ''; ?>>Award News</option>
                                <option value="Voting" <?php echo ($editPost && $editPost['category'] == 'Voting') ? 'selected' : ''; ?>>Voting</option>
                                <option value="Nominees" <?php echo ($editPost && $editPost['category'] == 'Nominees') ? 'selected' : ''; ?>>Nominees</option>
                                <option value="Industry" <?php echo ($editPost && $editPost['category'] == 'Industry') ? 'selected' : ''; ?>>Industry</option>
                                <option value="Events" <?php echo ($editPost && $editPost['category'] == 'Events') ? 'selected' : ''; ?>>Events</option>
                                <option value="Other" <?php echo ($editPost && $editPost['category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="featured_image" class="form-label">Featured Image</label>
                            <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                            <?php if ($editPost && $editPost['featured_image']): ?>
                                <div class="mt-2">
                                    <p>Current image:</p>
                                    <img src="../<?php echo htmlspecialchars($editPost['featured_image']); ?>" alt="Current featured image" style="max-width: 200px; max-height: 150px;">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="excerpt" class="form-label">Excerpt</label>
                            <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?php echo $editPost ? htmlspecialchars($editPost['excerpt']) : ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="10"><?php echo $editPost ? htmlspecialchars($editPost['content']) : ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tags" class="form-label">Tags (comma separated)</label>
                            <input type="text" class="form-control" id="tags" name="tags" value="<?php echo $editPost ? htmlspecialchars($editPost['tags']) : ''; ?>">
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_published" name="is_published" <?php echo ($editPost && $editPost['is_published']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_published">
                                Publish Post
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><?php echo $editPost ? 'Update Post' : 'Create Post'; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple script to handle the modal state
        document.addEventListener('DOMContentLoaded', function() {
            // If we're in edit mode, show the modal
            <?php if ($editPost): ?>
                var modal = new bootstrap.Modal(document.getElementById('createPostModal'));
                modal.show();
            <?php endif; ?>
        });
    </script>
</body>
</html>