<?php
// Script to add blog_posts table to existing database
include 'includes/dbcon.inc.php';

try {
    // Create blog_posts table
    $sql = "CREATE TABLE IF NOT EXISTS blog_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        content LONGTEXT NOT NULL,
        excerpt TEXT,
        featured_image VARCHAR(255),
        author_id INT NOT NULL,
        category VARCHAR(100),
        tags TEXT,
        is_published TINYINT(1) DEFAULT 0,
        published_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "Table 'blog_posts' created successfully or already exists.<br>";
    
    // Insert sample blog posts if they don't exist
    $samplePosts = [
        [
            'title' => 'Celebrating Excellence in Global Journalism',
            'slug' => 'celebrating-excellence-global-journalism',
            'content' => 'This year\'s nominees showcase the very best in investigative reporting, creative storytelling, and digital innovation. Join us as we celebrate another year of outstanding contributions to the world of journalism and publications.',
            'excerpt' => 'Join us as we celebrate another year of outstanding contributions to the world of journalism and publications. This year\'s nominees showcase the very best in investigative reporting, creative storytelling, and digital innovation.',
            'author_id' => 1,
            'category' => 'Award News',
            'is_published' => 1
        ],
        [
            'title' => 'Voting Now Open for 2026 Awards',
            'slug' => 'voting-open-2026-awards',
            'content' => 'The voting period for this year\'s World Publications Awards is officially open. Cast your vote for the nominees that have made the biggest impact in the industry. Your vote counts towards recognizing excellence in journalism.',
            'excerpt' => 'The voting period for this year\'s World Publications Awards is officially open. Cast your vote for the nominees that have made the biggest impact in the industry.',
            'author_id' => 1,
            'category' => 'Voting',
            'is_published' => 1
        ],
        [
            'title' => 'Meet This Year\'s Outstanding Nominees',
            'slug' => 'outstanding-nominees-2026',
            'content' => 'We\'re proud to present this year\'s exceptional nominees, representing the pinnacle of journalistic excellence from around the world. These nominees have demonstrated extraordinary dedication to truth, accuracy, and public service.',
            'excerpt' => 'We\'re proud to present this year\'s exceptional nominees, representing the pinnacle of journalistic excellence from around the world.',
            'author_id' => 1,
            'category' => 'Nominees',
            'is_published' => 1
        ]
    ];
    
    foreach ($samplePosts as $post) {
        $checkPost = $pdo->prepare("SELECT id FROM blog_posts WHERE slug = ?");
        $checkPost->execute([$post['slug']]);
        
        if ($checkPost->rowCount() == 0) {
            $sql = "INSERT INTO blog_posts (title, slug, content, excerpt, author_id, category, is_published, published_at) VALUES (:title, :slug, :content, :excerpt, :author_id, :category, :is_published, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'title' => $post['title'],
                'slug' => $post['slug'],
                'content' => $post['content'],
                'excerpt' => $post['excerpt'],
                'author_id' => $post['author_id'],
                'category' => $post['category'],
                'is_published' => $post['is_published']
            ]);
        }
    }
    echo "Sample blog posts inserted or already exist.<br>";
    
    echo "Blog table update completed successfully!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>