-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2026 at 03:25 PM
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
(1, 1, 'System Super Admin', 'admin@noorstaffing.com', '$2y$10$MZb47ycoHPPSq8GgjKKKg.BobeLtcb7REmJbGilQj3VTGcElT3Fhi', 'active', '2026-10-28 19:17:10', '2026-10-27 19:48:11', '2026-10-28 13:17:10');

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
(127, '/nsg/contact.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 23:10:34'),
(128, '/nsg/contact.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 23:17:39'),
(129, '/nsg/index.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/contact.php', '2026-10-27 23:17:42'),
(130, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php', '2026-10-27 23:17:44'),
(131, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-27 23:17:45'),
(132, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 23:17:47'),
(133, '/nsg/people.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-27 23:17:48'),
(134, '/nsg/board.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/people.php', '2026-10-27 23:17:49'),
(135, '/nsg/contact.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/board.php', '2026-10-27 23:17:49'),
(136, '/nsg/contact.php?lang=bn', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/contact.php', '2026-10-27 23:17:53'),
(137, '/nsg/contact.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/contact.php?lang=bn', '2026-10-27 23:17:58'),
(138, '/nsg/index.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/contact.php?lang=en', '2026-10-27 23:17:59'),
(139, '/nsg/index.php?lang=bn', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php', '2026-10-27 23:18:01'),
(140, '/nsg/index.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-27 23:18:10'),
(141, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php', '2026-10-27 23:18:12'),
(142, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:12:58'),
(143, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:23:50'),
(144, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:25:30'),
(145, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:26:30'),
(146, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:36:10'),
(147, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:41:05'),
(148, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:42:28'),
(149, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:42:58'),
(150, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:43:22'),
(151, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:43:23'),
(152, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:43:26'),
(153, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:43:46'),
(154, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:47:07'),
(155, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:47:19'),
(156, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:47:23'),
(157, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:47:24'),
(158, '/nsg/', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:51:48'),
(159, '/nsg/about.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/', '2026-10-28 13:55:31'),
(160, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/about.php', '2026-10-28 13:55:32'),
(161, '/nsg/people.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-28 13:55:33'),
(162, '/nsg/board.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/people.php', '2026-10-28 13:55:34'),
(163, '/nsg/contact.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/board.php', '2026-10-28 13:55:35'),
(164, '/nsg/index.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/contact.php', '2026-10-28 13:55:37'),
(165, '/nsg/index.php?lang=bn', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php', '2026-10-28 13:55:38'),
(166, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 13:55:40'),
(167, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 13:56:54'),
(168, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 13:56:55'),
(169, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 13:56:56'),
(170, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 13:58:57'),
(171, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 13:59:00'),
(172, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 13:59:06'),
(173, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:01:34'),
(174, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:01:36'),
(175, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:01:37'),
(176, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:03:30'),
(177, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:03:31'),
(178, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:03:46'),
(179, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:03:47'),
(180, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:03:49'),
(181, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 14:04:48'),
(182, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:06:15'),
(183, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:06:16'),
(184, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:06:17'),
(185, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '', '2026-10-28 14:06:34'),
(186, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:09:13'),
(187, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:09:14'),
(188, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:15:32'),
(189, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:15:43');
INSERT INTO `analytics_visits` (`id`, `page_url`, `ip_hash`, `user_agent`, `referrer`, `visited_at`) VALUES
(190, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:15:54'),
(191, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:16:16'),
(192, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:17:40'),
(193, '/nsg/index.php?lang=en', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=bn', '2026-10-28 14:17:59'),
(194, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php?lang=en', '2026-10-28 14:21:02'),
(195, '/nsg/index.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-28 14:21:06'),
(196, '/nsg/index.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-28 14:21:45'),
(197, '/nsg/careers.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/index.php', '2026-10-28 14:22:13'),
(198, '/nsg/index.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-28 14:22:16'),
(199, '/nsg/index.php', '1cc2b089f4429445ca7abc6c442db8cb65c04641fdb8d325f0e601bd1fdfccfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'http://localhost/nsg/careers.php', '2026-10-28 14:23:04');

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
(1, 'About Us Page', 'about', NULL, NULL, '', '', 'published', '2026-10-28 04:14:03', '2026-10-28 04:15:00', NULL, NULL, 1),
(4, 'Home Page', 'index', NULL, NULL, '', '', 'published', '2026-10-28 19:25:28', '2026-10-28 19:51:46', NULL, NULL, 1);

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
(17, 'about', 'about_vision_color', 'color', '#444444', NULL, '2026-10-27 22:15:00'),
(52, 'index', 'hero_title', 'text', 'Operational excellence with global impact', NULL, '2026-10-28 13:51:46'),
(53, 'index', 'hero_title_color', 'color', '#ffffff', NULL, '2026-10-28 13:51:46'),
(54, 'index', 'hero_title_size', 'text', '3.5rem', NULL, '2026-10-28 13:51:46'),
(55, 'index', 'hero_image', 'image', 'uploads/images/home_img_6ae1f7c80a7a65.83946799.webp', NULL, '2026-10-28 13:51:46'),
(56, 'index', 'home_about_heading', 'text', 'Noor Staffing Group in Bangladesh', NULL, '2026-10-28 13:51:46'),
(57, 'index', 'home_about_body', '', 'Noor Staffing Group, part of the Noor Inc. group of companies, supports its nationwide US staffing and workforce solutions through a dedicated offshore support team based in Bangladesh. \r\n\r\nWhile NSG\'s primary services—staffing, recruiting, and workforce solutions—are delivered across the United States, the Bangladesh team plays a pivotal role in supporting these endeavors. This strategic offshore hub enhances our capabilities, ensuring seamless service delivery to our diverse clientele and extending our commitment to excellence.\r\n\r\nThe Team\r\n\r\nBangladesh is home to NSG’s growing team of talented professionals who contribute to core business functions like recruiting and sales support, research, analytics, and back office operations. Working closely with our US-based teams, they help keep things running smoothly and efficiently.\r\n\r\nBy operating in alignment with US business hours, they ensure real-time collaboration with our stateside teams. This synergy enables NSG to maintain its reputation for responsive and efficient service, meeting the dynamic needs of clients across industries such as healthcare, government, professional services, light industrial, and hospitality.', NULL, '2026-10-28 13:51:46'),
(58, 'index', 'about_section_image', 'image', 'uploads/images/home_img_6ae1fa35dd1b65.00740847.webp', NULL, '2026-10-28 13:51:46'),
(59, 'index', 'cta_heading', 'text', 'Take the first step toward your dreams.', NULL, '2026-10-28 13:51:46'),
(60, 'index', 'cta_button_label', 'text', 'SUBMIT YOUR RESUME', NULL, '2026-10-28 13:51:46'),
(61, 'index', 'stat_1', 'text', '20+ Years in operation', NULL, '2026-10-28 13:51:46'),
(62, 'index', 'stat_2', 'text', '14 US offices supporting clients in 46 states', NULL, '2026-10-28 13:51:46'),
(63, 'index', 'stat_3', 'text', '250+ corporate staff in the U.S. and 100+ team members in Bangladesh', NULL, '2026-10-28 13:51:46'),
(64, 'index', 'stat_4', 'text', '22 specialized brands under the Noor, Inc. portfolio', NULL, '2026-10-28 13:51:46'),
(65, 'index', 'stat_5', 'text', '6,000+ contingent workers actively employed nationwide', NULL, '2026-10-28 13:51:46'),
(66, 'index', 'vision_quote', 'text', 'Driven by talent and built on trust.', NULL, '2026-10-28 13:51:46'),
(67, 'index', 'vision_bg_image', 'image', 'uploads/images/bd-flag-bg.jpg', NULL, '2026-10-28 13:51:46'),
(68, 'index', 'exp_1_icon', 'text', '👥', NULL, '2026-10-28 13:51:46'),
(69, 'index', 'exp_1_title', 'text', 'Staffing Solutions', NULL, '2026-10-28 13:51:46'),
(70, 'index', 'exp_1_body', '', 'Providing fast, reliable staffing support by connecting businesses with pre-vetted talent.', NULL, '2026-10-28 13:51:46'),
(71, 'index', 'exp_2_icon', 'text', '📄', NULL, '2026-10-28 13:51:46'),
(72, 'index', 'exp_2_title', 'text', 'Recruitment Services', NULL, '2026-10-28 13:51:46'),
(73, 'index', 'exp_2_body', '', 'Our experienced recruiters partner closely with clients to identify and hire top-tier professionals.', NULL, '2026-10-28 13:51:46'),
(74, 'index', 'exp_3_icon', 'text', '🧮', NULL, '2026-10-28 13:51:46'),
(75, 'index', 'exp_3_title', 'text', 'Payroll & Employer Services', NULL, '2026-10-28 13:51:46'),
(76, 'index', 'exp_3_body', '', 'Handling complex payroll processes to ensure full compliance, reduce risk, and simplify administration.', NULL, '2026-10-28 13:51:46'),
(77, 'index', 'exp_4_icon', 'text', '⚙️', NULL, '2026-10-28 13:51:46'),
(78, 'index', 'exp_4_title', 'text', 'Workforce Management', NULL, '2026-10-28 13:51:46'),
(79, 'index', 'exp_4_body', '', 'Offering comprehensive workforce solutions that include oversight, compliance, and transparent reporting.', NULL, '2026-10-28 13:51:46'),
(80, 'index', 'culture_heading', 'text', 'Opportunities with NSG Bangladesh', NULL, '2026-10-28 13:51:46'),
(81, 'index', 'culture_body', '', 'At NSG Bangladesh, we are building more than just an offshore support team. We are creating a place where talented professionals can grow, contribute, and be part of something bigger.', NULL, '2026-10-28 13:51:46'),
(82, 'index', 'office_wall_image', 'image', 'assets/office-wall.png', NULL, '2026-10-28 13:51:46'),
(154, 'index', 'leader_name', 'text', 'Mr. Noor', NULL, '2026-10-28 13:51:46'),
(155, 'index', 'leader_photo', 'image', 'uploads/images/home_img_6ae1fbc2f02282.68521468.webp', NULL, '2026-10-28 13:51:46'),
(156, 'index', 'leader_speech', '', 'Since 2003, Mr. Noor has been the individual owner and president of Noor Inc., a privately held umbrella company headquartered in New York City. The company oversees a national portfolio of staffing, recruiting and healthcare service brands.\r\n\r\nCornerstone to Mr. Noor’s success in the industry is his acquisition track record and proven ability to integrate and grow acquired brands. Mr. Noor’s investment strategy is to build and strengthen a diverse portfolio of brands. He leverages the unique talents and advantages of acquired companies while enabling them to maintain their unique brand identity and leadership. \r\n\r\nHis vision for Bangladesh is both strategic and personal. As a proud native of the country, he is committed to creating long-term, high-quality career opportunities for local talent while strengthening the operational backbone of his US-based portfolio. \r\n\r\nThe Bangladesh office and operations reflect his belief that global success is built on trusted people, thoughtful investment, and a deep connection to one’s roots.', NULL, '2026-10-28 13:51:46');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`key`, `value`, `updated_at`) VALUES
('contact_address', '', '2026-10-28 20:22:59'),
('contact_address_bn', 'ঢাকা, বাংলাদেশ', '2026-10-28 19:23:47'),
('contact_email', 'contact@noorstaffing.com', '2026-10-28 20:22:59'),
('contact_map_embed_url', '', '2026-10-28 19:23:47'),
('contact_phone', '+880 1700-000000', '2026-10-28 20:22:59'),
('default_seo_description', 'Connecting global businesses with high-value placement and staffing solutions in Bangladesh.', '2026-10-28 19:23:47'),
('default_seo_title', 'Noor Staffing Group | Offshore Workforce Solutions', '2026-10-28 19:23:47'),
('facebook_url', 'https://www.facebook.com/', '2026-10-28 20:22:59'),
('head_office_address', '', '2026-10-28 20:22:59'),
('head_office_address_bn', 'নিউ ইয়র্ক সিটি, নিউ ইয়র্ক, যুক্তরাষ্ট্র', '2026-10-28 19:23:47'),
('instagram_url', 'https://www.instagram.com/', '2026-10-28 20:22:59'),
('linkedin_url', 'https://www.linkedin.com/feed/', '2026-10-28 20:22:59'),
('logo_width', '280', '2026-10-28 20:22:59'),
('seo_description', '', '2026-10-28 20:22:59'),
('site_logo', 'uploads/images/site_logo_1793197379.webp', '2026-10-28 20:22:59'),
('site_logo_text', 'NOOR STAFFING', '2026-10-28 19:23:47'),
('site_name', 'Noor Staffing Group', '2026-10-28 20:22:59'),
('site_name_bn', 'নূর স্টাফিং গ্রুপ', '2026-10-28 20:22:59'),
('site_tagline', 'Offshore Operational Excellence', '2026-10-28 19:23:47'),
('social_facebook', 'https://facebook.com', '2026-10-28 19:23:47'),
('social_instagram', 'https://instagram.com', '2026-10-28 19:23:47'),
('social_linkedin', 'https://linkedin.com', '2026-10-28 19:23:47'),
('social_twitter', 'https://twitter.com', '2026-10-28 19:23:47'),
('theme_accent_color', '#B71C1C', '2026-10-28 20:22:59'),
('theme_primary_color', '#B71C1C', '2026-10-28 20:22:59');

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
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`key`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `page_sections`
--
ALTER TABLE `page_sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;

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
