-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2026 at 10:12 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wpa`
--

-- --------------------------------------------------------

--
-- Table structure for table `nominees`
--

CREATE TABLE `nominees` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `nominee_type` enum('organization','individual') DEFAULT 'organization',
  `is_active` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `total_votes` int(11) DEFAULT 0,
  `total_amount_raised` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nominees`
--

INSERT INTO `nominees` (`id`, `name`, `description`, `category_id`, `country_id`, `slug`, `logo`, `website_url`, `nominee_type`, `is_active`, `is_featured`, `total_votes`, `total_amount_raised`, `created_at`, `updated_at`) VALUES
(1, 'Jane Doe', 'Award-winning investigative editor known for impactful global reporting.', 1, 3, 'jane-doe-editor', 'uploads/nominees/jane_doe.png', 'https://janedoejournalism.com', 'individual', 1, 1, 1245, 1867.50, '2026-01-13 08:43:14', '2026-01-13 08:43:14'),
(2, 'John Smith', 'International journalist covering politics and social justice.', 2, 1, 'john-smith-journalist', 'uploads/nominees/john_smith.png', 'https://johnsmithreports.com', 'individual', 1, 0, 980, 1470.00, '2026-01-13 08:43:14', '2026-01-13 08:43:14'),
(3, 'International Journal of Science', 'Leading peer-reviewed academic journal with global reach.', 3, 3, 'international-journal-of-science', 'uploads/nominees/ijs.png', 'https://www.ijsjournal.org', '', 1, 1, 760, 1140.00, '2026-01-13 08:43:14', '2026-01-13 08:43:14'),
(4, 'ESPN Sports Weekly', 'Global sports magazine delivering in-depth analysis and coverage.', 4, 1, 'espn-sports-weekly', 'uploads/nominees/espn.png', 'https://www.espn.com', '', 1, 0, 1340, 2010.00, '2026-01-13 08:43:14', '2026-01-13 08:43:14'),
(5, 'Financial Times', 'World-renowned business newspaper with international influence.', 5, 3, 'financial-times', 'uploads/nominees/ft.png', 'https://www.ft.com', '', 1, 1, 2100, 3150.00, '2026-01-13 08:43:14', '2026-01-13 08:43:14'),
(6, 'TechCrunch', 'Leading technology publication covering startups and innovation.', 6, 1, 'techcrunch', 'uploads/nominees/techcrunch.png', 'https://techcrunch.com', '', 1, 0, 1890, 2835.00, '2026-01-13 08:43:14', '2026-01-13 08:43:14'),
(7, 'The Morning Brief', 'Daily newsletter delivering concise global news insights.', 7, 2, 'the-morning-brief', 'uploads/nominees/morning_brief.png', 'https://morningbrief.news', '', 1, 0, 640, 960.00, '2026-01-13 08:43:14', '2026-01-13 08:43:14'),
(8, 'Vogue India', 'Premier fashion magazine showcasing global and regional trends.', 8, 2, 'vogue-india', 'uploads/nominees/vogue_india.png', 'https://www.vogue.in', '', 1, 1, 1520, 2280.00, '2026-01-13 08:43:14', '2026-01-13 08:43:14'),
(9, 'National Geographic', 'Iconic magazine focused on science, exploration, and storytelling.', 9, 1, 'national-geographic', 'uploads/nominees/natgeo.png', 'https://www.nationalgeographic.com', '', 1, 1, 2650, 3975.00, '2026-01-13 08:43:14', '2026-01-13 08:43:14'),
(10, 'The Guardian', 'International newspaper recognized for independent journalism.', 10, 3, 'the-guardian', 'uploads/nominees/guardian.png', 'https://www.theguardian.com', '', 1, 1, 2400, 3600.00, '2026-01-13 08:43:14', '2026-01-13 08:43:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `nominees`
--
ALTER TABLE `nominees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nominees`
--
ALTER TABLE `nominees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
