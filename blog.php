<?php
$page_title = "Blog - World Publications Awards";
include 'includes/header.php';
include 'includes/dbcon.inc.php';
require 'notifications/notify_voters.php';
require 'includes/fn.inc.php';
?>

<!-- Blog Hero Section -->
<section class="bg-dark text-white py-5">
    <div class="container text-center">
        <h1 class="fw-bold">Latest News & Updates</h1>
        <p class="lead mt-3">
            Stay informed with the latest news, updates, and insights from the World Publications Awards.
        </p>
    </div>
</section>

<!-- Blog Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <?php
                // Fetch published blog posts
                $stmt = $pdo->prepare("SELECT bp.*, u.username, u.email FROM blog_posts bp JOIN users u ON bp.author_id = u.id WHERE bp.is_published = 1 ORDER BY bp.published_at DESC LIMIT 10");
                $stmt->execute();
                $posts = $stmt->fetchAll();
                
                if ($posts):
                ?>
                    <!-- Featured Post -->
                    <?php if (isset($posts[0])): ?>
                        <article class="mb-5">
                            <div class="card">
                                <?php if ($posts[0]['featured_image']): ?>
                                    <img src="<?php echo htmlspecialchars($posts[0]['featured_image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($posts[0]['title']); ?>" style="height: 400px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="https://placehold.co/800x400?text=<?php echo urlencode(htmlspecialchars($posts[0]['title'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($posts[0]['title']); ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($posts[0]['category'] ?: 'News'); ?></span>
                                        <small class="text-muted"><?php echo date('F j, Y', strtotime($posts[0]['published_at'])); ?></small>
                                    </div>
                                    <h2 class="card-title"><?php echo htmlspecialchars($posts[0]['title']); ?></h2>
                                    <p class="card-text"><?php echo htmlspecialchars($posts[0]['excerpt']); ?></p>
                                    <?php 
                                    
                                        require_once 'encryption/encoder.php';
                                        $encodedId = salted_encode($posts[0]['id']);
                                    ?>
                                    <a href="read-post.php?id=<?php echo $encodedId; ?>" class="btn btn-warning">Read More</a>
                                </div>
                            </div>
                        </article>
                    <?php endif; ?>

                    <!-- Remaining Posts -->
                    <div class="row">
                        <?php for ($i = 1; $i < count($posts); $i++): ?>
                            <div class="col-md-6 mb-4">
                                <article>
                                    <div class="card h-100">
                                        <?php if ($posts[$i]['featured_image']): ?>
                                            <img src="<?php echo htmlspecialchars($posts[$i]['featured_image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($posts[$i]['title']); ?>" style="height: 200px; object-fit: cover;">
                                        <?php else: ?>
                                            <img src="https://placehold.co/400x200?text=<?php echo urlencode(htmlspecialchars($posts[$i]['title'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($posts[$i]['title']); ?>">
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="badge bg-primary"><?php echo htmlspecialchars($posts[$i]['category'] ?: 'News'); ?></span>
                                                <small class="text-muted"><?php echo date('M j, Y', strtotime($posts[$i]['published_at'])); ?></small>
                                            </div>
                                            <h3 class="card-title h5"><?php echo htmlspecialchars($posts[$i]['title']); ?></h3>
                                            <p class="card-text"><?php echo htmlspecialchars(substr($posts[$i]['excerpt'], 0, 120)) . (strlen($posts[$i]['excerpt']) > 120 ? '...' : ''); ?></p>
                                            
                                            
                                            <?php 
                                            require_once 'encryption/encoder.php';
                                            $encodedId = salted_encode($posts[$i]['id']);
                                            ?>
                                            <a href="read-post.php?id=<?php echo $encodedId; ?>" class="btn btn-sm btn-outline-primary">Read More</a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endfor; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <h4>No blog posts available</h4>
                        <p>Check back later for news and updates from the World Publications Awards.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Subscribe to Newsletter</h4>
                    </div>
                    <div class="card-body">
                        <p>Stay updated with the latest news and announcements.</p>

                        <?php
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
                                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                                
                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    // Check if email already exists
                                    $checkStmt = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
                                    $checkStmt->execute([$email]);
                                    
                                    if ($checkStmt->rowCount() > 0) {
                                        echo '<div class="alert alert-warning">You are already subscribed.</div>';
                                    } else {
                                        // Insert new subscriber
                                        $insertStmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
                                        if ($insertStmt->execute([$email])) {
                                            notifyVoters("Thank you for subscribing", "Welcome to the World Publications Awards newsletter! You will now receive updates on the latest news and announcements.");

                                            echo '<div class="alert alert-success">Thank you for subscribing!</div>';
                                            // Optionally, notify voters about the new blog post
                                        } else {
                                            echo '<div class="alert alert-danger">An error occurred. Please try again.</div>';
                                        }
                                    }
                                } else {
                                    echo '<div class="alert alert-danger">Please enter a valid email address.</div>';
                                }
                            }
                        ?>
                        <form action= "" method="post">
                            <div class="mb-3">
                                <input type="email" class="form-control" name="email" placeholder="Your email address" required>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">Subscribe</button>
                        </form>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Categories</h4>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php
                        // Get unique categories
                        $catStmt = $pdo->prepare("SELECT DISTINCT category FROM blog_posts WHERE is_published = 1 AND category IS NOT NULL ORDER BY category");
                        $catStmt->execute();
                        $categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
                        
                        foreach ($categories as $category):
                        ?>
                            <a href="?category=<?php echo urlencode($category); ?>" class="list-group-item list-group-item-action"><?php echo htmlspecialchars($category); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>Recent Posts</h4>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php
                        // Get recent posts
                        $recentStmt = $pdo->prepare("SELECT id, title FROM blog_posts WHERE is_published = 1 ORDER BY published_at DESC LIMIT 3");
                        $recentStmt->execute();
                        $recentPosts = $recentStmt->fetchAll();
                        
                        foreach ($recentPosts as $post):
                        ?>
                            <a href="read-post.php?id=<?php echo $post['id']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>