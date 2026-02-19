-- SQL Migration: Create nominees_social_media_links table
-- Created: 2026-02-10
-- This table stores social media links for nominees

CREATE TABLE IF NOT EXISTS `nominees_social_media_links` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nominee_id` INT(11) NOT NULL,
  `platform_name` VARCHAR(100) NOT NULL COMMENT 'e.g., YouTube, Twitter, Facebook, Instagram, LinkedIn, TikTok, Website, Blog',
  `link` VARCHAR(500) NOT NULL COMMENT 'The full URL of the social media profile',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  -- Foreign key constraint
  CONSTRAINT `fk_nominees_social_media_nominee_id` 
    FOREIGN KEY (`nominee_id`) 
    REFERENCES `nominees` (`id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
  
  -- Unique constraint: one platform per nominee
  UNIQUE KEY `uk_nominee_platform` (`nominee_id`, `platform_name`),
  
  -- Index for faster queries
  KEY `idx_nominee_id` (`nominee_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note: Run this SQL query in your MySQL client or phpMyAdmin
-- Example execution:
-- 1. Open phpMyAdmin
-- 2. Select your 'wpa' database
-- 3. Go to SQL tab
-- 4. Copy and paste the CREATE TABLE query above
-- 5. Click Execute

-- To verify the table was created:
-- SELECT * FROM nominees_social_media_links;

-- To view the table structure:
-- DESCRIBE nominees_social_media_links;
