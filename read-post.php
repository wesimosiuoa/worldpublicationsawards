<?php
$page_title = "Blog Post - World Publications Awards";
include 'includes/header.php';
include 'includes/dbcon.inc.php';
require_once 'encryption/decoder.php';


// Get post ID from URL
$postId = isset($_GET['id']) ? salted_decode($_GET['id']) : 0;

if ($postId <= 0) {
    header('Location: blog.php');
    exit;
}

// Fetch the blog post
$stmt = $pdo->prepare("SELECT bp.*, u.username, u.email FROM blog_posts bp JOIN users u ON bp.author_id = u.id WHERE bp.id = ? AND bp.is_published = 1");
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: blog.php');
    exit;
}

// Update page title
$page_title = htmlspecialchars($post['title']) . " - World Publications Awards";
?>

<!-- Blog Post Hero Section -->
<section class="bg-dark text-white py-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="blog.php">Blog</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($post['title']); ?></li>
            </ol>
        </nav>
        <h1 class="fw-bold"><?php echo htmlspecialchars($post['title']); ?></h1>
        <div class="d-flex align-items-center mt-3">
            <div class="me-3">
                <i class="fas fa-user me-1"></i>
                <span><?php echo htmlspecialchars($post['username']); ?></span>
            </div>
            <div class="me-3">
                <i class="fas fa-calendar me-1"></i>
                <span><?php echo date('F j, Y', strtotime($post['published_at'])); ?></span>
            </div>
            <div>
                <i class="fas fa-tag me-1"></i>
                <span><?php echo htmlspecialchars($post['category']); ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Blog Post Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <article>
                    <?php if ($post['featured_image']): ?>
                        <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" class="img-fluid mb-4" alt="<?php echo htmlspecialchars($post['title']); ?>" style="width: 100%; height: 400px; object-fit: cover;">
                    <?php endif; ?>
                    
                    <div class="blog-content">
                        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                    </div>
                    
                    <?php if ($post['tags']): ?>
                        <div class="mt-4">
                            <h5>Tags:</h5>
                            <?php
                            $tags = explode(',', $post['tags']);
                            foreach ($tags as $tag):
                            ?>
                                <span class="badge bg-secondary me-1"><?php echo trim(htmlspecialchars($tag)); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>
                
                <hr class="my-5">
                
                <div class="card mt-5">
                    <div class="card-body">
                        <h5 class="card-title">About the Author</h5>
                        <p class="card-text">
                            <i class="fas fa-user me-2"></i>
                            <strong><?php echo htmlspecialchars($post['username']); ?></strong>
                        </p>
                        <p class="card-text">
                            <i class="fas fa-calendar me-2"></i>
                            Published on <?php echo date('F j, Y \a\t g:i A', strtotime($post['published_at'])); ?>
                        </p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="blog.php" class="btn btn-outline-primary">&larr; Back to Blog</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>