-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Feb 08, 2026 at 08:20 PM
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
(34, 15, '::1', 'thabo@gmail.com', 5.00, '2026-01-16 14:50:23', 'LESOTHO', 'LS'),
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
(73, 26, '::1', 'wezimosiuoa@gmail.com', 3.00, '2026-02-08 18:26:03', 'Localhost', 'LOCAL');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`nominee_id`) REFERENCES `nominees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
