-- SQL Schema for Blog Posts Table
CREATE TABLE IF NOT EXISTS blog_posts (
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
);

-- Insert sample blog posts
INSERT INTO blog_posts (title, slug, content, excerpt, author_id, category, is_published, published_at) VALUES
('Celebrating Excellence in Global Journalism', 'celebrating-excellence-global-journalism', 
'This year''s nominees showcase the very best in investigative reporting, creative storytelling, and digital innovation. Join us as we celebrate another year of outstanding contributions to the world of journalism and publications.',
'Join us as we celebrate another year of outstanding contributions to the world of journalism and publications. This year''s nominees showcase the very best in investigative reporting, creative storytelling, and digital innovation.',
1, 'Award News', 1, NOW()),
('Voting Now Open for 2026 Awards', 'voting-open-2026-awards',
'The voting period for this year''s World Publications Awards is officially open. Cast your vote for the nominees that have made the biggest impact in the industry. Your vote counts towards recognizing excellence in journalism.',
'The voting period for this year''s World Publications Awards is officially open. Cast your vote for the nominees that have made the biggest impact in the industry.',
1, 'Voting', 1, NOW()),
('Meet This Year''s Outstanding Nominees', 'outstanding-nominees-2026',
'We''re proud to present this year''s exceptional nominees, representing the pinnacle of journalistic excellence from around the world. These nominees have demonstrated extraordinary dedication to truth, accuracy, and public service.',
'We''re proud to present this year''s exceptional nominees, representing the pinnacle of journalistic excellence from around the world.',
1, 'Nominees', 1, NOW());