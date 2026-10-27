-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2026 at 12:12 AM
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
-- Database: `noorstaffing`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(120) NOT NULL,
  `entity_type` varchar(120) DEFAULT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `details_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details_json`)),
  `ip_hash` char(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `admin_user_id`, `action`, `entity_type`, `entity_id`, `details_json`, `ip_hash`, `created_at`) VALUES
(1, 1, 'admin_logout', 'admin_users', 1, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', '2026-10-27 20:14:54'),
(2, 1, 'admin_login', 'admin_users', 1, '{\"email\":\"admin@noorstaffing.com\"}', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', '2026-10-27 20:15:08');

-- --------------------------------------------------------

--
-- Table structure for table `admin_roles`
--

CREATE TABLE `admin_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_key` varchar(60) NOT NULL,
  `role_name` varchar(120) NOT NULL,
  `permissions_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions_json`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_roles`
--

INSERT INTO `admin_roles` (`id`, `role_key`, `role_name`, `permissions_json`, `created_at`) VALUES
(1, 'super_admin', 'Super Administrator', '[\"*\"]', '2026-10-27 19:23:52'),
(2, 'editor', 'Content Editor', '[\"pages.read\", \"pages.write\", \"media.write\", \"stories.write\"]', '2026-10-27 19:23:52'),
(3, 'recruiter', 'Recruiter', '[\"jobs.write\", \"applications.read\"]', '2026-10-27 19:23:52');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(160) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('active','disabled','locked') NOT NULL DEFAULT 'active',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `role_id`, `full_name`, `email`, `password_hash`, `status`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'System Super Admin', 'admin@noorstaffing.com', '$2y$10$MZb47ycoHPPSq8GgjKKKg.BobeLtcb7REmJbGilQj3VTGcElT3Fhi', 'active', '2026-10-28 02:15:08', '2026-10-27 19:48:11', '2026-10-27 20:15:08');

-- --------------------------------------------------------

--
-- Table structure for table `analytics_visits`
--

CREATE TABLE `analytics_visits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `page_url` varchar(255) NOT NULL,
  `ip_hash` char(64) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `visited_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `analytics_visits`
--

INSERT INTO `analytics_visits` (`id`, `page_url`, `ip_hash`, `user_agent`, `referrer`, `visited_at`) VALUES
(1, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/contact.php', '2026-10-27 20:02:21'),
(2, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/contact.php', '2026-10-27 20:02:24'),
(3, '/nsg/about.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php', '2026-10-27 20:03:38'),
(4, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:03:41'),
(5, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:04:20'),
(6, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:04:22'),
(7, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:09:01'),
(8, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:10:00'),
(9, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:10:37'),
(10, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:12:45'),
(11, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:14:52'),
(12, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:16:20'),
(13, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:16:32'),
(14, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:26:24'),
(15, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:27:44'),
(16, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:28:24'),
(17, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:28:25'),
(18, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:28:44'),
(19, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:30:50'),
(20, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:30:51'),
(21, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-27 20:30:55'),
(22, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:31:08'),
(23, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:35:23'),
(24, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:41:32'),
(25, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:44:21'),
(26, '/nsg/index.php', 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:44:54'),
(27, '/nsg/index.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:49:02'),
(28, '/nsg/index.php?lang=bn', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php', '2026-10-27 20:49:13'),
(29, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-27 20:49:14'),
(30, '/nsg/index.php?lang=bn', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=en', '2026-10-27 20:49:15'),
(31, '/nsg/index.php?lang=bn', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=en', '2026-10-27 20:52:33'),
(32, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-27 20:52:38'),
(33, '/nsg/index.php?lang=bn', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=en', '2026-10-27 20:52:39'),
(34, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-27 20:52:40'),
(35, '/nsg/index.php?lang=bn', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=en', '2026-10-27 20:52:41'),
(36, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-27 20:52:47'),
(37, '/nsg/index.php?lang=bn', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=en', '2026-10-27 20:52:51'),
(38, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-27 20:52:56'),
(39, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=en', '2026-10-27 20:53:45'),
(40, '/nsg/about.php?lang=bn', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:53:47'),
(41, '/nsg/about.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php?lang=bn', '2026-10-27 20:53:54'),
(42, '/nsg/about.php?lang=bn', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php?lang=en', '2026-10-27 20:53:57'),
(43, '/nsg/about.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php?lang=bn', '2026-10-27 20:55:36'),
(44, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php?lang=en', '2026-10-27 20:55:37'),
(45, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php?lang=en', '2026-10-27 20:56:24'),
(46, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 20:56:31'),
(47, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:56:32'),
(48, '/nsg/people.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 20:56:33'),
(49, '/nsg/board.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/people.php', '2026-10-27 20:56:36'),
(50, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/board.php', '2026-10-27 20:56:38'),
(51, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:56:39'),
(52, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 20:56:41'),
(53, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 20:56:42'),
(54, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 21:02:47'),
(55, '/nsg/about.php?lang=bn', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 21:02:50'),
(56, '/nsg/about.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php?lang=bn', '2026-10-27 21:02:52'),
(57, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php?lang=en', '2026-10-27 21:04:01'),
(58, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 21:04:02'),
(59, '/nsg/index.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 21:04:09'),
(60, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php', '2026-10-27 21:04:11'),
(61, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 21:06:01'),
(62, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 21:06:32'),
(63, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 21:06:56'),
(64, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 21:06:57'),
(65, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 21:06:58'),
(66, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 21:07:25'),
(67, '/nsg/careers.php?lang=bn', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 21:07:36'),
(68, '/nsg/careers.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php?lang=bn', '2026-10-27 21:07:40'),
(69, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php?lang=en', '2026-10-27 21:10:43'),
(70, '/nsg/people.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 21:12:48'),
(71, '/nsg/board.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/people.php', '2026-10-27 21:12:54'),
(72, '/nsg/people.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/board.php', '2026-10-27 21:12:59'),
(73, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/people.php', '2026-10-27 21:13:01'),
(74, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/people.php', '2026-10-27 21:13:21'),
(75, '/nsg/people.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 21:13:23'),
(76, '/nsg/board.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/people.php', '2026-10-27 21:13:46'),
(77, '/nsg/contact.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/board.php', '2026-10-27 21:14:46'),
(78, '/nsg/people.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/contact.php', '2026-10-27 21:23:16'),
(79, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/people.php', '2026-10-27 21:25:09'),
(80, '/nsg/careers.php?q=drop+shoulder&department=&type=', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 21:25:15'),
(81, '/nsg/careers.php?q=drop+shoulder&department=&type=', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php?q=drop+shoulder&department=&type=', '2026-10-27 21:25:18'),
(82, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php?q=drop+shoulder&department=&type=', '2026-10-27 21:25:55'),
(83, '/nsg/contact.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 21:27:11'),
(84, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/contact.php', '2026-10-27 21:32:50'),
(85, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/contact.php', '2026-10-27 21:33:24'),
(86, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 21:33:25'),
(87, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 21:35:00'),
(88, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 21:35:01'),
(89, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 21:37:10'),
(90, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 21:37:22'),
(91, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 21:37:25'),
(92, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 21:37:26'),
(93, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 21:37:27'),
(94, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 21:37:28'),
(95, '/nsg/board.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 21:38:34'),
(96, '/nsg/board.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 21:38:43'),
(97, '/nsg/board.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 21:40:24'),
(98, '/nsg/people.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/board.php', '2026-10-27 21:42:15'),
(99, '/nsg/people.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/board.php', '2026-10-27 21:42:19'),
(100, '/nsg/people.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/board.php', '2026-10-27 21:42:21'),
(101, '/nsg/people.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/board.php', '2026-10-27 21:42:22'),
(102, '/nsg/index.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/people.php', '2026-10-27 21:42:28'),
(103, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php', '2026-10-27 21:44:30'),
(104, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 21:44:48'),
(105, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-27 22:14:13'),
(106, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/', '2026-10-27 22:14:15'),
(107, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/', '2026-10-27 22:14:50'),
(108, '/nsg/board.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 22:20:46'),
(109, '/nsg/board.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 22:20:48'),
(110, '/nsg/board.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 22:25:22'),
(111, '/nsg/people.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/board.php', '2026-10-27 22:25:24'),
(112, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/people.php', '2026-10-27 22:29:19'),
(113, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/people.php', '2026-10-27 22:32:17'),
(114, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/people.php', '2026-10-27 22:32:20'),
(115, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/people.php', '2026-10-27 22:35:06'),
(116, '/nsg/contact.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 22:35:08'),
(117, '/nsg/index.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/contact.php', '2026-10-27 22:37:05'),
(118, '/nsg/job.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-27 22:50:55'),
(119, '/nsg/contact.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/job.php', '2026-10-27 22:51:02'),
(120, '/nsg/index.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/contact.php', '2026-10-27 22:51:03'),
(121, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php', '2026-10-27 22:51:03'),
(122, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 22:51:04'),
(123, '/nsg/contact.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 22:51:09'),
(124, '/nsg/board.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/contact.php', '2026-10-27 22:51:09'),
(125, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/board.php', '2026-10-27 22:51:11'),
(126, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/board.php', '2026-10-27 23:10:02'),
(127, '/nsg/contact.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 23:10:34');

-- --------------------------------------------------------

--
-- Table structure for table `board_members`
--

CREATE TABLE `board_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(160) NOT NULL,
  `title` varchar(160) NOT NULL,
  `biography` text DEFAULT NULL,
  `photo_path` varchar(500) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(160) NOT NULL,
  `email` varchar(190) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `ip_hash` char(64) DEFAULT NULL,
  `status` enum('new','read','replied','archived') NOT NULL DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_memories`
--

CREATE TABLE `employee_memories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(220) NOT NULL,
  `event_date` date DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `photo_path` varchar(500) DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_of_month`
--

CREATE TABLE `employee_of_month` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(160) NOT NULL,
  `role` varchar(160) NOT NULL,
  `month_label` varchar(80) NOT NULL,
  `quote` text DEFAULT NULL,
  `photo_path` varchar(500) DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_stories`
--

CREATE TABLE `employee_stories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(220) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `body` longtext NOT NULL,
  `featured_media_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entity_translations`
--

CREATE TABLE `entity_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_type` varchar(60) NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `field_name` varchar(60) NOT NULL,
  `locale` char(2) NOT NULL DEFAULT 'bn',
  `translation` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(220) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `location` varchar(160) NOT NULL,
  `employment_type` varchar(80) NOT NULL DEFAULT 'Full-time',
  `department` varchar(120) DEFAULT NULL,
  `summary` text NOT NULL,
  `description` longtext NOT NULL,
  `salary_min` decimal(12,2) DEFAULT NULL,
  `salary_max` decimal(12,2) DEFAULT NULL,
  `currency` char(3) DEFAULT NULL,
  `closing_date` date DEFAULT NULL,
  `status` enum('draft','published','closed','archived') NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

CREATE TABLE `job_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(160) NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `cover_letter` text DEFAULT NULL,
  `cv_path` varchar(500) NOT NULL,
  `status` enum('received','reviewing','shortlisted','interview','offered','rejected','withdrawn') NOT NULL DEFAULT 'received',
  `consent_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `mime_type` varchar(120) NOT NULL,
  `file_size` bigint(20) UNSIGNED NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `navigation_items`
--

CREATE TABLE `navigation_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `label` varchar(120) NOT NULL,
  `url` varchar(500) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `navigation_items`
--

INSERT INTO `navigation_items` (`id`, `parent_id`, `label`, `url`, `sort_order`, `is_active`) VALUES
(1, NULL, 'Home', 'index.php', 1, 1),
(2, NULL, 'About Us', 'about.php', 2, 1),
(3, NULL, 'Careers', 'careers.php', 3, 1),
(4, NULL, 'Our People', 'people.php', 4, 1),
(5, NULL, 'Leadership', 'board.php', 5, 1),
(6, NULL, 'Contact', 'contact.php', 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(220) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `body_html` longtext DEFAULT NULL,
  `hero_image` varchar(500) DEFAULT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` varchar(500) DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `published_at` datetime DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `title`, `slug`, `body_html`, `hero_image`, `seo_title`, `seo_description`, `status`, `created_at`, `updated_at`, `published_at`, `created_by`, `updated_by`) VALUES
(1, 'About Us Page', 'about', NULL, NULL, '', '', 'published', '2026-10-28 04:14:03', '2026-10-28 04:15:00', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `page_sections`
--

CREATE TABLE `page_sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `page_slug` varchar(120) NOT NULL,
  `section_key` varchar(120) NOT NULL,
  `content_type` enum('text','html','image','color','number') DEFAULT 'text',
  `content_value` longtext DEFAULT NULL,
  `content_value_bn` longtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `page_sections`
--

INSERT INTO `page_sections` (`id`, `page_slug`, `section_key`, `content_type`, `content_value`, `content_value_bn`, `updated_at`) VALUES
(1, 'about', 'about_heading', 'text', 'About Noor Staffing Group', NULL, '2026-10-27 22:15:00'),
(2, 'about', 'about_heading_color', 'color', '#111111', NULL, '2026-10-27 22:15:00'),
(3, 'about', 'about_heading_size', 'text', '3rem', NULL, '2026-10-27 22:15:00'),
(4, 'about', 'about_intro', '', 'Bridging talent gaps and delivering offshore operational excellence.', NULL, '2026-10-27 22:15:00'),
(5, 'about', 'about_intro_color', 'color', '#555555', NULL, '2026-10-27 22:15:00'),
(6, 'about', 'about_intro_size', 'text', '1.15rem', NULL, '2026-10-27 22:15:00'),
(7, 'about', 'about_story_title', 'text', 'Our Journey & Strategic Value', NULL, '2026-10-27 22:15:00'),
(8, 'about', 'about_story_title_color', 'color', '#111111', NULL, '2026-10-27 22:15:00'),
(9, 'about', 'about_story_body', '', 'Noor Staffing Group in Bangladesh operates as a vital strategic extension of our US operations.', NULL, '2026-10-27 22:15:00'),
(10, 'about', 'about_story_body_color', 'color', '#444444', NULL, '2026-10-27 22:15:00'),
(11, 'about', 'about_page_image', 'image', 'assets/dhaka-city.jpg', NULL, '2026-10-27 22:15:00'),
(12, 'about', 'management_section_title', 'text', 'Message From Senior Management', NULL, '2026-10-27 22:15:00'),
(13, 'about', 'management_section_title_color', 'color', '#111111', NULL, '2026-10-27 22:15:00'),
(14, 'about', 'about_mission', '', 'To bridge talent gaps with high-value placement solutions.', NULL, '2026-10-27 22:15:00'),
(15, 'about', 'about_mission_color', 'color', '#444444', NULL, '2026-10-27 22:15:00'),
(16, 'about', 'about_vision', '', 'To be the leading talent acquisition partner built on integrity.', NULL, '2026-10-27 22:15:00'),
(17, 'about', 'about_vision_color', 'color', '#444444', NULL, '2026-10-27 22:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `setting_key` varchar(120) NOT NULL,
  `setting_value` longtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_name', 'Noor Staffing Bangladesh', '2026-10-27 19:23:52'),
(2, 'announcement', 'People first. Opportunity always.', '2026-10-27 19:23:52'),
(3, 'theme_primary', '#123B5D', '2026-10-27 19:23:52'),
(4, 'theme_accent', '#E9A23B', '2026-10-27 19:23:52'),
(5, 'body_font_size', '16', '2026-10-27 19:23:52'),
(6, 'heading_font_size', '42', '2026-10-27 19:23:52'),
(7, 'logo_width', '180', '2026-10-27 19:23:52'),
(8, 'home_heading', 'Connecting talent with opportunity', '2026-10-27 19:23:52'),
(9, 'home_intro', 'We help organizations build stronger teams and help people discover meaningful work.', '2026-10-27 19:23:52'),
(10, 'home_cta', 'Explore opportunities', '2026-10-27 19:23:52');

-- --------------------------------------------------------

--
-- Table structure for table `system_config`
--

CREATE TABLE `system_config` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `config_group` varchar(60) NOT NULL DEFAULT 'general',
  `config_key` varchar(120) NOT NULL,
  `config_value` longtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `translations`
--

CREATE TABLE `translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_type` varchar(60) NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `field_name` varchar(60) NOT NULL,
  `locale` char(2) NOT NULL DEFAULT 'bn',
  `translation` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_created` (`created_at`),
  ADD KEY `fk_activity_admin` (`admin_user_id`);

--
-- Indexes for table `admin_roles`
--
ALTER TABLE `admin_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_key` (`role_key`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_admin_role` (`role_id`);

--
-- Indexes for table `analytics_visits`
--
ALTER TABLE `analytics_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_analytics_date` (`visited_at`);

--
-- Indexes for table `board_members`
--
ALTER TABLE `board_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_memories`
--
ALTER TABLE `employee_memories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_of_month`
--
ALTER TABLE `employee_of_month`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_stories`
--
ALTER TABLE `employee_stories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_story_media` (`featured_media_id`);

--
-- Indexes for table `entity_translations`
--
ALTER TABLE `entity_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_entity_translation` (`entity_type`,`entity_id`,`field_name`,`locale`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_jobs_public` (`status`,`closing_date`,`published_at`),
  ADD KEY `fk_jobs_admin` (`created_by`);

--
-- Indexes for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_applications_job_status` (`job_id`,`status`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_media_admin` (`uploaded_by`);

--
-- Indexes for table `navigation_items`
--
ALTER TABLE `navigation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_nav_parent` (`parent_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_pages_creator` (`created_by`),
  ADD KEY `fk_pages_updater` (`updated_by`);

--
-- Indexes for table `page_sections`
--
ALTER TABLE `page_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_page_section` (`page_slug`,`section_key`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `system_config`
--
ALTER TABLE `system_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `config_key` (`config_key`);

--
-- Indexes for table `translations`
--
ALTER TABLE `translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_entity_translation` (`entity_type`,`entity_id`,`field_name`,`locale`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_roles`
--
ALTER TABLE `admin_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `analytics_visits`
--
ALTER TABLE `analytics_visits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `board_members`
--
ALTER TABLE `board_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_memories`
--
ALTER TABLE `employee_memories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_of_month`
--
ALTER TABLE `employee_of_month`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_stories`
--
ALTER TABLE `employee_stories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `entity_translations`
--
ALTER TABLE `entity_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `navigation_items`
--
ALTER TABLE `navigation_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `page_sections`
--
ALTER TABLE `page_sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `system_config`
--
ALTER TABLE `system_config`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `translations`
--
ALTER TABLE `translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_activity_admin` FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD CONSTRAINT `fk_admin_role` FOREIGN KEY (`role_id`) REFERENCES `admin_roles` (`id`);

--
-- Constraints for table `employee_stories`
--
ALTER TABLE `employee_stories`
  ADD CONSTRAINT `fk_story_media` FOREIGN KEY (`featured_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `fk_jobs_admin` FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD CONSTRAINT `fk_app_job` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`);

--
-- Constraints for table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `fk_media_admin` FOREIGN KEY (`uploaded_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `navigation_items`
--
ALTER TABLE `navigation_items`
  ADD CONSTRAINT `fk_nav_parent` FOREIGN KEY (`parent_id`) REFERENCES `navigation_items` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `fk_pages_creator` FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pages_updater` FOREIGN KEY (`updated_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
