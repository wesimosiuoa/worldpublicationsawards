-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Feb 19, 2026 at 03:44 PM
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
-- Table structure for table `awards`
--

CREATE TABLE `awards` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `awards`
--

INSERT INTO `awards` (`id`, `name`, `description`, `year`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'World Publications Awards 2026', 'Annual World Publications Awards for Excellence in Journalism', '2026', 1, '2026-01-08 20:08:04', '2026-01-08 20:08:04'),
(2, 'FanD\'Or Awards 2026', 'FanD\'Or Awards 2026  FanD\'Or Awards 2026FanD\'Or Awards 2026FanD\'Or Awards 2026FanD\'Or Awards 2026FanD\'Or Awards 2026FanD\'Or Awards 2026FanD\'Or Awards 2026FanD\'Or Awards 2026FanD\'Or Awards 2026FanD\'Or Awards 2026', '2026', 1, '2026-02-16 20:36:53', '2026-02-16 20:36:53');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `excerpt` text DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `title`, `slug`, `content`, `excerpt`, `featured_image`, `author_id`, `category`, `tags`, `is_published`, `published_at`, `created_at`, `updated_at`) VALUES
(2, 'Voting Now Open for 2026 Awards', 'voting-open-2026-awards', 'The voting period for this year\'s World Publications Awards is officially open. Cast your vote for the nominees that have made the biggest impact in the industry. Your vote counts towards recognizing excellence in journalism.', 'The voting period for this year\'s World Publications Awards is officially open. Cast your vote for the nominees that have made the biggest impact in the industry.', NULL, 1, 'Voting', NULL, 1, '2026-02-05 07:25:14', '2026-02-05 07:25:14', '2026-02-05 07:25:14'),
(3, 'Meet This Year\'s Outstanding Nominees', 'outstanding-nominees-2026', 'We\'re proud to present this year\'s exceptional nominees, representing the pinnacle of journalistic excellence from around the world. These nominees have demonstrated extraordinary dedication to truth, accuracy, and public service.', 'We\'re proud to present this year\'s exceptional nominees, representing the pinnacle of journalistic excellence from around the world.', NULL, 1, 'Nominees', NULL, 1, '2026-02-05 07:25:14', '2026-02-05 07:25:14', '2026-02-05 07:25:14'),
(4, 'QALO', 'qalo', 'FFDFDFDFD', 'HELELE', 'uploads/blog/1770294557_2024-04-28.png', 1, 'Events', 'SEBONO, KE, TENG,', 1, '2026-02-05 07:59:18', '2026-02-05 12:29:18', '2026-02-05 12:45:34'),
(5, 'NTATA MOSHANYANA', 'ntata-moshanyana', 'QWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  dddQWadsdsd  ddd', 'WPA', 'uploads/blog/1770398016_2024-07-06 (1).png', 1, 'Award News', 'QWadsdsd  ddd, QWadsdsd  ddd, QWadsdsd  ddd,QWadsdsd  ddd', 1, '2026-02-06 12:43:36', '2026-02-06 17:13:36', '2026-02-06 17:13:36'),
(6, 'MALEFU', 'malefu', 'Secure PayPal Voting & Distribution System\r\nFor your World Publication Award where voters pay to vote and funds are distributed to nominees, here\'s a complete, secure PayPal integration:', '404', 'uploads/blog/1770581865_IMG-20251020-WA0038.jpg', 1, 'Nominees', 'QW, KL, KL, LK', 1, NULL, '2026-02-08 20:17:45', '2026-02-08 20:17:55'),
(7, 'Secure PayPal Voting & Distribution System', 'secure-paypal-voting-distribution-system', 'Secure PayPal Voting & Distribution System\r\nFor your World Publication Award where voters pay to vote and funds are distributed to nominees, here\'s a complete, secure PayPal integration:', 'Secure PayPal Voting', 'uploads/blog/1770582111_WhatsApp Image 2025-12-26 at 21.19.29.jpeg', 1, 'Voting', 'FD', 1, '2026-02-08 15:51:51', '2026-02-08 20:21:51', '2026-02-08 20:21:51'),
(8, 'Create New Post', 'create-new-post', 'Create New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New Post', 'Create New Post', 'uploads/blog/1770932997_IMG-20220903-WA0012.jpg', 1, 'Voting', 'Create, New, Post', 0, NULL, '2026-02-12 21:49:57', '2026-02-12 21:49:57'),
(9, 'Create New Post', 'create-new-post-1770933046', 'Create New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New Post', 'Create New Post', 'uploads/blog/1770933046_IMG-20220903-WA0012.jpg', 1, 'Voting', 'Create, New, Post', 0, NULL, '2026-02-12 21:50:46', '2026-02-12 21:50:46'),
(10, 'Create New Post', 'create-new-post-1770933291', 'Create New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New PostCreate New Post', 'Create New Post', 'uploads/blog/1770933291_IMG-20220903-WA0012.jpg', 1, 'Voting', 'Create, New, Post', 0, NULL, '2026-02-12 21:54:51', '2026-02-12 21:54:51'),
(11, 'dfsds', 'dfsds', '', '', NULL, 1, '', '', 0, NULL, '2026-02-12 21:55:07', '2026-02-12 21:55:07'),
(12, 'dfsds', 'dfsds-1770933381', '', '', NULL, 1, '', '', 0, NULL, '2026-02-12 21:56:21', '2026-02-12 21:56:21'),
(13, 'dfsds', 'dfsds-1770933419', '', '', NULL, 1, '', '', 0, NULL, '2026-02-12 21:56:59', '2026-02-12 21:56:59');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `award_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_type` enum('publication','journalist','organization','special') DEFAULT 'publication',
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `award_id`, `name`, `slug`, `description`, `category_type`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(50, 1, 'Journalist of the Year', 'journalist-of-the-year', 'Outstanding contribution to journalism', 'journalist', 1, 3, '2026-01-16 07:04:22', '2026-01-16 07:04:22'),
(51, 1, 'Investigative Reporting', 'investigative-reporting', 'Excellence in investigative journalism', 'publication', 1, 4, '2026-01-16 07:04:22', '2026-01-16 07:04:22'),
(52, 1, 'Photojournalism', 'photojournalism', 'Outstanding photojournalism work', 'publication', 1, 5, '2026-01-16 07:04:22', '2026-01-16 07:04:22'),
(53, 1, 'Digital Innovation', 'digital-innovation', 'Innovation in digital journalism', 'publication', 1, 6, '2026-01-16 07:04:22', '2026-01-16 07:04:22'),
(54, 1, 'Environmental Reporting', 'environmental-reporting', 'Excellence in environmental journalism', 'publication', 1, 7, '2026-01-16 07:04:22', '2026-01-16 07:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `iso_code` char(2) NOT NULL,
  `iso_code3` char(3) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `iso_code`, `iso_code3`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(182, 'Afghanistan', 'AF', 'AFG', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(183, 'Albania', 'AL', 'ALB', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(184, 'Algeria', 'DZ', 'DZA', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(185, 'Andorra', 'AD', 'AND', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(186, 'Angola', 'AO', 'AGO', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(187, 'Antigua and Barbuda', 'AG', 'ATG', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(188, 'Argentina', 'AR', 'ARG', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(189, 'Armenia', 'AM', 'ARM', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(190, 'Australia', 'AU', 'AUS', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(191, 'Austria', 'AT', 'AUT', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(192, 'Azerbaijan', 'AZ', 'AZE', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(193, 'Bahamas', 'BS', 'BHS', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(194, 'Bahrain', 'BH', 'BHR', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(195, 'Bangladesh', 'BD', 'BGD', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(196, 'Barbados', 'BB', 'BRB', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(197, 'Belarus', 'BY', 'BLR', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(198, 'Belgium', 'BE', 'BEL', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(199, 'Belize', 'BZ', 'BLZ', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(200, 'Benin', 'BJ', 'BEN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(201, 'Bhutan', 'BT', 'BTN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(202, 'Bolivia', 'BO', 'BOL', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(203, 'Bosnia and Herzegovina', 'BA', 'BIH', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(204, 'Botswana', 'BW', 'BWA', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(205, 'Brazil', 'BR', 'BRA', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(206, 'Brunei', 'BN', 'BRN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(207, 'Bulgaria', 'BG', 'BGR', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(208, 'Burkina Faso', 'BF', 'BFA', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(209, 'Burundi', 'BI', 'BDI', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(210, 'Cambodia', 'KH', 'KHM', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(211, 'Cameroon', 'CM', 'CMR', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(212, 'Canada', 'CA', 'CAN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(213, 'Cape Verde', 'CV', 'CPV', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(214, 'Central African Republic', 'CF', 'CAF', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(215, 'Chad', 'TD', 'TCD', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(216, 'Chile', 'CL', 'CHL', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(217, 'China', 'CN', 'CHN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(218, 'Colombia', 'CO', 'COL', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(219, 'Comoros', 'KM', 'COM', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(220, 'Congo', 'CG', 'COG', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(221, 'Costa Rica', 'CR', 'CRI', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(222, 'Croatia', 'HR', 'HRV', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(223, 'Cuba', 'CU', 'CUB', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(224, 'Cyprus', 'CY', 'CYP', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(225, 'Czech Republic', 'CZ', 'CZE', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(226, 'Denmark', 'DK', 'DNK', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(227, 'Djibouti', 'DJ', 'DJI', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(228, 'Dominica', 'DM', 'DMA', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(229, 'Dominican Republic', 'DO', 'DOM', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(230, 'Ecuador', 'EC', 'ECU', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(231, 'Egypt', 'EG', 'EGY', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(232, 'El Salvador', 'SV', 'SLV', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(233, 'Equatorial Guinea', 'GQ', 'GNQ', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(234, 'Eritrea', 'ER', 'ERI', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(235, 'Estonia', 'EE', 'EST', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(236, 'Eswatini', 'SZ', 'SWZ', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(237, 'Ethiopia', 'ET', 'ETH', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(238, 'Fiji', 'FJ', 'FJI', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(239, 'Finland', 'FI', 'FIN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(240, 'France', 'FR', 'FRA', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(241, 'Gabon', 'GA', 'GAB', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(242, 'Gambia', 'GM', 'GMB', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(243, 'Georgia', 'GE', 'GEO', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(244, 'Germany', 'DE', 'DEU', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(245, 'Ghana', 'GH', 'GHA', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(246, 'Greece', 'GR', 'GRC', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(247, 'Grenada', 'GD', 'GRD', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(248, 'Guatemala', 'GT', 'GTM', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(249, 'Guinea', 'GN', 'GIN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(250, 'Guinea-Bissau', 'GW', 'GNB', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(251, 'Guyana', 'GY', 'GUY', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(252, 'Haiti', 'HT', 'HTI', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(253, 'Honduras', 'HN', 'HND', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(254, 'Hungary', 'HU', 'HUN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(255, 'Iceland', 'IS', 'ISL', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(256, 'India', 'IN', 'IND', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(257, 'Indonesia', 'ID', 'IDN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(258, 'Iran', 'IR', 'IRN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(259, 'Iraq', 'IQ', 'IRQ', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(260, 'Ireland', 'IE', 'IRL', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(261, 'Israel', 'IL', 'ISR', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(262, 'Italy', 'IT', 'ITA', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(263, 'Jamaica', 'JM', 'JAM', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(264, 'Japan', 'JP', 'JPN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(265, 'Jordan', 'JO', 'JOR', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(266, 'Kazakhstan', 'KZ', 'KAZ', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(267, 'Kenya', 'KE', 'KEN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(268, 'Kiribati', 'KI', 'KIR', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(269, 'Kuwait', 'KW', 'KWT', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(270, 'Kyrgyzstan', 'KG', 'KGZ', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(271, 'Laos', 'LA', 'LAO', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(272, 'Latvia', 'LV', 'LVA', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(273, 'Lebanon', 'LB', 'LBN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(274, 'Lesotho', 'LS', 'LSO', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(275, 'Liberia', 'LR', 'LBR', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(276, 'Libya', 'LY', 'LBY', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(277, 'Liechtenstein', 'LI', 'LIE', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(278, 'Lithuania', 'LT', 'LTU', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(279, 'Luxembourg', 'LU', 'LUX', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(280, 'Madagascar', 'MG', 'MDG', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(281, 'Malawi', 'MW', 'MWI', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(282, 'Malaysia', 'MY', 'MYS', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(283, 'Maldives', 'MV', 'MDV', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(284, 'Mali', 'ML', 'MLI', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(285, 'Malta', 'MT', 'MLT', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(286, 'Marshall Islands', 'MH', 'MHL', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(287, 'Mauritania', 'MR', 'MRT', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(288, 'Mauritius', 'MU', 'MUS', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(289, 'Mexico', 'MX', 'MEX', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(290, 'Micronesia', 'FM', 'FSM', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(291, 'Moldova', 'MD', 'MDA', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(292, 'Monaco', 'MC', 'MCO', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(293, 'Mongolia', 'MN', 'MNG', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(294, 'Montenegro', 'ME', 'MNE', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(295, 'Morocco', 'MA', 'MAR', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(296, 'Mozambique', 'MZ', 'MOZ', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(297, 'Myanmar', 'MM', 'MMR', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(298, 'Namibia', 'NA', 'NAM', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(299, 'Nepal', 'NP', 'NPL', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(300, 'Netherlands', 'NL', 'NLD', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(301, 'New Zealand', 'NZ', 'NZL', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(302, 'Nicaragua', 'NI', 'NIC', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(303, 'Niger', 'NE', 'NER', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(304, 'Nigeria', 'NG', 'NGA', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(305, 'North Macedonia', 'MK', 'MKD', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(306, 'Norway', 'NO', 'NOR', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(307, 'Oman', 'OM', 'OMN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(308, 'Pakistan', 'PK', 'PAK', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(309, 'Panama', 'PA', 'PAN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(310, 'Papua New Guinea', 'PG', 'PNG', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(311, 'Paraguay', 'PY', 'PRY', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(312, 'Peru', 'PE', 'PER', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(313, 'Philippines', 'PH', 'PHL', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(314, 'Poland', 'PL', 'POL', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(315, 'Portugal', 'PT', 'PRT', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(316, 'Qatar', 'QA', 'QAT', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(317, 'Romania', 'RO', 'ROU', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(318, 'Russia', 'RU', 'RUS', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(319, 'Rwanda', 'RW', 'RWA', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(320, 'Saudi Arabia', 'SA', 'SAU', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(321, 'Senegal', 'SN', 'SEN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(322, 'Serbia', 'RS', 'SRB', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(323, 'Seychelles', 'SC', 'SYC', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(324, 'Sierra Leone', 'SL', 'SLE', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(325, 'Singapore', 'SG', 'SGP', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(326, 'Slovakia', 'SK', 'SVK', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(327, 'Slovenia', 'SI', 'SVN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(328, 'South Africa', 'ZA', 'ZAF', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(329, 'South Korea', 'KR', 'KOR', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(330, 'South Sudan', 'SS', 'SSD', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(331, 'Spain', 'ES', 'ESP', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(332, 'Sri Lanka', 'LK', 'LKA', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(333, 'Sudan', 'SD', 'SDN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(334, 'Suriname', 'SR', 'SUR', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(335, 'Sweden', 'SE', 'SWE', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(336, 'Switzerland', 'CH', 'CHE', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(337, 'Syria', 'SY', 'SYR', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(338, 'Taiwan', 'TW', 'TWN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(339, 'Tajikistan', 'TJ', 'TJK', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(340, 'Tanzania', 'TZ', 'TZA', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(341, 'Thailand', 'TH', 'THA', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(342, 'Togo', 'TG', 'TGO', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(343, 'Tonga', 'TO', 'TON', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(344, 'Trinidad and Tobago', 'TT', 'TTO', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(345, 'Tunisia', 'TN', 'TUN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(346, 'Turkey', 'TR', 'TUR', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(347, 'Turkmenistan', 'TM', 'TKM', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(348, 'Uganda', 'UG', 'UGA', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(349, 'Ukraine', 'UA', 'UKR', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(350, 'United Arab Emirates', 'AE', 'ARE', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(351, 'United Kingdom', 'GB', 'GBR', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(352, 'United States', 'US', 'USA', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(353, 'Uruguay', 'UY', 'URY', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(354, 'Uzbekistan', 'UZ', 'UZB', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(355, 'Vanuatu', 'VU', 'VUT', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(356, 'Vatican City', 'VA', 'VAT', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(357, 'Venezuela', 'VE', 'VEN', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(358, 'Vietnam', 'VN', 'VNM', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(359, 'Yemen', 'YE', 'YEM', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(360, 'Zambia', 'ZM', 'ZMB', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13'),
(361, 'Zimbabwe', 'ZW', 'ZWE', 1, 0, '2026-01-08 19:55:13', '2026-01-08 19:55:13');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `newsletter_subscribers`
--

INSERT INTO `newsletter_subscribers` (`id`, `email`) VALUES
(4, 'wezimosiuoa@gmail.com');

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
  `email` varchar(200) NOT NULL,
  `contact_person_email` varchar(200) NOT NULL,
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

INSERT INTO `nominees` (`id`, `name`, `description`, `category_id`, `country_id`, `email`, `contact_person_email`, `slug`, `logo`, `website_url`, `nominee_type`, `is_active`, `is_featured`, `total_votes`, `total_amount_raised`, `created_at`, `updated_at`) VALUES
(11, 'Lesotho Times', 'this is perfect fine', 54, 196, 'wezimosiuoa@gmail.com', 'wezimosiuoa@gmail.com', 'lesotho-times', '6969e6ba0c7a5_Screenshot 2025-10-08 200812.png', 'http://localhost/worldpublicationawards/admin/nominees.php', 'individual', 1, 1, 14, 38.00, '2026-01-16 07:18:49', '2026-02-10 07:59:49'),
(15, 'Tribune', 'fine ifen', 53, 195, 'wezimosiuoa@wtit.com', 'wezimosiuoa@wtit.com', 'tribune', '481899230_536469076132096_3437832106723279604_n.jpg', 'https://www.google.com/search?q=internet&ie=UTF-8', 'individual', 1, 1, 11, 36.00, '2026-01-16 08:04:40', '2026-02-16 20:32:53'),
(16, 'Moafrika Radio TV', 'http://localhost/phpmyadmin/index.php?route=/sql&pos=0&db=wpa&table=countries', 54, 190, 'admin@1234.com', 'wezimosiuoa@wtit.com', 'moafrika-radio-tv', '6969e6ba0c7a5_Screenshot 2025-10-08 200812.png', 'http://localhost/phpmyadmin/index.php?route=/sql&pos=0&db=wpa&table=countries', 'individual', 1, 0, 13, 39.00, '2026-01-17 07:12:46', '2026-02-12 12:26:38'),
(17, 'Global News Network', 'An international news organization delivering unbiased global coverage.', 1, 182, '', '', 'global-news-network', '6969e6ba0c7a5_Screenshot 2025-10-08 200812.png', 'https://globalnews.example', 'organization', 1, 1, 3, 11.00, '2026-01-17 07:22:19', '2026-02-08 11:07:26'),
(18, 'Albania Media Group', 'Leading independent media house in Albania.', 1, 183, '', '', 'albania-media-group', '6969e6ba0c7a5_Screenshot 2025-10-08 200812.png', 'https://albaniamedia.example', 'organization', 1, 0, 1, 5.00, '2026-01-17 07:22:19', '2026-01-17 11:04:29'),
(19, 'Algeria Press Today', 'Digital-first journalism platform covering North Africa.', 2, 184, '', '', 'algeria-press-today', '6969e6ba0c7a5_Screenshot 2025-10-08 200812.png', 'https://algeriapress.example', 'organization', 1, 1, 8, 24.00, '2026-01-17 07:22:19', '2026-02-08 20:13:49'),
(20, 'Andorra Times', 'Regional publication focusing on European policy and culture.', 2, 185, '', '', 'andorra-times', '481899230_536469076132096_3437832106723279604_n.jpg', 'https://andorratimes.example', 'organization', 1, 0, 2, 6.00, '2026-01-17 07:22:19', '2026-02-16 20:32:50'),
(21, 'Angola Investigates', 'Investigative journalism initiative uncovering corruption.', 3, 186, '', '', 'angola-investigates', '6969e6ba0c7a5_Screenshot 2025-10-08 200812.png', 'https://angolainvestigates.example', 'organization', 1, 1, 0, 0.00, '2026-01-17 07:22:19', '2026-01-17 11:04:18'),
(22, 'Caribbean Voice', 'Independent Caribbean media organization.', 3, 187, '', '', 'caribbean-voice', '6969e6ba0c7a5_Screenshot 2025-10-08 200812.png', 'https://caribbeanvoice.example', 'organization', 1, 0, 0, 0.00, '2026-01-17 07:22:19', '2026-01-17 11:04:18'),
(23, 'Argentina Journal', 'Award-winning South American journalism platform.', 1, 188, '', '', 'argentina-journal', '6969e6ba0c7a5_Screenshot 2025-10-08 200812.png', 'https://argentinajournal.example', 'organization', 1, 1, 1, 3.00, '2026-01-17 07:22:19', '2026-02-09 21:14:32'),
(24, 'Armenia Insight', 'Nonprofit newsroom covering politics and social issues.', 2, 189, '', '', 'armenia-insight', '6969e6ba0c7a5_Screenshot 2025-10-08 200812.png', 'https://armeniainsight.example', 'organization', 1, 0, 0, 0.00, '2026-01-17 07:22:19', '2026-01-17 11:04:18'),
(25, 'Australian Review', 'National publication with global reach.', 1, 190, '', '', 'australian-review', '6969e6ba0c7a5_Screenshot 2025-10-08 200812.png', 'https://australianreview.example', 'organization', 1, 1, 1, 3.00, '2026-01-17 07:22:19', '2026-02-09 21:14:20'),
(26, 'Global Correspondent – Jane Doe', 'Independent journalist reporting from conflict zones.', 3, 188, '', '', 'jane-doe-global-correspondent', '6969e6ba0c7a5_Screenshot 2025-10-08 200812.png', 'https://janedoe.example', 'individual', 1, 0, 1, 3.00, '2026-01-17 07:22:19', '2026-02-08 18:26:03'),
(27, 'FORBES', 'https://chatgpt.com/g/g-p-695b7339b5f0819198c9276d9ab266e2/c/695b74d9-3efc-8324-9327-f97fe1cb0d54https://chatgpt.com/g/g-p-695b7339b5f0819198c9276d9ab266e2/c/695b74d9-3efc-8324-9327-f97fe1cb0d54', 50, 274, 'teboho@gmail.com', 'teboho@gmail.com', 'forbes', '696e2b25233ac_Screenshot 2025-10-13 221929.png', 'https://chatgpt.com/g/g-p-695b7339b5f0819198c9276d9ab266e2/c/695b74d9-3efc-8324-9327-f97fe1cb0d54', 'individual', 1, 1, 5, 15.00, '2026-01-19 13:01:25', '2026-02-08 11:09:26'),
(28, 'TJATJI', 'tjatjiramathalea007@gmail.com', 53, 274, 'tjatjiramathalea007@gmail.com', 'tjatjiramathalea007@gmail.com', 'TJATJI', '698499a1b2e92_Screenshot 2025-10-08 200812.png', 'https://chatgpt.com/g/g-p-695b7339b5f0819198c9276d9ab266e2/c/695b74d9-3efc-8324-9327-f97fe1cb0d54', 'individual', 1, 1, 5, 15.00, '2026-02-05 13:22:41', '2026-02-08 11:05:19'),
(29, 'THE POST OF INDIA', 'If you want the vote percentage to update instantly without reload, we can later add:\r\n\r\nAJAX refresh of nominee stats\r\n\r\nOr auto page reload after success\r\n\r\nBut your core system is now correct.', 53, 227, 'sb-8ysze48355569@personal.example.com', 'sb-8ysze48355569@personal.example.com', 'the-post-of-india', '481899230_536469076132096_3437832106723279604_n.jpg', 'http://localhost/phpmyadmin/index.php?route=/sql&pos=0&db=wpa&table=categories', 'individual', 1, 0, 1, 3.00, '2026-02-12 12:33:17', '2026-02-16 20:34:07'),
(32, 'MOELETSI WA BASOTHO', '324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in324207316021@andhrauniversity.edu.in', 54, 192, '324207316021@andhrauniversity.edu.in', '324207316021@andhrauniversity.edu.in', 'moeletsi-wa-basotho', '698e49271fa9b_IMG-20211216-WA0084.jpg', 'http://localhost/phpmyadmin/index.php?route=/sql&pos=0&db=wpa&table=countries', 'individual', 1, 0, 0, 0.00, '2026-02-12 21:41:59', '2026-02-12 21:41:59');

-- --------------------------------------------------------

--
-- Table structure for table `nominees_social_media_links`
--

CREATE TABLE `nominees_social_media_links` (
  `platform_id` int(11) NOT NULL,
  `platform_name` varchar(100) DEFAULT NULL,
  `link` text DEFAULT NULL,
  `nominee_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nominees_social_media_links`
--

INSERT INTO `nominees_social_media_links` (`platform_id`, `platform_name`, `link`, `nominee_id`) VALUES
(20, 'YouTube', 'http://localhost/worldpublicationawards/nominees/profile.php', 11),
(21, 'Instagram', 'http://localhost/worldpublicationawards/nominees/profile.php', 11),
(22, 'YouTube', 'http://localhost/worldpublicationawards/nominees/profile.php', 29),
(23, 'Instagram', 'http://localhost/worldpublicationawards/nominees/profile.php', 32),
(24, 'Twitter', 'http://localhost/worldpublicationawards/nominees/profile.php', 32),
(25, 'LinkedIn', 'http://localhost/worldpublicationawards/nominees/profile.php', 32),
(26, 'Website', 'http://localhost/worldpublicationawards/nominees/profile.php', 32),
(27, 'TikTok', 'http://localhost/worldpublicationawards/nominees/profile.php', 32),
(28, 'Facebook', 'http://localhost/worldpublicationawards/nominees/profile.php', 11),
(29, 'Website', 'http://localhost/worldpublicationawards/nominees/profile.php', 11),
(30, 'TikTok', 'http://localhost/worldpublicationawards/nominees/profile.php', 11),
(31, 'LinkedIn', 'http://localhost/worldpublicationawards/nominees/profile.php', 11),
(32, 'Twitter', 'http://localhost/worldpublicationawards/nominees/profile.php', 11);

-- --------------------------------------------------------

--
-- Table structure for table `otp_tokens`
--

CREATE TABLE `otp_tokens` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used` tinyint(1) DEFAULT 0,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `otp_tokens`
--

INSERT INTO `otp_tokens` (`id`, `email`, `otp`, `expires_at`, `used`, `used_at`, `created_at`) VALUES
(51, 'tjatjiramathalea007@gmail.com', '459358', '2026-02-05 13:24:08', 1, '2026-02-05 07:54:08', '2026-02-05 13:22:58'),
(63, '324207316021@andhrauniversity.edu.in', '787960', '2026-02-13 07:13:58', 1, '2026-02-13 01:43:58', '2026-02-13 07:13:28'),
(64, 'wezimosiuoa@gmail.com', '272500', '2026-02-16 20:13:43', 1, '2026-02-16 14:43:43', '2026-02-16 20:13:25');

-- --------------------------------------------------------

--
-- Table structure for table `paypal_transactions`
--

CREATE TABLE `paypal_transactions` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) DEFAULT NULL,
  `nominee_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paypal_transactions`
--

INSERT INTO `paypal_transactions` (`id`, `order_id`, `nominee_id`, `amount`, `status`, `created_at`) VALUES
(1, '51J04792D7620032C', 15, 3.00, 'COMPLETED', '2026-02-12 11:55:40'),
(2, '44A434811A969872S', 15, 3.00, 'COMPLETED', '2026-02-12 11:58:17'),
(3, '5YN092711K8433117', 15, 3.00, 'COMPLETED', '2026-02-12 11:59:11'),
(4, '3WC49522TY731991B', 15, 3.00, 'COMPLETED', '2026-02-12 12:08:00'),
(5, '86E226578R606272V', 15, 3.00, 'COMPLETED', '2026-02-12 12:09:23'),
(6, '7C156259YX8253846', 15, 3.00, 'COMPLETED', '2026-02-12 12:11:20'),
(7, '9F2508832P6399201', 15, 3.00, 'COMPLETED', '2026-02-12 12:12:21'),
(8, '3PB30955DH8049003', 16, 3.00, 'COMPLETED', '2026-02-12 12:16:25'),
(9, '182572252X845615V', 16, 3.00, 'COMPLETED', '2026-02-12 12:18:03'),
(10, '9D4832851X7883836', 16, 3.00, 'COMPLETED', '2026-02-12 12:23:52'),
(11, '7RT3958108450125R', 16, 3.00, 'COMPLETED', '2026-02-12 12:26:38'),
(12, '3TF90718261852338', 29, 3.00, 'COMPLETED', '2026-02-12 12:34:28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user','stakeholder','nominee') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@worldpublicationawards.org', '$2y$10$cl1yqmwmYgqNLeAS5KU47eUN9gCzKWcfd9L/yiM0wWEEv8PTbAZeu', 'admin', '2026-01-08 19:48:45', '2026-02-05 12:05:23'),
(2, 'MOSIUOA WESI', 'wezimosiuoa@gmail.com', '$2y$10$ZKqSDTV2cRJT8V8P.jnbOuVDnoquH87ja7r09Le8ZE6efk5ih/0O2', 'nominee', '2026-01-16 07:08:14', '2026-02-08 19:54:15'),
(3, 'tjatjiramathalea007', 'tjatjiramathalea007@gmail.com', '$2y$10$NQmmW95cE7RIGH5QkZLO/eY9a6nQG.h.8IOwR5dj9NuqkJ5ntnoDC', 'nominee', '2026-02-05 13:22:58', '2026-02-08 11:15:48'),
(4, '324207316021', '324207316021@andhrauniversity.edu.in', '$2y$10$LyGvBmwAhHUm1cdZirKKgOPu93dKUWFe7vm0zTcXbpW87MMFU8XWu', 'nominee', '2026-02-13 07:13:28', '2026-02-13 07:13:28');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `nominee_id` int(11) NOT NULL,
  `voter_ip` varchar(45) DEFAULT NULL,
  `voter_email` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `voter_country` varchar(100) DEFAULT NULL,
  `voter_country_code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `nominee_id`, `voter_ip`, `voter_email`, `amount`, `created_at`, `voter_country`, `voter_country_code`) VALUES
(34, 15, '0.0.0.0', 'thabo@gmail.com', 5.00, '2026-01-16 14:50:23', 'LESOTHO', 'LS'),
(35, 11, '::1', 'wezimosiuoa@gmail.com', 5.00, '2026-01-16 14:51:23', 'SOUTH AFRICA', 'SA'),
(36, 18, '::1', '', 5.00, '2026-01-17 11:04:29', 'BOTSOANA', 'BW'),
(37, 17, '::1', 'wezimosiuoa@gmail.com', 5.00, '2026-01-17 11:04:57', 'INDIA', 'IN'),
(38, 15, '::1', 'thabo@gmail.com', 5.00, '2026-01-17 11:05:30', 'UNITED KINGDOM', 'UK'),
(39, 15, '::1', 'admin@1234.com', 5.00, '2026-01-17 14:04:39', 'UNITED STATE', 'US'),
(40, 28, '::1', 'tjatjiramathalea007@gmail.com', 3.00, '2026-02-06 17:16:21', 'UNITED ARAB', 'UAE'),
(41, 11, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 06:30:32', 'NAMIBIA', 'NM'),
(42, 11, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 06:30:42', 'LESOTHO ', 'LS'),
(43, 11, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 06:30:51', 'UNITED STATE ', 'US'),
(44, 16, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 06:31:03', 'INDIA', 'IN'),
(45, 16, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 06:31:10', NULL, NULL),
(46, 16, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 06:31:17', NULL, NULL),
(47, 16, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 06:31:25', NULL, NULL),
(48, 16, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 06:32:27', NULL, NULL),
(49, 16, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 06:32:45', NULL, NULL),
(50, 16, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 06:34:02', NULL, NULL),
(51, 16, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 06:34:19', NULL, NULL),
(52, 16, '::1', 'wezimosiuoa@wtit.com', 3.00, '2026-02-08 06:37:42', NULL, NULL),
(53, 11, '::1', NULL, 3.00, '2026-02-08 07:21:50', 'Localhost', 'LOCAL'),
(54, 11, 'wezimosiuoa@gmail.com', '3', 0.00, '2026-02-08 07:24:52', 'Localhost', 'LOCAL'),
(55, 11, 'wezimosiuoa@gmail.com', '3', 0.00, '2026-02-08 07:24:57', 'Localhost', 'LOCAL'),
(56, 11, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 07:26:09', 'Localhost', 'LOCAL'),
(57, 11, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 07:26:15', 'Localhost', 'LOCAL'),
(58, 11, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 10:59:33', 'Localhost', 'LOCAL'),
(59, 11, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 11:02:21', 'Localhost', 'LOCAL'),
(60, 11, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 11:04:26', 'Localhost', 'LOCAL'),
(61, 11, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 11:04:36', 'Localhost', 'LOCAL'),
(62, 28, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 11:05:03', 'Localhost', 'LOCAL'),
(63, 28, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 11:05:08', 'Localhost', 'LOCAL'),
(64, 28, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 11:05:14', 'Localhost', 'LOCAL'),
(65, 28, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 11:05:19', 'Localhost', 'LOCAL'),
(66, 17, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 11:07:21', 'Localhost', 'LOCAL'),
(67, 17, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 11:07:26', 'Localhost', 'LOCAL'),
(68, 27, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 11:08:53', 'Localhost', 'LOCAL'),
(69, 27, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 11:09:00', 'Localhost', 'LOCAL'),
(70, 27, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 11:09:05', 'Localhost', 'LOCAL'),
(71, 27, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 11:09:16', 'Localhost', 'LOCAL'),
(72, 27, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 11:09:26', 'Localhost', 'LOCAL'),
(73, 26, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 18:26:03', 'Localhost', 'LOCAL'),
(74, 19, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 20:13:13', 'Localhost', 'LOCAL'),
(75, 19, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 20:13:34', 'Localhost', 'LOCAL'),
(76, 19, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 20:13:37', 'Localhost', 'LOCAL'),
(77, 19, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 20:13:40', 'Localhost', 'LOCAL'),
(78, 19, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 20:13:42', 'Localhost', 'LOCAL'),
(79, 19, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 20:13:44', 'Localhost', 'LOCAL'),
(80, 19, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 20:13:47', 'Localhost', 'LOCAL'),
(81, 19, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 20:13:49', 'Localhost', 'LOCAL'),
(82, 20, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 20:14:01', 'Localhost', 'LOCAL'),
(83, 20, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 20:14:09', 'Localhost', 'LOCAL'),
(84, 25, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-09 21:14:20', 'Localhost', 'LOCAL'),
(85, 23, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-09 21:14:32', 'Localhost', 'LOCAL'),
(86, 11, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-10 07:59:49', 'Localhost', 'LOCAL'),
(87, 15, '::1', NULL, NULL, '2026-02-10 19:16:04', 'Localhost', 'LOCAL'),
(88, 15, '::1', 'sb-47kaaj48939442@business.example.com', 3.00, '2026-02-12 11:55:40', NULL, NULL),
(89, 15, '::1', 'sb-47kaaj48939442@business.example.com', 3.00, '2026-02-12 11:58:17', NULL, NULL),
(90, 15, '::1', 'sb-47kaaj48939442@business.example.com', 3.00, '2026-02-12 11:59:11', NULL, NULL),
(91, 15, '::1', 'sb-47kaaj48939442@business.example.com', 3.00, '2026-02-12 12:07:59', NULL, NULL),
(92, 15, '::1', 'sb-47kaaj48939442@business.example.com', 3.00, '2026-02-12 12:09:23', NULL, NULL),
(93, 15, '::1', 'sb-47kaaj48939442@business.example.com', 3.00, '2026-02-12 12:11:20', 'US', 'US'),
(94, 15, '::1', 'sb-47kaaj48939442@business.example.com', 3.00, '2026-02-12 12:12:21', 'US', 'US'),
(95, 16, '::1', 'sb-m43fg149399066@personal.example.com', 3.00, '2026-02-12 12:16:25', 'LS', 'LS'),
(96, 16, '::1', 'sb-m43fg149399066@personal.example.com', 3.00, '2026-02-12 12:18:03', 'LS', 'LS'),
(97, 16, '::1', 'sb-8ysze48355569@personal.example.com', 3.00, '2026-02-12 12:23:52', 'DJ', 'DJ'),
(98, 16, '::1', 'sb-8ysze48355569@personal.example.com', 3.00, '2026-02-12 12:26:38', 'DJ', 'DJ'),
(99, 29, '::1', 'sb-8ysze48355569@personal.example.com', 3.00, '2026-02-12 12:34:28', 'DJ', 'DJ');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `awards`
--
ALTER TABLE `awards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_category_per_award` (`award_id`,`slug`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `iso_code` (`iso_code`),
  ADD UNIQUE KEY `iso_code3` (`iso_code3`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nominees`
--
ALTER TABLE `nominees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `nominees_social_media_links`
--
ALTER TABLE `nominees_social_media_links`
  ADD PRIMARY KEY (`platform_id`),
  ADD KEY `fk_nominee_id` (`nominee_id`);

--
-- Indexes for table `otp_tokens`
--
ALTER TABLE `otp_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_otp` (`otp`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `paypal_transactions`
--
ALTER TABLE `paypal_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `f_nominee_id` (`nominee_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nominee_id` (`nominee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `awards`
--
ALTER TABLE `awards`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=366;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `nominees`
--
ALTER TABLE `nominees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `nominees_social_media_links`
--
ALTER TABLE `nominees_social_media_links`
  MODIFY `platform_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `otp_tokens`
--
ALTER TABLE `otp_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `paypal_transactions`
--
ALTER TABLE `paypal_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_award` FOREIGN KEY (`award_id`) REFERENCES `awards` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nominees_social_media_links`
--
ALTER TABLE `nominees_social_media_links`
  ADD CONSTRAINT `fk_nominee_id` FOREIGN KEY (`nominee_id`) REFERENCES `nominees` (`id`);

--
-- Constraints for table `paypal_transactions`
--
ALTER TABLE `paypal_transactions`
  ADD CONSTRAINT `f_nominee_id` FOREIGN KEY (`nominee_id`) REFERENCES `nominees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`nominee_id`) REFERENCES `nominees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
