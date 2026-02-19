<?php
// Database Setup Script for World Publications Awards

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wpa";

try {
    // Create connection to MySQL (without selecting database)
    $pdo = new PDO("mysql:host=$servername", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sql);
    echo "Database '$dbname' created successfully or already exists.<br>";
    
    // Select the database
    $pdo->exec("USE $dbname");
    
    // Create awards table first (for foreign key references)
    $sql = "CREATE TABLE IF NOT EXISTS awards (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        year YEAR,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "Table 'awards' created successfully or already exists.<br>";
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user', 'stakeholder', 'nominee') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "Table 'users' created successfully or already exists.<br>";
    
    // Create categories table
    $sql = "CREATE TABLE IF NOT EXISTS categories (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        award_id INT UNSIGNED NOT NULL,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL,
        description TEXT,
        category_type ENUM('publication','journalist','organization','special') DEFAULT 'publication',
        is_active TINYINT(1) DEFAULT 1,
        display_order INT UNSIGNED DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_category_per_award (award_id, slug),
        CONSTRAINT fk_categories_award FOREIGN KEY (award_id) REFERENCES awards(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "Table 'categories' created successfully or already exists.<br>";
    
    // Create countries table
    $sql = "CREATE TABLE IF NOT EXISTS countries (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        iso_code CHAR(2) NOT NULL UNIQUE,
        iso_code3 CHAR(3) UNIQUE,
        is_active TINYINT(1) DEFAULT 1,
        display_order INT UNSIGNED DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "Table 'countries' created successfully or already exists.<br>";
    
    // Create nominees table
    $sql = "CREATE TABLE IF NOT EXISTS nominees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        category_id INT NOT NULL,
        country_id INT NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        logo VARCHAR(255),
        website_url VARCHAR(255),
        email VARCHAR(100),
        contact_person_email VARCHAR(100),
        user_id INT NULL,
        nominee_type ENUM('organization', 'individual') DEFAULT 'organization',
        is_active TINYINT(1) DEFAULT 1,
        is_featured TINYINT(1) DEFAULT 0,
        total_votes INT DEFAULT 0,
        total_amount_raised DECIMAL(10,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "Table 'nominees' created successfully or already exists.<br>";
    
    // Create votes table
    $sql = "CREATE TABLE IF NOT EXISTS votes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nominee_id INT NOT NULL,
        voter_ip VARCHAR(45),
        voter_email VARCHAR(100),
        amount DECIMAL(10,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (nominee_id) REFERENCES nominees(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "Table 'votes' created successfully or already exists.<br>";
    
    // Create otp_tokens table
    $sql = "CREATE TABLE IF NOT EXISTS otp_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL,
        otp VARCHAR(6) NOT NULL,
        expires_at TIMESTAMP NOT NULL,
        used TINYINT(1) DEFAULT 0,
        used_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_otp (otp),
        INDEX idx_expires_at (expires_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "Table 'otp_tokens' created successfully or already exists.<br>";
    
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
    
    // Insert default admin user (username: admin, password: admin123)
    $default_admin_username = 'admin';
    $default_admin_email = 'admin@worldpublicationawards.org';
    $default_admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $checkAdmin = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $checkAdmin->execute([$default_admin_username]);
    
    if ($checkAdmin->rowCount() == 0) {
        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$default_admin_username, $default_admin_email, $default_admin_password]);
        echo "Default admin user created: Username: $default_admin_username, Password: admin123<br>";
    } else {
        echo "Admin user already exists.<br>";
    }
    
    // Insert sample awards if they don't exist
    $awards = [
        ['name' => 'World Publications Awards 2026', 'description' => 'Annual World Publications Awards for Excellence in Journalism', 'year' => 2026]
    ];
    
    foreach ($awards as $award) {
        $checkAward = $pdo->prepare("SELECT id FROM awards WHERE name = ?");
        $checkAward->execute([$award['name']]);
        
        if ($checkAward->rowCount() == 0) {
            $sql = "INSERT INTO awards (name, description, year) VALUES (:name, :description, :year)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($award);
        }
    }
    echo "Sample awards inserted.<br>";
    
    // Insert sample categories
    $categories = [
        ['award_id' => 1, 'name' => 'Journalist of the Year', 'slug' => 'journalist-of-the-year', 'description' => 'Outstanding contribution to journalism', 'category_type' => 'journalist', 'is_active' => 1, 'display_order' => 3],
        ['award_id' => 1, 'name' => 'Investigative Reporting', 'slug' => 'investigative-reporting', 'description' => 'Excellence in investigative journalism', 'category_type' => 'publication', 'is_active' => 1, 'display_order' => 4],
        ['award_id' => 1, 'name' => 'Photojournalism', 'slug' => 'photojournalism', 'description' => 'Outstanding photojournalism work', 'category_type' => 'publication', 'is_active' => 1, 'display_order' => 5],
        ['award_id' => 1, 'name' => 'Digital Innovation', 'slug' => 'digital-innovation', 'description' => 'Innovation in digital journalism', 'category_type' => 'publication', 'is_active' => 1, 'display_order' => 6],
        ['award_id' => 1, 'name' => 'Environmental Reporting', 'slug' => 'environmental-reporting', 'description' => 'Excellence in environmental journalism', 'category_type' => 'publication', 'is_active' => 1, 'display_order' => 7]
    ];
    
    foreach ($categories as $category) {
        $checkCat = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
        $checkCat->execute([$category['name']]);
        
        if ($checkCat->rowCount() == 0) {
            $sql = "INSERT INTO categories (award_id, name, slug, description, category_type, is_active, display_order) VALUES (:award_id, :name, :slug, :description, :category_type, :is_active, :display_order)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($category);
        }
    }
    echo "Sample categories inserted.<br>";
    
    // Insert sample countries
    $countries = [
        ['name' => 'United States', 'iso_code' => 'US', 'iso_code3' => 'USA'],
        ['name' => 'United Kingdom', 'iso_code' => 'UK', 'iso_code3' => 'GBR'],
        ['name' => 'Canada', 'iso_code' => 'CA', 'iso_code3' => 'CAN'],
        ['name' => 'Australia', 'iso_code' => 'AU', 'iso_code3' => 'AUS'],
        ['name' => 'Germany', 'iso_code' => 'DE', 'iso_code3' => 'DEU'],
        ['name' => 'France', 'iso_code' => 'FR', 'iso_code3' => 'FRA'],
        ['name' => 'Japan', 'iso_code' => 'JP', 'iso_code3' => 'JPN'],
        ['name' => 'South Africa', 'iso_code' => 'ZA', 'iso_code3' => 'ZAF'],
        ['name' => 'Brazil', 'iso_code' => 'BR', 'iso_code3' => 'BRA'],
        ['name' => 'India', 'iso_code' => 'IN', 'iso_code3' => 'IND']
    ];
    
    foreach ($countries as $country) {
        $checkCountry = $pdo->prepare("SELECT id FROM countries WHERE iso_code = ?");
        $checkCountry->execute([$country['iso_code']]);
        
        if ($checkCountry->rowCount() == 0) {
            $sql = "INSERT INTO countries (name, iso_code, iso_code3) VALUES (:name, :iso_code, :iso_code3)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($country);
        }
    }
    echo "Sample countries inserted.<br>";
    
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
    echo "Sample blog posts inserted.<br>";
    
    // Add foreign key constraints after all tables are created
    try {
        $pdo->exec("ALTER TABLE nominees ADD CONSTRAINT fk_nominees_category_id FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE");
        echo "Foreign key constraint for category_id added successfully.<br>";
    } catch (PDOException $e) {
        echo "Error adding category foreign key: " . $e->getMessage() . "<br>";
    }
    
    try {
        $pdo->exec("ALTER TABLE nominees ADD CONSTRAINT fk_nominees_country_id FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE CASCADE");
        echo "Foreign key constraint for country_id added successfully.<br>";
    } catch (PDOException $e) {
        echo "Error adding country foreign key: " . $e->getMessage() . "<br>";
    }
    
    echo "<br><strong>Database setup completed successfully!</strong>";

} catch(PDOException $e) {
    die("Error creating database or tables: " . $e->getMessage());
}

// Create dbcon.inc.php file
$dbconContent = '<?php
// Database connection configuration for World Publication Awards
$servername = "localhost";
$username = "root";  // Default XAMPP MySQL username
$password = "";      // Default XAMPP MySQL password (empty)
$dbname = "wpa";    // Database name for World Publication Awards
$port = 3307;       // MySQL port (changed from default 3306)

try {
    $pdo = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Uncomment the line below if you want to confirm connection in development
    // echo "Connected successfully to WPA database on port $port";
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>';

file_put_contents(__DIR__ . '/includes/dbcon.inc.php', $dbconContent);
echo "<br><br>Database connection file created.";
?>