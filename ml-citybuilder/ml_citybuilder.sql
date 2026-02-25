-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 25, 2026 at 03:00 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ml_citybuilder`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `message` varchar(255) NOT NULL,
  `meta_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta_json`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `event_type`, `message`, `meta_json`, `created_at`) VALUES
(1, 1, 'login', 'Logged in as Ethan', NULL, '2026-02-24 13:43:39'),
(2, 1, 'narration', 'Crisis: Crime Patrol Allocation', NULL, '2026-02-24 13:43:50'),
(3, 1, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":3,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 13:44:13'),
(4, 1, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-24 13:44:19'),
(5, 1, 'mistake', 'Incorrect ML decision. Try again.', NULL, '2026-02-24 13:44:29'),
(6, 1, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":2,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 13:44:40'),
(7, 2, 'login', 'Logged in as test', NULL, '2026-02-24 14:01:46'),
(8, 2, 'narration', 'Crisis: Crime Patrol Allocation', NULL, '2026-02-24 14:26:23'),
(9, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":3,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:26:29'),
(10, 2, 'narration', 'Crisis: Model Audit', NULL, '2026-02-24 14:26:33'),
(11, 2, 'mistake', 'Incorrect ML decision. Try again.', NULL, '2026-02-24 14:26:47'),
(12, 2, 'earn_points', 'Earned +150 revenue for correct ML decision', '{\"task_id\":4,\"base_points\":150,\"booster_bonus\":0}', '2026-02-24 14:26:53'),
(13, 2, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-24 14:26:55'),
(14, 2, 'mistake', 'Incorrect ML decision. Try again.', NULL, '2026-02-24 14:27:02'),
(15, 2, 'narration', 'Crisis: Crime Patrol Allocation', NULL, '2026-02-24 14:27:06'),
(16, 2, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-24 14:27:18'),
(17, 2, 'narration', 'Crisis: Dirty Sensor Data', NULL, '2026-02-24 14:27:20'),
(18, 2, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-24 14:27:21'),
(19, 2, 'narration', 'Crisis: Dirty Sensor Data', NULL, '2026-02-24 14:27:22'),
(20, 2, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-24 14:27:22'),
(21, 2, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-24 14:27:23'),
(22, 2, 'narration', 'Crisis: Crime Patrol Allocation', NULL, '2026-02-24 14:27:24'),
(23, 2, 'narration', 'Crisis: Traffic Gridlock', NULL, '2026-02-24 14:27:25'),
(24, 2, 'narration', 'Crisis: Dirty Sensor Data', NULL, '2026-02-24 14:27:25'),
(25, 2, 'narration', 'Crisis: Traffic Gridlock', NULL, '2026-02-24 14:27:26'),
(26, 2, 'narration', 'Crisis: Dirty Sensor Data', NULL, '2026-02-24 14:27:27'),
(27, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:30:01'),
(28, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:01'),
(29, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:02'),
(30, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:05'),
(31, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:06'),
(32, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:06'),
(33, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:06'),
(34, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:06'),
(35, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:06'),
(36, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:08'),
(37, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:08'),
(38, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:09'),
(39, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:09'),
(40, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:09'),
(41, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:09'),
(42, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:09'),
(43, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:10'),
(44, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:10'),
(45, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:10'),
(46, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:10'),
(47, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:10'),
(48, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:10'),
(49, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:11'),
(50, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:11'),
(51, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:11'),
(52, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:11'),
(53, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:11'),
(54, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:12'),
(55, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:12'),
(56, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:12'),
(57, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:12'),
(58, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:12'),
(59, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:13'),
(60, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:13'),
(61, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:13'),
(62, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:13'),
(63, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:13'),
(64, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:14'),
(65, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:14'),
(66, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:14'),
(67, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:14'),
(68, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:14'),
(69, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:15'),
(70, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:15'),
(71, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:15'),
(72, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:15'),
(73, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:15'),
(74, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:16'),
(75, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:16'),
(76, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:16'),
(77, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:16'),
(78, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:16'),
(79, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:17'),
(80, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:17'),
(81, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:17'),
(82, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:17'),
(83, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:17'),
(84, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:18'),
(85, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:18'),
(86, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:18'),
(87, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:18'),
(88, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:18'),
(89, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":5,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:18'),
(90, 2, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-24 14:32:22'),
(91, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":2,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:32:41'),
(92, 3, 'login', 'Logged in as Ron', NULL, '2026-02-24 14:45:15'),
(93, 3, 'map', 'City map initialised (10x10).', NULL, '2026-02-24 14:45:15'),
(94, 3, 'narration', 'Crisis: Traffic Gridlock', NULL, '2026-02-24 14:45:29'),
(95, 3, 'booster', 'Used a booster (Auto-Tune tool)', NULL, '2026-02-24 14:45:47'),
(96, 3, 'earn_points', 'Earned +150 revenue for correct ML decision', '{\"task_id\":1,\"base_points\":120,\"booster_bonus\":30}', '2026-02-24 14:45:47'),
(97, 3, 'narration', 'Crisis: Crime Patrol Allocation', NULL, '2026-02-24 14:45:52'),
(98, 3, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":3,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 14:45:57'),
(99, 3, 'build', 'Placed House at (5,2) (-60).', NULL, '2026-02-24 14:46:21'),
(100, 3, 'build', 'Placed Factory at (3,2) (-120).', NULL, '2026-02-24 14:46:32'),
(101, 3, 'turn', 'End turn: +14 revenue from city income.', NULL, '2026-02-24 14:46:37'),
(102, 3, 'build', 'Placed Road at (4,2) (-10).', NULL, '2026-02-24 14:46:55'),
(103, 3, 'turn', 'End turn: +14 revenue from city income.', NULL, '2026-02-24 14:47:04'),
(104, 3, 'turn', 'End turn: +14 revenue from city income.', NULL, '2026-02-24 14:47:04'),
(105, 3, 'turn', 'End turn: +14 revenue from city income.', NULL, '2026-02-24 14:47:04'),
(106, 3, 'turn', 'End turn: +14 revenue from city income.', NULL, '2026-02-24 14:47:05'),
(107, 3, 'turn', 'End turn: +14 revenue from city income.', NULL, '2026-02-24 14:47:05'),
(108, 3, 'turn', 'End turn: +14 revenue from city income.', NULL, '2026-02-24 14:47:05'),
(109, 3, 'turn', 'End turn: +14 revenue from city income.', NULL, '2026-02-24 14:47:05'),
(110, 3, 'turn', 'End turn: +14 revenue from city income.', NULL, '2026-02-24 14:47:05'),
(111, 3, 'turn', 'End turn: +14 revenue from city income.', NULL, '2026-02-24 14:47:05'),
(112, 3, 'turn', 'End turn: +14 revenue from city income.', NULL, '2026-02-24 14:47:06'),
(113, 3, 'turn', 'End turn: +14 revenue from city income.', NULL, '2026-02-24 14:47:06'),
(114, 3, 'turn', 'End turn: +14 revenue from city income.', NULL, '2026-02-24 14:47:06'),
(115, 3, 'turn', 'End turn: +14 revenue from city income.', NULL, '2026-02-24 14:47:06'),
(116, 3, 'turn', 'End turn: +14 revenue from city income.', NULL, '2026-02-24 14:47:06'),
(117, 3, 'turn', 'End turn: +14 revenue from city income.', NULL, '2026-02-24 14:47:06'),
(118, 3, 'turn', 'End turn: +14 revenue from city income.', NULL, '2026-02-24 14:47:07'),
(119, 2, 'login', 'Logged in as test', NULL, '2026-02-24 15:56:16'),
(120, 2, 'map', 'City map initialised (10x10).', NULL, '2026-02-24 15:56:16'),
(121, 2, 'narration', 'Crisis: Housing Demand Surge', NULL, '2026-02-24 15:56:29'),
(122, 2, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-24 15:56:38'),
(123, 2, 'narration', 'Crisis: Housing Demand Surge', NULL, '2026-02-24 15:56:39'),
(124, 2, 'narration', 'Crisis: Citizen Segmentation', NULL, '2026-02-24 15:56:39'),
(125, 2, 'narration', 'Crisis: Fair Services Review', NULL, '2026-02-24 15:56:39'),
(126, 2, 'narration', 'Crisis: Housing Demand Surge', NULL, '2026-02-24 15:56:39'),
(127, 2, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":2,\"base_points\":120,\"booster_bonus\":0}', '2026-02-24 15:56:48'),
(128, 2, 'narration', 'Crisis: Traffic Gridlock', NULL, '2026-02-24 15:56:50'),
(129, 2, 'narration', 'Crisis: Crime Patrol Allocation', NULL, '2026-02-24 15:56:56'),
(130, 2, 'narration', 'Crisis: Crime Patrol Allocation', NULL, '2026-02-24 15:57:00'),
(131, 2, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-24 15:57:07'),
(132, 2, 'narration', 'Crisis: Model Audit', NULL, '2026-02-24 15:57:11'),
(133, 2, 'narration', 'Crisis: Fair Services Review', NULL, '2026-02-24 15:57:17'),
(134, 2, 'narration', 'Crisis: Fair Services Review', NULL, '2026-02-24 15:57:24'),
(135, 2, 'narration', 'Crisis: Crime Patrol Allocation', NULL, '2026-02-24 15:57:28'),
(136, 2, 'login', 'Logged in as test', NULL, '2026-02-24 15:57:41'),
(137, 2, 'map', 'City map initialised (10x10).', NULL, '2026-02-24 15:57:41'),
(138, 2, 'narration', 'Crisis: Fair Services Review', NULL, '2026-02-24 15:57:45'),
(139, 2, 'narration', 'Crisis: Crime Patrol Allocation', NULL, '2026-02-24 15:57:49'),
(140, 2, 'narration', 'Crisis: Model Audit', NULL, '2026-02-24 15:57:55'),
(141, 2, 'narration', 'Crisis: Dirty Sensor Data', NULL, '2026-02-24 15:57:59'),
(142, 2, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-24 15:58:00'),
(143, 2, 'narration', 'Crisis: Fair Services Review', NULL, '2026-02-24 15:58:01'),
(144, 2, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-24 15:58:09'),
(145, 2, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-24 15:58:14'),
(146, 2, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-24 15:58:21'),
(147, 2, 'narration', 'Crisis: Traffic Gridlock', NULL, '2026-02-24 15:58:25'),
(148, 2, 'narration', 'Crisis: Dirty Sensor Data', NULL, '2026-02-24 15:58:31'),
(149, 2, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-24 15:58:34'),
(150, 2, 'narration', 'Crisis: Traffic Gridlock', NULL, '2026-02-24 15:59:14'),
(151, 2, 'narration', 'Crisis: Fair Services Review', NULL, '2026-02-24 15:59:19'),
(152, 2, 'narration', 'Crisis: Dirty Sensor Data', NULL, '2026-02-24 15:59:24'),
(153, 2, 'narration', 'Crisis: Model Audit', NULL, '2026-02-24 15:59:27'),
(154, 2, 'narration', 'Crisis: Crime Patrol Allocation', NULL, '2026-02-24 15:59:32'),
(155, 2, 'narration', 'Crisis: Citizen Segmentation', NULL, '2026-02-24 15:59:34'),
(156, 2, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-24 16:02:36'),
(157, 2, 'login', 'Logged in as test', NULL, '2026-02-24 16:20:47'),
(158, 2, 'map', 'City map initialised (10x10).', NULL, '2026-02-24 16:20:47'),
(159, 2, 'narration', 'Crisis: Citizen Segmentation', NULL, '2026-02-24 16:20:50'),
(160, 2, 'narration', 'Crisis: Housing Demand Surge', NULL, '2026-02-24 16:20:53'),
(161, 2, 'narration', 'Crisis: Traffic Gridlock', NULL, '2026-02-24 16:20:56'),
(162, 2, 'mistake', 'Incorrect ML decision. Try again.', NULL, '2026-02-24 16:21:14'),
(163, 2, 'narration', 'Crisis: Citizen Segmentation', NULL, '2026-02-24 16:21:29'),
(164, 2, 'narration', 'Crisis: Crime Patrol Allocation', NULL, '2026-02-24 16:21:33'),
(165, 2, 'earn_points', 'Earned +150 revenue for correct ML decision', '{\"task_id\":11,\"base_points\":150,\"booster_bonus\":0}', '2026-02-24 16:23:35'),
(166, 2, 'narration', 'Crisis: Model Audit', NULL, '2026-02-24 16:23:40'),
(167, 2, 'earn_points', 'Earned +160 revenue for correct ML decision', '{\"task_id\":10,\"base_points\":160,\"booster_bonus\":0}', '2026-02-24 16:23:56'),
(168, 2, 'narration', 'Crisis: Fair Services Review', NULL, '2026-02-24 16:24:00'),
(169, 2, 'narration', 'Crisis: Model Audit', NULL, '2026-02-24 16:24:03'),
(170, 2, 'narration', 'Crisis: Crime Patrol Allocation', NULL, '2026-02-24 16:24:11'),
(171, 2, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-24 16:24:16'),
(172, 2, 'login', 'Logged in as test', NULL, '2026-02-25 11:42:41'),
(173, 2, 'map', 'City map initialised (10x10).', NULL, '2026-02-25 11:42:41'),
(174, 2, 'narration', 'Crisis: Model Audit', NULL, '2026-02-25 11:46:06'),
(175, 2, 'build', 'Placed House at (2,2) (-60).', NULL, '2026-02-25 11:55:37'),
(176, 2, 'build', 'Placed House at (6,2) (-60).', NULL, '2026-02-25 11:55:40'),
(177, 2, 'build', 'Placed Factory at (4,1) (-120).', NULL, '2026-02-25 11:55:46'),
(178, 2, 'build', 'Placed Road at (3,2) (-10).', NULL, '2026-02-25 11:56:00'),
(179, 2, 'build', 'Placed Road at (4,2) (-10).', NULL, '2026-02-25 11:56:01'),
(180, 2, 'build', 'Placed Road at (5,2) (-10).', NULL, '2026-02-25 11:56:02'),
(181, 4, 'login', 'Logged in as Bogdan', NULL, '2026-02-25 12:22:00'),
(182, 4, 'map', 'City map initialised (10x10).', NULL, '2026-02-25 12:22:00'),
(183, 4, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-25 12:22:09'),
(184, 4, 'booster', 'Used a booster (Auto-Tune tool)', NULL, '2026-02-25 12:22:29'),
(185, 4, 'earn_points', 'Earned +150 revenue for correct ML decision', '{\"task_id\":2,\"base_points\":120,\"booster_bonus\":30}', '2026-02-25 12:22:29'),
(186, 4, 'narration', 'Crisis: Traffic Gridlock', NULL, '2026-02-25 12:22:35'),
(187, 4, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-25 12:22:39'),
(188, 4, 'mistake', 'Incorrect ML decision. Try again.', NULL, '2026-02-25 12:22:48'),
(189, 4, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":2,\"base_points\":120,\"booster_bonus\":0}', '2026-02-25 12:23:48'),
(190, 4, 'narration', 'Crisis: Model Audit', NULL, '2026-02-25 12:23:52'),
(191, 4, 'build', 'Placed House at (7,1) (-60).', NULL, '2026-02-25 12:24:07'),
(192, 4, 'build', 'Placed House at (2,1) (-60).', NULL, '2026-02-25 12:24:08'),
(193, 4, 'build', 'Placed House at (2,3) (-60).', NULL, '2026-02-25 12:24:10'),
(194, 4, 'build', 'Placed House at (4,2) (-60).', NULL, '2026-02-25 12:24:11'),
(195, 4, 'turn', 'End turn: +17 revenue from city income.', NULL, '2026-02-25 12:24:30'),
(196, 4, 'turn', 'End turn: +17 revenue from city income.', NULL, '2026-02-25 12:24:33'),
(197, 4, 'narration', 'Crisis: Citizen Segmentation', NULL, '2026-02-25 12:24:50'),
(198, 4, 'narration', 'Crisis: Fair Services Review', NULL, '2026-02-25 12:24:50'),
(199, 4, 'narration', 'Crisis: Power Shortage Forecast', NULL, '2026-02-25 12:25:06'),
(200, 4, 'earn_points', 'Earned +150 revenue for correct ML decision', '{\"task_id\":9,\"base_points\":150,\"booster_bonus\":0}', '2026-02-25 12:25:22'),
(201, 4, 'earn_points', 'Earned +150 revenue for correct ML decision', '{\"task_id\":9,\"base_points\":150,\"booster_bonus\":0}', '2026-02-25 12:25:23'),
(202, 4, 'earn_points', 'Earned +150 revenue for correct ML decision', '{\"task_id\":9,\"base_points\":150,\"booster_bonus\":0}', '2026-02-25 12:25:24'),
(203, 4, 'earn_points', 'Earned +150 revenue for correct ML decision', '{\"task_id\":9,\"base_points\":150,\"booster_bonus\":0}', '2026-02-25 12:25:24'),
(204, 4, 'earn_points', 'Earned +150 revenue for correct ML decision', '{\"task_id\":9,\"base_points\":150,\"booster_bonus\":0}', '2026-02-25 12:25:24'),
(205, 4, 'earn_points', 'Earned +150 revenue for correct ML decision', '{\"task_id\":9,\"base_points\":150,\"booster_bonus\":0}', '2026-02-25 12:25:24'),
(206, 4, 'earn_points', 'Earned +150 revenue for correct ML decision', '{\"task_id\":9,\"base_points\":150,\"booster_bonus\":0}', '2026-02-25 12:25:25'),
(207, 4, 'earn_points', 'Earned +150 revenue for correct ML decision', '{\"task_id\":9,\"base_points\":150,\"booster_bonus\":0}', '2026-02-25 12:25:25'),
(208, 4, 'earn_points', 'Earned +150 revenue for correct ML decision', '{\"task_id\":9,\"base_points\":150,\"booster_bonus\":0}', '2026-02-25 12:25:25'),
(209, 4, 'earn_points', 'Earned +150 revenue for correct ML decision', '{\"task_id\":9,\"base_points\":150,\"booster_bonus\":0}', '2026-02-25 12:25:25'),
(210, 4, 'earn_points', 'Earned +150 revenue for correct ML decision', '{\"task_id\":9,\"base_points\":150,\"booster_bonus\":0}', '2026-02-25 12:25:25'),
(211, 2, 'login', 'Logged in as test', NULL, '2026-02-25 13:02:51'),
(212, 2, 'map', 'City map initialised (10x10).', NULL, '2026-02-25 13:02:51'),
(213, 5, 'login', 'Logged in as ED', NULL, '2026-02-25 13:03:14'),
(214, 5, 'map', 'City map initialised (10x10).', NULL, '2026-02-25 13:03:14'),
(215, 5, 'narration', 'Crisis: Dirty Sensor Data', NULL, '2026-02-25 13:03:20'),
(216, 5, 'narration', 'Crisis: Housing Demand Surge', NULL, '2026-02-25 13:03:31'),
(217, 5, 'narration', 'Crisis: Housing Demand Surge', NULL, '2026-02-25 13:03:37'),
(218, 5, 'narration', 'Crisis: Traffic Gridlock', NULL, '2026-02-25 13:03:41'),
(219, 5, 'narration', 'Crisis: Dirty Sensor Data', NULL, '2026-02-25 13:03:41'),
(220, 5, 'narration', 'Crisis: Crime Patrol Allocation', NULL, '2026-02-25 13:03:41'),
(221, 5, 'narration', 'Crisis: Housing Demand Surge', NULL, '2026-02-25 13:03:41'),
(222, 5, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":3,\"base_points\":120,\"booster_bonus\":0}', '2026-02-25 13:03:47'),
(223, 5, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":3,\"base_points\":120,\"booster_bonus\":0}', '2026-02-25 13:03:48'),
(224, 5, 'earn_points', 'Earned +120 revenue for correct ML decision', '{\"task_id\":3,\"base_points\":120,\"booster_bonus\":0}', '2026-02-25 13:03:48'),
(225, 6, 'login', 'Logged in as Ensi', NULL, '2026-02-25 13:33:11'),
(226, 6, 'map', 'City map initialised (10x10).', NULL, '2026-02-25 13:33:11'),
(227, 6, 'narration', 'Crisis: Model Audit', NULL, '2026-02-25 13:33:53'),
(228, 6, 'mistake', 'Incorrect ML decision. Try again.', NULL, '2026-02-25 13:34:13'),
(229, 6, 'earn_points', 'Earned +160 revenue for correct ML decision', '{\"task_id\":10,\"base_points\":160,\"booster_bonus\":0}', '2026-02-25 13:34:19'),
(230, 6, 'mistake', 'Incorrect ML decision. Try again.', NULL, '2026-02-25 13:38:17'),
(231, 6, 'build', 'Placed House at (1,2) (-60).', NULL, '2026-02-25 13:41:59'),
(232, 6, 'build', 'Placed House at (5,2) (-60).', NULL, '2026-02-25 13:42:00'),
(233, 6, 'narration', 'Crisis: Model Audit', NULL, '2026-02-25 13:42:23');

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `badges`
--

INSERT INTO `badges` (`id`, `code`, `name`, `description`) VALUES
(1, 'DATA_CLEANING_EXPERT', 'Data Cleaning Expert', 'Completed 3 preprocessing tasks.'),
(2, 'EDA_SPECIALIST', 'EDA Specialist', 'Completed 3 EDA/analysis tasks.'),
(3, 'HYPERPARAM_HERO', 'Hyperparameter Hero', 'Used boosters to optimize successfully 3 times.'),
(4, 'FIRST_WIN', 'First Win', 'Complete your first ML task correctly.'),
(5, 'PIPELINE_PRO', 'Pipeline Pro', 'Solve an ML pipeline ordering task correctly.'),
(6, 'CODE_APPRENTICE', 'Code Apprentice', 'Answer 3 code questions correctly.'),
(7, 'MATCH_MASTER', 'Match Master', 'Solve 3 matching tasks correctly.'),
(8, 'CITY_PLANNER', 'City Planner', 'Place 10 buildings on the map.'),
(9, 'RICH_CITY', 'Rich City', 'Reach 1000 revenue.');

-- --------------------------------------------------------

--
-- Table structure for table `city_tiles`
--

CREATE TABLE `city_tiles` (
  `user_id` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `building` varchar(30) NOT NULL DEFAULT 'empty',
  `blevel` int(11) NOT NULL DEFAULT 1,
  `placed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `city_tiles`
--

INSERT INTO `city_tiles` (`user_id`, `x`, `y`, `building`, `blevel`, `placed_at`) VALUES
(2, 0, 0, 'empty', 1, '2026-02-24 15:56:16'),
(2, 0, 1, 'empty', 1, '2026-02-24 15:56:16'),
(2, 0, 2, 'empty', 1, '2026-02-24 15:56:16'),
(2, 0, 3, 'empty', 1, '2026-02-24 15:56:16'),
(2, 0, 4, 'empty', 1, '2026-02-24 15:56:16'),
(2, 0, 5, 'empty', 1, '2026-02-24 15:56:16'),
(2, 0, 6, 'empty', 1, '2026-02-24 15:56:16'),
(2, 0, 7, 'empty', 1, '2026-02-24 15:56:16'),
(2, 0, 8, 'empty', 1, '2026-02-24 15:56:16'),
(2, 0, 9, 'empty', 1, '2026-02-24 15:56:16'),
(2, 1, 0, 'empty', 1, '2026-02-24 15:56:16'),
(2, 1, 1, 'empty', 1, '2026-02-24 15:56:16'),
(2, 1, 2, 'empty', 1, '2026-02-24 15:56:16'),
(2, 1, 3, 'empty', 1, '2026-02-24 15:56:16'),
(2, 1, 4, 'empty', 1, '2026-02-24 15:56:16'),
(2, 1, 5, 'empty', 1, '2026-02-24 15:56:16'),
(2, 1, 6, 'empty', 1, '2026-02-24 15:56:16'),
(2, 1, 7, 'empty', 1, '2026-02-24 15:56:16'),
(2, 1, 8, 'empty', 1, '2026-02-24 15:56:16'),
(2, 1, 9, 'empty', 1, '2026-02-24 15:56:16'),
(2, 2, 0, 'empty', 1, '2026-02-24 15:56:16'),
(2, 2, 1, 'empty', 1, '2026-02-24 15:56:16'),
(2, 2, 2, 'house', 1, '2026-02-24 15:56:16'),
(2, 2, 3, 'empty', 1, '2026-02-24 15:56:16'),
(2, 2, 4, 'empty', 1, '2026-02-24 15:56:16'),
(2, 2, 5, 'empty', 1, '2026-02-24 15:56:16'),
(2, 2, 6, 'empty', 1, '2026-02-24 15:56:16'),
(2, 2, 7, 'empty', 1, '2026-02-24 15:56:16'),
(2, 2, 8, 'empty', 1, '2026-02-24 15:56:16'),
(2, 2, 9, 'empty', 1, '2026-02-24 15:56:16'),
(2, 3, 0, 'empty', 1, '2026-02-24 15:56:16'),
(2, 3, 1, 'empty', 1, '2026-02-24 15:56:16'),
(2, 3, 2, 'road', 1, '2026-02-24 15:56:16'),
(2, 3, 3, 'empty', 1, '2026-02-24 15:56:16'),
(2, 3, 4, 'empty', 1, '2026-02-24 15:56:16'),
(2, 3, 5, 'empty', 1, '2026-02-24 15:56:16'),
(2, 3, 6, 'empty', 1, '2026-02-24 15:56:16'),
(2, 3, 7, 'empty', 1, '2026-02-24 15:56:16'),
(2, 3, 8, 'empty', 1, '2026-02-24 15:56:16'),
(2, 3, 9, 'empty', 1, '2026-02-24 15:56:16'),
(2, 4, 0, 'empty', 1, '2026-02-24 15:56:16'),
(2, 4, 1, 'factory', 1, '2026-02-24 15:56:16'),
(2, 4, 2, 'road', 1, '2026-02-24 15:56:16'),
(2, 4, 3, 'empty', 1, '2026-02-24 15:56:16'),
(2, 4, 4, 'empty', 1, '2026-02-24 15:56:16'),
(2, 4, 5, 'empty', 1, '2026-02-24 15:56:16'),
(2, 4, 6, 'empty', 1, '2026-02-24 15:56:16'),
(2, 4, 7, 'empty', 1, '2026-02-24 15:56:16'),
(2, 4, 8, 'empty', 1, '2026-02-24 15:56:16'),
(2, 4, 9, 'empty', 1, '2026-02-24 15:56:16'),
(2, 5, 0, 'empty', 1, '2026-02-24 15:56:16'),
(2, 5, 1, 'empty', 1, '2026-02-24 15:56:16'),
(2, 5, 2, 'road', 1, '2026-02-24 15:56:16'),
(2, 5, 3, 'empty', 1, '2026-02-24 15:56:16'),
(2, 5, 4, 'empty', 1, '2026-02-24 15:56:16'),
(2, 5, 5, 'empty', 1, '2026-02-24 15:56:16'),
(2, 5, 6, 'empty', 1, '2026-02-24 15:56:16'),
(2, 5, 7, 'empty', 1, '2026-02-24 15:56:16'),
(2, 5, 8, 'empty', 1, '2026-02-24 15:56:16'),
(2, 5, 9, 'empty', 1, '2026-02-24 15:56:16'),
(2, 6, 0, 'empty', 1, '2026-02-24 15:56:16'),
(2, 6, 1, 'empty', 1, '2026-02-24 15:56:16'),
(2, 6, 2, 'house', 1, '2026-02-24 15:56:16'),
(2, 6, 3, 'empty', 1, '2026-02-24 15:56:16'),
(2, 6, 4, 'empty', 1, '2026-02-24 15:56:16'),
(2, 6, 5, 'empty', 1, '2026-02-24 15:56:16'),
(2, 6, 6, 'empty', 1, '2026-02-24 15:56:16'),
(2, 6, 7, 'empty', 1, '2026-02-24 15:56:16'),
(2, 6, 8, 'empty', 1, '2026-02-24 15:56:16'),
(2, 6, 9, 'empty', 1, '2026-02-24 15:56:16'),
(2, 7, 0, 'empty', 1, '2026-02-24 15:56:16'),
(2, 7, 1, 'empty', 1, '2026-02-24 15:56:16'),
(2, 7, 2, 'empty', 1, '2026-02-24 15:56:16'),
(2, 7, 3, 'empty', 1, '2026-02-24 15:56:16'),
(2, 7, 4, 'empty', 1, '2026-02-24 15:56:16'),
(2, 7, 5, 'empty', 1, '2026-02-24 15:56:16'),
(2, 7, 6, 'empty', 1, '2026-02-24 15:56:16'),
(2, 7, 7, 'empty', 1, '2026-02-24 15:56:16'),
(2, 7, 8, 'empty', 1, '2026-02-24 15:56:16'),
(2, 7, 9, 'empty', 1, '2026-02-24 15:56:16'),
(2, 8, 0, 'empty', 1, '2026-02-24 15:56:16'),
(2, 8, 1, 'empty', 1, '2026-02-24 15:56:16'),
(2, 8, 2, 'empty', 1, '2026-02-24 15:56:16'),
(2, 8, 3, 'empty', 1, '2026-02-24 15:56:16'),
(2, 8, 4, 'empty', 1, '2026-02-24 15:56:16'),
(2, 8, 5, 'empty', 1, '2026-02-24 15:56:16'),
(2, 8, 6, 'empty', 1, '2026-02-24 15:56:16'),
(2, 8, 7, 'empty', 1, '2026-02-24 15:56:16'),
(2, 8, 8, 'empty', 1, '2026-02-24 15:56:16'),
(2, 8, 9, 'empty', 1, '2026-02-24 15:56:16'),
(2, 9, 0, 'empty', 1, '2026-02-24 15:56:16'),
(2, 9, 1, 'empty', 1, '2026-02-24 15:56:16'),
(2, 9, 2, 'empty', 1, '2026-02-24 15:56:16'),
(2, 9, 3, 'empty', 1, '2026-02-24 15:56:16'),
(2, 9, 4, 'empty', 1, '2026-02-24 15:56:16'),
(2, 9, 5, 'empty', 1, '2026-02-24 15:56:16'),
(2, 9, 6, 'empty', 1, '2026-02-24 15:56:16'),
(2, 9, 7, 'empty', 1, '2026-02-24 15:56:16'),
(2, 9, 8, 'empty', 1, '2026-02-24 15:56:16'),
(2, 9, 9, 'empty', 1, '2026-02-24 15:56:16'),
(3, 0, 0, 'empty', 1, '2026-02-24 14:45:15'),
(3, 0, 1, 'empty', 1, '2026-02-24 14:45:15'),
(3, 0, 2, 'empty', 1, '2026-02-24 14:45:15'),
(3, 0, 3, 'empty', 1, '2026-02-24 14:45:15'),
(3, 0, 4, 'empty', 1, '2026-02-24 14:45:15'),
(3, 0, 5, 'empty', 1, '2026-02-24 14:45:15'),
(3, 0, 6, 'empty', 1, '2026-02-24 14:45:15'),
(3, 0, 7, 'empty', 1, '2026-02-24 14:45:15'),
(3, 0, 8, 'empty', 1, '2026-02-24 14:45:15'),
(3, 0, 9, 'empty', 1, '2026-02-24 14:45:15'),
(3, 1, 0, 'empty', 1, '2026-02-24 14:45:15'),
(3, 1, 1, 'empty', 1, '2026-02-24 14:45:15'),
(3, 1, 2, 'empty', 1, '2026-02-24 14:45:15'),
(3, 1, 3, 'empty', 1, '2026-02-24 14:45:15'),
(3, 1, 4, 'empty', 1, '2026-02-24 14:45:15'),
(3, 1, 5, 'empty', 1, '2026-02-24 14:45:15'),
(3, 1, 6, 'empty', 1, '2026-02-24 14:45:15'),
(3, 1, 7, 'empty', 1, '2026-02-24 14:45:15'),
(3, 1, 8, 'empty', 1, '2026-02-24 14:45:15'),
(3, 1, 9, 'empty', 1, '2026-02-24 14:45:15'),
(3, 2, 0, 'empty', 1, '2026-02-24 14:45:15'),
(3, 2, 1, 'empty', 1, '2026-02-24 14:45:15'),
(3, 2, 2, 'empty', 1, '2026-02-24 14:45:15'),
(3, 2, 3, 'empty', 1, '2026-02-24 14:45:15'),
(3, 2, 4, 'empty', 1, '2026-02-24 14:45:15'),
(3, 2, 5, 'empty', 1, '2026-02-24 14:45:15'),
(3, 2, 6, 'empty', 1, '2026-02-24 14:45:15'),
(3, 2, 7, 'empty', 1, '2026-02-24 14:45:15'),
(3, 2, 8, 'empty', 1, '2026-02-24 14:45:15'),
(3, 2, 9, 'empty', 1, '2026-02-24 14:45:15'),
(3, 3, 0, 'empty', 1, '2026-02-24 14:45:15'),
(3, 3, 1, 'empty', 1, '2026-02-24 14:45:15'),
(3, 3, 2, 'factory', 1, '2026-02-24 14:45:15'),
(3, 3, 3, 'empty', 1, '2026-02-24 14:45:15'),
(3, 3, 4, 'empty', 1, '2026-02-24 14:45:15'),
(3, 3, 5, 'empty', 1, '2026-02-24 14:45:15'),
(3, 3, 6, 'empty', 1, '2026-02-24 14:45:15'),
(3, 3, 7, 'empty', 1, '2026-02-24 14:45:15'),
(3, 3, 8, 'empty', 1, '2026-02-24 14:45:15'),
(3, 3, 9, 'empty', 1, '2026-02-24 14:45:15'),
(3, 4, 0, 'empty', 1, '2026-02-24 14:45:15'),
(3, 4, 1, 'empty', 1, '2026-02-24 14:45:15'),
(3, 4, 2, 'road', 1, '2026-02-24 14:45:15'),
(3, 4, 3, 'empty', 1, '2026-02-24 14:45:15'),
(3, 4, 4, 'empty', 1, '2026-02-24 14:45:15'),
(3, 4, 5, 'empty', 1, '2026-02-24 14:45:15'),
(3, 4, 6, 'empty', 1, '2026-02-24 14:45:15'),
(3, 4, 7, 'empty', 1, '2026-02-24 14:45:15'),
(3, 4, 8, 'empty', 1, '2026-02-24 14:45:15'),
(3, 4, 9, 'empty', 1, '2026-02-24 14:45:15'),
(3, 5, 0, 'empty', 1, '2026-02-24 14:45:15'),
(3, 5, 1, 'empty', 1, '2026-02-24 14:45:15'),
(3, 5, 2, 'house', 1, '2026-02-24 14:45:15'),
(3, 5, 3, 'empty', 1, '2026-02-24 14:45:15'),
(3, 5, 4, 'empty', 1, '2026-02-24 14:45:15'),
(3, 5, 5, 'empty', 1, '2026-02-24 14:45:15'),
(3, 5, 6, 'empty', 1, '2026-02-24 14:45:15'),
(3, 5, 7, 'empty', 1, '2026-02-24 14:45:15'),
(3, 5, 8, 'empty', 1, '2026-02-24 14:45:15'),
(3, 5, 9, 'empty', 1, '2026-02-24 14:45:15'),
(3, 6, 0, 'empty', 1, '2026-02-24 14:45:15'),
(3, 6, 1, 'empty', 1, '2026-02-24 14:45:15'),
(3, 6, 2, 'empty', 1, '2026-02-24 14:45:15'),
(3, 6, 3, 'empty', 1, '2026-02-24 14:45:15'),
(3, 6, 4, 'empty', 1, '2026-02-24 14:45:15'),
(3, 6, 5, 'empty', 1, '2026-02-24 14:45:15'),
(3, 6, 6, 'empty', 1, '2026-02-24 14:45:15'),
(3, 6, 7, 'empty', 1, '2026-02-24 14:45:15'),
(3, 6, 8, 'empty', 1, '2026-02-24 14:45:15'),
(3, 6, 9, 'empty', 1, '2026-02-24 14:45:15'),
(3, 7, 0, 'empty', 1, '2026-02-24 14:45:15'),
(3, 7, 1, 'empty', 1, '2026-02-24 14:45:15'),
(3, 7, 2, 'empty', 1, '2026-02-24 14:45:15'),
(3, 7, 3, 'empty', 1, '2026-02-24 14:45:15'),
(3, 7, 4, 'empty', 1, '2026-02-24 14:45:15'),
(3, 7, 5, 'empty', 1, '2026-02-24 14:45:15'),
(3, 7, 6, 'empty', 1, '2026-02-24 14:45:15'),
(3, 7, 7, 'empty', 1, '2026-02-24 14:45:15'),
(3, 7, 8, 'empty', 1, '2026-02-24 14:45:15'),
(3, 7, 9, 'empty', 1, '2026-02-24 14:45:15'),
(3, 8, 0, 'empty', 1, '2026-02-24 14:45:15'),
(3, 8, 1, 'empty', 1, '2026-02-24 14:45:15'),
(3, 8, 2, 'empty', 1, '2026-02-24 14:45:15'),
(3, 8, 3, 'empty', 1, '2026-02-24 14:45:15'),
(3, 8, 4, 'empty', 1, '2026-02-24 14:45:15'),
(3, 8, 5, 'empty', 1, '2026-02-24 14:45:15'),
(3, 8, 6, 'empty', 1, '2026-02-24 14:45:15'),
(3, 8, 7, 'empty', 1, '2026-02-24 14:45:15'),
(3, 8, 8, 'empty', 1, '2026-02-24 14:45:15'),
(3, 8, 9, 'empty', 1, '2026-02-24 14:45:15'),
(3, 9, 0, 'empty', 1, '2026-02-24 14:45:15'),
(3, 9, 1, 'empty', 1, '2026-02-24 14:45:15'),
(3, 9, 2, 'empty', 1, '2026-02-24 14:45:15'),
(3, 9, 3, 'empty', 1, '2026-02-24 14:45:15'),
(3, 9, 4, 'empty', 1, '2026-02-24 14:45:15'),
(3, 9, 5, 'empty', 1, '2026-02-24 14:45:15'),
(3, 9, 6, 'empty', 1, '2026-02-24 14:45:15'),
(3, 9, 7, 'empty', 1, '2026-02-24 14:45:15'),
(3, 9, 8, 'empty', 1, '2026-02-24 14:45:15'),
(3, 9, 9, 'empty', 1, '2026-02-24 14:45:15'),
(4, 0, 0, 'empty', 1, '2026-02-25 12:22:00'),
(4, 0, 1, 'empty', 1, '2026-02-25 12:22:00'),
(4, 0, 2, 'empty', 1, '2026-02-25 12:22:00'),
(4, 0, 3, 'empty', 1, '2026-02-25 12:22:00'),
(4, 0, 4, 'empty', 1, '2026-02-25 12:22:00'),
(4, 0, 5, 'empty', 1, '2026-02-25 12:22:00'),
(4, 0, 6, 'empty', 1, '2026-02-25 12:22:00'),
(4, 0, 7, 'empty', 1, '2026-02-25 12:22:00'),
(4, 0, 8, 'empty', 1, '2026-02-25 12:22:00'),
(4, 0, 9, 'empty', 1, '2026-02-25 12:22:00'),
(4, 1, 0, 'empty', 1, '2026-02-25 12:22:00'),
(4, 1, 1, 'empty', 1, '2026-02-25 12:22:00'),
(4, 1, 2, 'empty', 1, '2026-02-25 12:22:00'),
(4, 1, 3, 'empty', 1, '2026-02-25 12:22:00'),
(4, 1, 4, 'empty', 1, '2026-02-25 12:22:00'),
(4, 1, 5, 'empty', 1, '2026-02-25 12:22:00'),
(4, 1, 6, 'empty', 1, '2026-02-25 12:22:00'),
(4, 1, 7, 'empty', 1, '2026-02-25 12:22:00'),
(4, 1, 8, 'empty', 1, '2026-02-25 12:22:00'),
(4, 1, 9, 'empty', 1, '2026-02-25 12:22:00'),
(4, 2, 0, 'empty', 1, '2026-02-25 12:22:00'),
(4, 2, 1, 'house', 1, '2026-02-25 12:22:00'),
(4, 2, 2, 'empty', 1, '2026-02-25 12:22:00'),
(4, 2, 3, 'house', 1, '2026-02-25 12:22:00'),
(4, 2, 4, 'empty', 1, '2026-02-25 12:22:00'),
(4, 2, 5, 'empty', 1, '2026-02-25 12:22:00'),
(4, 2, 6, 'empty', 1, '2026-02-25 12:22:00'),
(4, 2, 7, 'empty', 1, '2026-02-25 12:22:00'),
(4, 2, 8, 'empty', 1, '2026-02-25 12:22:00'),
(4, 2, 9, 'empty', 1, '2026-02-25 12:22:00'),
(4, 3, 0, 'empty', 1, '2026-02-25 12:22:00'),
(4, 3, 1, 'empty', 1, '2026-02-25 12:22:00'),
(4, 3, 2, 'empty', 1, '2026-02-25 12:22:00'),
(4, 3, 3, 'empty', 1, '2026-02-25 12:22:00'),
(4, 3, 4, 'empty', 1, '2026-02-25 12:22:00'),
(4, 3, 5, 'empty', 1, '2026-02-25 12:22:00'),
(4, 3, 6, 'empty', 1, '2026-02-25 12:22:00'),
(4, 3, 7, 'empty', 1, '2026-02-25 12:22:00'),
(4, 3, 8, 'empty', 1, '2026-02-25 12:22:00'),
(4, 3, 9, 'empty', 1, '2026-02-25 12:22:00'),
(4, 4, 0, 'empty', 1, '2026-02-25 12:22:00'),
(4, 4, 1, 'empty', 1, '2026-02-25 12:22:00'),
(4, 4, 2, 'house', 1, '2026-02-25 12:22:00'),
(4, 4, 3, 'empty', 1, '2026-02-25 12:22:00'),
(4, 4, 4, 'empty', 1, '2026-02-25 12:22:00'),
(4, 4, 5, 'empty', 1, '2026-02-25 12:22:00'),
(4, 4, 6, 'empty', 1, '2026-02-25 12:22:00'),
(4, 4, 7, 'empty', 1, '2026-02-25 12:22:00'),
(4, 4, 8, 'empty', 1, '2026-02-25 12:22:00'),
(4, 4, 9, 'empty', 1, '2026-02-25 12:22:00'),
(4, 5, 0, 'empty', 1, '2026-02-25 12:22:00'),
(4, 5, 1, 'empty', 1, '2026-02-25 12:22:00'),
(4, 5, 2, 'empty', 1, '2026-02-25 12:22:00'),
(4, 5, 3, 'empty', 1, '2026-02-25 12:22:00'),
(4, 5, 4, 'empty', 1, '2026-02-25 12:22:00'),
(4, 5, 5, 'empty', 1, '2026-02-25 12:22:00'),
(4, 5, 6, 'empty', 1, '2026-02-25 12:22:00'),
(4, 5, 7, 'empty', 1, '2026-02-25 12:22:00'),
(4, 5, 8, 'empty', 1, '2026-02-25 12:22:00'),
(4, 5, 9, 'empty', 1, '2026-02-25 12:22:00'),
(4, 6, 0, 'empty', 1, '2026-02-25 12:22:00'),
(4, 6, 1, 'empty', 1, '2026-02-25 12:22:00'),
(4, 6, 2, 'empty', 1, '2026-02-25 12:22:00'),
(4, 6, 3, 'empty', 1, '2026-02-25 12:22:00'),
(4, 6, 4, 'empty', 1, '2026-02-25 12:22:00'),
(4, 6, 5, 'empty', 1, '2026-02-25 12:22:00'),
(4, 6, 6, 'empty', 1, '2026-02-25 12:22:00'),
(4, 6, 7, 'empty', 1, '2026-02-25 12:22:00'),
(4, 6, 8, 'empty', 1, '2026-02-25 12:22:00'),
(4, 6, 9, 'empty', 1, '2026-02-25 12:22:00'),
(4, 7, 0, 'empty', 1, '2026-02-25 12:22:00'),
(4, 7, 1, 'house', 1, '2026-02-25 12:22:00'),
(4, 7, 2, 'empty', 1, '2026-02-25 12:22:00'),
(4, 7, 3, 'empty', 1, '2026-02-25 12:22:00'),
(4, 7, 4, 'empty', 1, '2026-02-25 12:22:00'),
(4, 7, 5, 'empty', 1, '2026-02-25 12:22:00'),
(4, 7, 6, 'empty', 1, '2026-02-25 12:22:00'),
(4, 7, 7, 'empty', 1, '2026-02-25 12:22:00'),
(4, 7, 8, 'empty', 1, '2026-02-25 12:22:00'),
(4, 7, 9, 'empty', 1, '2026-02-25 12:22:00'),
(4, 8, 0, 'empty', 1, '2026-02-25 12:22:00'),
(4, 8, 1, 'empty', 1, '2026-02-25 12:22:00'),
(4, 8, 2, 'empty', 1, '2026-02-25 12:22:00'),
(4, 8, 3, 'empty', 1, '2026-02-25 12:22:00'),
(4, 8, 4, 'empty', 1, '2026-02-25 12:22:00'),
(4, 8, 5, 'empty', 1, '2026-02-25 12:22:00'),
(4, 8, 6, 'empty', 1, '2026-02-25 12:22:00'),
(4, 8, 7, 'empty', 1, '2026-02-25 12:22:00'),
(4, 8, 8, 'empty', 1, '2026-02-25 12:22:00'),
(4, 8, 9, 'empty', 1, '2026-02-25 12:22:00'),
(4, 9, 0, 'empty', 1, '2026-02-25 12:22:00'),
(4, 9, 1, 'empty', 1, '2026-02-25 12:22:00'),
(4, 9, 2, 'empty', 1, '2026-02-25 12:22:00'),
(4, 9, 3, 'empty', 1, '2026-02-25 12:22:00'),
(4, 9, 4, 'empty', 1, '2026-02-25 12:22:00'),
(4, 9, 5, 'empty', 1, '2026-02-25 12:22:00'),
(4, 9, 6, 'empty', 1, '2026-02-25 12:22:00'),
(4, 9, 7, 'empty', 1, '2026-02-25 12:22:00'),
(4, 9, 8, 'empty', 1, '2026-02-25 12:22:00'),
(4, 9, 9, 'empty', 1, '2026-02-25 12:22:00'),
(5, 0, 0, 'empty', 1, '2026-02-25 13:03:14'),
(5, 0, 1, 'empty', 1, '2026-02-25 13:03:14'),
(5, 0, 2, 'empty', 1, '2026-02-25 13:03:14'),
(5, 0, 3, 'empty', 1, '2026-02-25 13:03:14'),
(5, 0, 4, 'empty', 1, '2026-02-25 13:03:14'),
(5, 0, 5, 'empty', 1, '2026-02-25 13:03:14'),
(5, 0, 6, 'empty', 1, '2026-02-25 13:03:14'),
(5, 0, 7, 'empty', 1, '2026-02-25 13:03:14'),
(5, 0, 8, 'empty', 1, '2026-02-25 13:03:14'),
(5, 0, 9, 'empty', 1, '2026-02-25 13:03:14'),
(5, 1, 0, 'empty', 1, '2026-02-25 13:03:14'),
(5, 1, 1, 'empty', 1, '2026-02-25 13:03:14'),
(5, 1, 2, 'empty', 1, '2026-02-25 13:03:14'),
(5, 1, 3, 'empty', 1, '2026-02-25 13:03:14'),
(5, 1, 4, 'empty', 1, '2026-02-25 13:03:14'),
(5, 1, 5, 'empty', 1, '2026-02-25 13:03:14'),
(5, 1, 6, 'empty', 1, '2026-02-25 13:03:14'),
(5, 1, 7, 'empty', 1, '2026-02-25 13:03:14'),
(5, 1, 8, 'empty', 1, '2026-02-25 13:03:14'),
(5, 1, 9, 'empty', 1, '2026-02-25 13:03:14'),
(5, 2, 0, 'empty', 1, '2026-02-25 13:03:14'),
(5, 2, 1, 'empty', 1, '2026-02-25 13:03:14'),
(5, 2, 2, 'empty', 1, '2026-02-25 13:03:14'),
(5, 2, 3, 'empty', 1, '2026-02-25 13:03:14'),
(5, 2, 4, 'empty', 1, '2026-02-25 13:03:14'),
(5, 2, 5, 'empty', 1, '2026-02-25 13:03:14'),
(5, 2, 6, 'empty', 1, '2026-02-25 13:03:14'),
(5, 2, 7, 'empty', 1, '2026-02-25 13:03:14'),
(5, 2, 8, 'empty', 1, '2026-02-25 13:03:14'),
(5, 2, 9, 'empty', 1, '2026-02-25 13:03:14'),
(5, 3, 0, 'empty', 1, '2026-02-25 13:03:14'),
(5, 3, 1, 'empty', 1, '2026-02-25 13:03:14'),
(5, 3, 2, 'empty', 1, '2026-02-25 13:03:14'),
(5, 3, 3, 'empty', 1, '2026-02-25 13:03:14'),
(5, 3, 4, 'empty', 1, '2026-02-25 13:03:14'),
(5, 3, 5, 'empty', 1, '2026-02-25 13:03:14'),
(5, 3, 6, 'empty', 1, '2026-02-25 13:03:14'),
(5, 3, 7, 'empty', 1, '2026-02-25 13:03:14'),
(5, 3, 8, 'empty', 1, '2026-02-25 13:03:14'),
(5, 3, 9, 'empty', 1, '2026-02-25 13:03:14'),
(5, 4, 0, 'empty', 1, '2026-02-25 13:03:14'),
(5, 4, 1, 'empty', 1, '2026-02-25 13:03:14'),
(5, 4, 2, 'empty', 1, '2026-02-25 13:03:14'),
(5, 4, 3, 'empty', 1, '2026-02-25 13:03:14'),
(5, 4, 4, 'empty', 1, '2026-02-25 13:03:14'),
(5, 4, 5, 'empty', 1, '2026-02-25 13:03:14'),
(5, 4, 6, 'empty', 1, '2026-02-25 13:03:14'),
(5, 4, 7, 'empty', 1, '2026-02-25 13:03:14'),
(5, 4, 8, 'empty', 1, '2026-02-25 13:03:14'),
(5, 4, 9, 'empty', 1, '2026-02-25 13:03:14'),
(5, 5, 0, 'empty', 1, '2026-02-25 13:03:14'),
(5, 5, 1, 'empty', 1, '2026-02-25 13:03:14'),
(5, 5, 2, 'empty', 1, '2026-02-25 13:03:14'),
(5, 5, 3, 'empty', 1, '2026-02-25 13:03:14'),
(5, 5, 4, 'empty', 1, '2026-02-25 13:03:14'),
(5, 5, 5, 'empty', 1, '2026-02-25 13:03:14'),
(5, 5, 6, 'empty', 1, '2026-02-25 13:03:14'),
(5, 5, 7, 'empty', 1, '2026-02-25 13:03:14'),
(5, 5, 8, 'empty', 1, '2026-02-25 13:03:14'),
(5, 5, 9, 'empty', 1, '2026-02-25 13:03:14'),
(5, 6, 0, 'empty', 1, '2026-02-25 13:03:14'),
(5, 6, 1, 'empty', 1, '2026-02-25 13:03:14'),
(5, 6, 2, 'empty', 1, '2026-02-25 13:03:14'),
(5, 6, 3, 'empty', 1, '2026-02-25 13:03:14'),
(5, 6, 4, 'empty', 1, '2026-02-25 13:03:14'),
(5, 6, 5, 'empty', 1, '2026-02-25 13:03:14'),
(5, 6, 6, 'empty', 1, '2026-02-25 13:03:14'),
(5, 6, 7, 'empty', 1, '2026-02-25 13:03:14'),
(5, 6, 8, 'empty', 1, '2026-02-25 13:03:14'),
(5, 6, 9, 'empty', 1, '2026-02-25 13:03:14'),
(5, 7, 0, 'empty', 1, '2026-02-25 13:03:14'),
(5, 7, 1, 'empty', 1, '2026-02-25 13:03:14'),
(5, 7, 2, 'empty', 1, '2026-02-25 13:03:14'),
(5, 7, 3, 'empty', 1, '2026-02-25 13:03:14'),
(5, 7, 4, 'empty', 1, '2026-02-25 13:03:14'),
(5, 7, 5, 'empty', 1, '2026-02-25 13:03:14'),
(5, 7, 6, 'empty', 1, '2026-02-25 13:03:14'),
(5, 7, 7, 'empty', 1, '2026-02-25 13:03:14'),
(5, 7, 8, 'empty', 1, '2026-02-25 13:03:14'),
(5, 7, 9, 'empty', 1, '2026-02-25 13:03:14'),
(5, 8, 0, 'empty', 1, '2026-02-25 13:03:14'),
(5, 8, 1, 'empty', 1, '2026-02-25 13:03:14'),
(5, 8, 2, 'empty', 1, '2026-02-25 13:03:14'),
(5, 8, 3, 'empty', 1, '2026-02-25 13:03:14'),
(5, 8, 4, 'empty', 1, '2026-02-25 13:03:14'),
(5, 8, 5, 'empty', 1, '2026-02-25 13:03:14'),
(5, 8, 6, 'empty', 1, '2026-02-25 13:03:14'),
(5, 8, 7, 'empty', 1, '2026-02-25 13:03:14'),
(5, 8, 8, 'empty', 1, '2026-02-25 13:03:14'),
(5, 8, 9, 'empty', 1, '2026-02-25 13:03:14'),
(5, 9, 0, 'empty', 1, '2026-02-25 13:03:14'),
(5, 9, 1, 'empty', 1, '2026-02-25 13:03:14'),
(5, 9, 2, 'empty', 1, '2026-02-25 13:03:14'),
(5, 9, 3, 'empty', 1, '2026-02-25 13:03:14'),
(5, 9, 4, 'empty', 1, '2026-02-25 13:03:14'),
(5, 9, 5, 'empty', 1, '2026-02-25 13:03:14'),
(5, 9, 6, 'empty', 1, '2026-02-25 13:03:14'),
(5, 9, 7, 'empty', 1, '2026-02-25 13:03:14'),
(5, 9, 8, 'empty', 1, '2026-02-25 13:03:14'),
(5, 9, 9, 'empty', 1, '2026-02-25 13:03:14'),
(6, 0, 0, 'empty', 1, '2026-02-25 13:33:11'),
(6, 0, 1, 'empty', 1, '2026-02-25 13:33:11'),
(6, 0, 2, 'empty', 1, '2026-02-25 13:33:11'),
(6, 0, 3, 'empty', 1, '2026-02-25 13:33:11'),
(6, 0, 4, 'empty', 1, '2026-02-25 13:33:11'),
(6, 0, 5, 'empty', 1, '2026-02-25 13:33:11'),
(6, 0, 6, 'empty', 1, '2026-02-25 13:33:11'),
(6, 0, 7, 'empty', 1, '2026-02-25 13:33:11'),
(6, 0, 8, 'empty', 1, '2026-02-25 13:33:11'),
(6, 0, 9, 'empty', 1, '2026-02-25 13:33:11'),
(6, 1, 0, 'empty', 1, '2026-02-25 13:33:11'),
(6, 1, 1, 'empty', 1, '2026-02-25 13:33:11'),
(6, 1, 2, 'house', 1, '2026-02-25 13:33:11'),
(6, 1, 3, 'empty', 1, '2026-02-25 13:33:11'),
(6, 1, 4, 'empty', 1, '2026-02-25 13:33:11'),
(6, 1, 5, 'empty', 1, '2026-02-25 13:33:11'),
(6, 1, 6, 'empty', 1, '2026-02-25 13:33:11'),
(6, 1, 7, 'empty', 1, '2026-02-25 13:33:11'),
(6, 1, 8, 'empty', 1, '2026-02-25 13:33:11'),
(6, 1, 9, 'empty', 1, '2026-02-25 13:33:11'),
(6, 2, 0, 'empty', 1, '2026-02-25 13:33:11'),
(6, 2, 1, 'empty', 1, '2026-02-25 13:33:11'),
(6, 2, 2, 'empty', 1, '2026-02-25 13:33:11'),
(6, 2, 3, 'empty', 1, '2026-02-25 13:33:11'),
(6, 2, 4, 'empty', 1, '2026-02-25 13:33:11'),
(6, 2, 5, 'empty', 1, '2026-02-25 13:33:11'),
(6, 2, 6, 'empty', 1, '2026-02-25 13:33:11'),
(6, 2, 7, 'empty', 1, '2026-02-25 13:33:11'),
(6, 2, 8, 'empty', 1, '2026-02-25 13:33:11'),
(6, 2, 9, 'empty', 1, '2026-02-25 13:33:11'),
(6, 3, 0, 'empty', 1, '2026-02-25 13:33:11'),
(6, 3, 1, 'empty', 1, '2026-02-25 13:33:11'),
(6, 3, 2, 'empty', 1, '2026-02-25 13:33:11'),
(6, 3, 3, 'empty', 1, '2026-02-25 13:33:11'),
(6, 3, 4, 'empty', 1, '2026-02-25 13:33:11'),
(6, 3, 5, 'empty', 1, '2026-02-25 13:33:11'),
(6, 3, 6, 'empty', 1, '2026-02-25 13:33:11'),
(6, 3, 7, 'empty', 1, '2026-02-25 13:33:11'),
(6, 3, 8, 'empty', 1, '2026-02-25 13:33:11'),
(6, 3, 9, 'empty', 1, '2026-02-25 13:33:11'),
(6, 4, 0, 'empty', 1, '2026-02-25 13:33:11'),
(6, 4, 1, 'empty', 1, '2026-02-25 13:33:11'),
(6, 4, 2, 'empty', 1, '2026-02-25 13:33:11'),
(6, 4, 3, 'empty', 1, '2026-02-25 13:33:11'),
(6, 4, 4, 'empty', 1, '2026-02-25 13:33:11'),
(6, 4, 5, 'empty', 1, '2026-02-25 13:33:11'),
(6, 4, 6, 'empty', 1, '2026-02-25 13:33:11'),
(6, 4, 7, 'empty', 1, '2026-02-25 13:33:11'),
(6, 4, 8, 'empty', 1, '2026-02-25 13:33:11'),
(6, 4, 9, 'empty', 1, '2026-02-25 13:33:11'),
(6, 5, 0, 'empty', 1, '2026-02-25 13:33:11'),
(6, 5, 1, 'empty', 1, '2026-02-25 13:33:11'),
(6, 5, 2, 'house', 1, '2026-02-25 13:33:11'),
(6, 5, 3, 'empty', 1, '2026-02-25 13:33:11'),
(6, 5, 4, 'empty', 1, '2026-02-25 13:33:11'),
(6, 5, 5, 'empty', 1, '2026-02-25 13:33:11'),
(6, 5, 6, 'empty', 1, '2026-02-25 13:33:11'),
(6, 5, 7, 'empty', 1, '2026-02-25 13:33:11'),
(6, 5, 8, 'empty', 1, '2026-02-25 13:33:11'),
(6, 5, 9, 'empty', 1, '2026-02-25 13:33:11'),
(6, 6, 0, 'empty', 1, '2026-02-25 13:33:11'),
(6, 6, 1, 'empty', 1, '2026-02-25 13:33:11'),
(6, 6, 2, 'empty', 1, '2026-02-25 13:33:11'),
(6, 6, 3, 'empty', 1, '2026-02-25 13:33:11'),
(6, 6, 4, 'empty', 1, '2026-02-25 13:33:11'),
(6, 6, 5, 'empty', 1, '2026-02-25 13:33:11'),
(6, 6, 6, 'empty', 1, '2026-02-25 13:33:11'),
(6, 6, 7, 'empty', 1, '2026-02-25 13:33:11'),
(6, 6, 8, 'empty', 1, '2026-02-25 13:33:11'),
(6, 6, 9, 'empty', 1, '2026-02-25 13:33:11'),
(6, 7, 0, 'empty', 1, '2026-02-25 13:33:11'),
(6, 7, 1, 'empty', 1, '2026-02-25 13:33:11'),
(6, 7, 2, 'empty', 1, '2026-02-25 13:33:11'),
(6, 7, 3, 'empty', 1, '2026-02-25 13:33:11'),
(6, 7, 4, 'empty', 1, '2026-02-25 13:33:11'),
(6, 7, 5, 'empty', 1, '2026-02-25 13:33:11'),
(6, 7, 6, 'empty', 1, '2026-02-25 13:33:11'),
(6, 7, 7, 'empty', 1, '2026-02-25 13:33:11'),
(6, 7, 8, 'empty', 1, '2026-02-25 13:33:11'),
(6, 7, 9, 'empty', 1, '2026-02-25 13:33:11'),
(6, 8, 0, 'empty', 1, '2026-02-25 13:33:11'),
(6, 8, 1, 'empty', 1, '2026-02-25 13:33:11'),
(6, 8, 2, 'empty', 1, '2026-02-25 13:33:11'),
(6, 8, 3, 'empty', 1, '2026-02-25 13:33:11'),
(6, 8, 4, 'empty', 1, '2026-02-25 13:33:11'),
(6, 8, 5, 'empty', 1, '2026-02-25 13:33:11'),
(6, 8, 6, 'empty', 1, '2026-02-25 13:33:11'),
(6, 8, 7, 'empty', 1, '2026-02-25 13:33:11'),
(6, 8, 8, 'empty', 1, '2026-02-25 13:33:11'),
(6, 8, 9, 'empty', 1, '2026-02-25 13:33:11'),
(6, 9, 0, 'empty', 1, '2026-02-25 13:33:11'),
(6, 9, 1, 'empty', 1, '2026-02-25 13:33:11'),
(6, 9, 2, 'empty', 1, '2026-02-25 13:33:11'),
(6, 9, 3, 'empty', 1, '2026-02-25 13:33:11'),
(6, 9, 4, 'empty', 1, '2026-02-25 13:33:11'),
(6, 9, 5, 'empty', 1, '2026-02-25 13:33:11'),
(6, 9, 6, 'empty', 1, '2026-02-25 13:33:11'),
(6, 9, 7, 'empty', 1, '2026-02-25 13:33:11'),
(6, 9, 8, 'empty', 1, '2026-02-25 13:33:11'),
(6, 9, 9, 'empty', 1, '2026-02-25 13:33:11');

-- --------------------------------------------------------

--
-- Table structure for table `crises`
--

CREATE TABLE `crises` (
  `id` int(11) NOT NULL,
  `title` varchar(120) NOT NULL,
  `story` text NOT NULL,
  `ml_topic` varchar(50) NOT NULL,
  `difficulty` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crises`
--

INSERT INTO `crises` (`id`, `title`, `story`, `ml_topic`, `difficulty`) VALUES
(1, 'Traffic Gridlock', 'Your roads are jammed. You must group neighborhoods by traffic patterns to redesign routes.', 'clustering', 1),
(2, 'Power Shortage Forecast', 'Blackouts are looming. Predict tomorrow’s energy demand to prevent outages.', 'regression', 1),
(3, 'Crime Patrol Allocation', 'Incidents are rising. Classify zones as high/medium/low risk to allocate patrols.', 'classification', 1),
(4, 'Model Audit', 'Citizens complain about unfair services. Evaluate model performance and detect imbalance.', 'evaluation', 2),
(5, 'Dirty Sensor Data', 'Sensors are noisy. Clean the dataset and handle missing values before training.', 'preprocessing', 1),
(6, 'Housing Demand Surge', 'New citizens arrive. You must choose a model to predict housing demand from past data.', 'regression', 1),
(7, 'Citizen Segmentation', 'Group citizens by service usage to plan resource distribution.', 'clustering', 1),
(8, 'Fair Services Review', 'Check whether your model treats districts fairly and evaluate more than accuracy.', 'evaluation', 2);

-- --------------------------------------------------------

--
-- Table structure for table `leaderboard_scores`
--

CREATE TABLE `leaderboard_scores` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaderboard_scores`
--

INSERT INTO `leaderboard_scores` (`id`, `user_id`, `score`, `created_at`) VALUES
(1, 1, 120, '2026-02-24 13:44:13'),
(2, 1, 240, '2026-02-24 13:44:40'),
(3, 2, 120, '2026-02-24 14:26:29'),
(4, 2, 270, '2026-02-24 14:26:53'),
(5, 2, 390, '2026-02-24 14:30:01'),
(6, 2, 510, '2026-02-24 14:32:01'),
(7, 2, 630, '2026-02-24 14:32:02'),
(8, 2, 750, '2026-02-24 14:32:05'),
(9, 2, 870, '2026-02-24 14:32:06'),
(10, 2, 990, '2026-02-24 14:32:06'),
(11, 2, 1110, '2026-02-24 14:32:06'),
(12, 2, 1230, '2026-02-24 14:32:06'),
(13, 2, 1350, '2026-02-24 14:32:06'),
(14, 2, 1470, '2026-02-24 14:32:08'),
(15, 2, 1590, '2026-02-24 14:32:08'),
(16, 2, 1710, '2026-02-24 14:32:09'),
(17, 2, 1830, '2026-02-24 14:32:09'),
(18, 2, 1950, '2026-02-24 14:32:09'),
(19, 2, 2070, '2026-02-24 14:32:09'),
(20, 2, 2190, '2026-02-24 14:32:09'),
(21, 2, 2310, '2026-02-24 14:32:10'),
(22, 2, 2430, '2026-02-24 14:32:10'),
(23, 2, 2550, '2026-02-24 14:32:10'),
(24, 2, 2670, '2026-02-24 14:32:10'),
(25, 2, 2790, '2026-02-24 14:32:10'),
(26, 2, 2910, '2026-02-24 14:32:10'),
(27, 2, 3030, '2026-02-24 14:32:11'),
(28, 2, 3150, '2026-02-24 14:32:11'),
(29, 2, 3270, '2026-02-24 14:32:11'),
(30, 2, 3390, '2026-02-24 14:32:11'),
(31, 2, 3510, '2026-02-24 14:32:11'),
(32, 2, 3630, '2026-02-24 14:32:12'),
(33, 2, 3750, '2026-02-24 14:32:12'),
(34, 2, 3870, '2026-02-24 14:32:12'),
(35, 2, 3990, '2026-02-24 14:32:12'),
(36, 2, 4110, '2026-02-24 14:32:12'),
(37, 2, 4230, '2026-02-24 14:32:13'),
(38, 2, 4350, '2026-02-24 14:32:13'),
(39, 2, 4470, '2026-02-24 14:32:13'),
(40, 2, 4590, '2026-02-24 14:32:13'),
(41, 2, 4710, '2026-02-24 14:32:13'),
(42, 2, 4830, '2026-02-24 14:32:14'),
(43, 2, 4950, '2026-02-24 14:32:14'),
(44, 2, 5070, '2026-02-24 14:32:14'),
(45, 2, 5190, '2026-02-24 14:32:14'),
(46, 2, 5310, '2026-02-24 14:32:14'),
(47, 2, 5430, '2026-02-24 14:32:15'),
(48, 2, 5550, '2026-02-24 14:32:15'),
(49, 2, 5670, '2026-02-24 14:32:15'),
(50, 2, 5790, '2026-02-24 14:32:15'),
(51, 2, 5910, '2026-02-24 14:32:15'),
(52, 2, 6030, '2026-02-24 14:32:16'),
(53, 2, 6150, '2026-02-24 14:32:16'),
(54, 2, 6270, '2026-02-24 14:32:16'),
(55, 2, 6390, '2026-02-24 14:32:16'),
(56, 2, 6510, '2026-02-24 14:32:16'),
(57, 2, 6630, '2026-02-24 14:32:17'),
(58, 2, 6750, '2026-02-24 14:32:17'),
(59, 2, 6870, '2026-02-24 14:32:17'),
(60, 2, 6990, '2026-02-24 14:32:17'),
(61, 2, 7110, '2026-02-24 14:32:17'),
(62, 2, 7230, '2026-02-24 14:32:18'),
(63, 2, 7350, '2026-02-24 14:32:18'),
(64, 2, 7470, '2026-02-24 14:32:18'),
(65, 2, 7590, '2026-02-24 14:32:18'),
(66, 2, 7710, '2026-02-24 14:32:18'),
(67, 2, 7830, '2026-02-24 14:32:18'),
(68, 2, 7950, '2026-02-24 14:32:41'),
(69, 3, 150, '2026-02-24 14:45:47'),
(70, 3, 270, '2026-02-24 14:45:57'),
(71, 2, 8070, '2026-02-24 15:56:48'),
(72, 2, 8220, '2026-02-24 16:23:35'),
(73, 2, 8380, '2026-02-24 16:23:56'),
(74, 4, 150, '2026-02-25 12:22:29'),
(75, 4, 270, '2026-02-25 12:23:48'),
(76, 4, 214, '2026-02-25 12:25:22'),
(77, 4, 364, '2026-02-25 12:25:23'),
(78, 4, 514, '2026-02-25 12:25:24'),
(79, 4, 664, '2026-02-25 12:25:24'),
(80, 4, 814, '2026-02-25 12:25:24'),
(81, 4, 964, '2026-02-25 12:25:24'),
(82, 4, 1114, '2026-02-25 12:25:25'),
(83, 4, 1264, '2026-02-25 12:25:25'),
(84, 4, 1414, '2026-02-25 12:25:25'),
(85, 4, 1564, '2026-02-25 12:25:25'),
(86, 4, 1714, '2026-02-25 12:25:25'),
(87, 5, 120, '2026-02-25 13:03:47'),
(88, 5, 240, '2026-02-25 13:03:48'),
(89, 5, 360, '2026-02-25 13:03:48'),
(90, 6, 160, '2026-02-25 13:34:19');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `crisis_id` int(11) NOT NULL,
  `prompt` text NOT NULL,
  `task_type` varchar(50) NOT NULL,
  `correct_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`correct_json`)),
  `reward_points` int(11) NOT NULL DEFAULT 100,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `crisis_id`, `prompt`, `task_type`, `correct_json`, `reward_points`, `created_at`) VALUES
(1, 1, 'Which ML technique is best for grouping neighborhoods by similar traffic patterns?', 'mcq', '{\"options\": [\"Regression\", \"Clustering\", \"Classification\", \"Reinforcement Learning\"], \"answer\": \"Clustering\"}', 120, '2026-02-24 12:59:39'),
(2, 2, 'You need to predict continuous energy demand (kWh). Which task is this?', 'mcq', '{\"options\": [\"Classification\", \"Regression\", \"Clustering\", \"Dimensionality Reduction\"], \"answer\": \"Regression\"}', 120, '2026-02-24 12:59:39'),
(3, 3, 'You must label zones as high/medium/low risk. Which task is this?', 'mcq', '{\"options\": [\"Regression\", \"Clustering\", \"Classification\", \"Anomaly Detection\"], \"answer\": \"Classification\"}', 120, '2026-02-24 12:59:39'),
(4, 4, 'Your model has 95% accuracy but fails most minority cases. Which metric helps reveal this problem?', 'mcq', '{\"options\": [\"Accuracy\", \"Precision/Recall or F1\", \"Mean Squared Error\", \"Silhouette Score\"], \"answer\": \"Precision/Recall or F1\"}', 150, '2026-02-24 12:59:39'),
(5, 5, 'Sensor data has missing values. What is a common first step?', 'mcq', '{\"options\": [\"Delete the entire dataset\", \"Impute missing values\", \"Only tune hyperparameters\", \"Skip preprocessing\"], \"answer\": \"Impute missing values\"}', 120, '2026-02-24 12:59:39'),
(6, 5, 'Order the Machine Learning pipeline steps correctly.', 'ordering', '{\"items\": [\"Collecting data\", \"Preparing the data\", \"Choosing a model\", \"Training the model\", \"Evaluating the model\", \"Parameter tuning\", \"Making predictions\"], \"answer\": [\"Collecting data\", \"Preparing the data\", \"Choosing a model\", \"Training the model\", \"Evaluating the model\", \"Parameter tuning\", \"Making predictions\"]}', 200, '2026-02-24 15:48:14'),
(7, 1, 'Match each ML approach to its common city use-case.', 'match', '{\"left\": [\"Classification\", \"Regression\", \"Clustering\"], \"right\": [\"Predict energy demand\", \"Group neighborhoods by traffic\", \"Label zones as high risk\"], \"answer\": {\"Classification\": \"Label zones as high risk\", \"Regression\": \"Predict energy demand\", \"Clustering\": \"Group neighborhoods by traffic\"}}', 180, '2026-02-24 15:48:14'),
(8, 3, 'In this KNN classifier, which line correctly creates the model with k=5?', 'code_mcq', '{\"code\": \"from sklearn.neighbors import KNeighborsClassifier\\n\\n# X_train, y_train already prepared\\n\\nA) model = KNeighborsClassifier(n_neighbors=5)\\nB) model = KNeighborsClassifier(k=5)\\nC) model = KNNClassifier(n=5)\\nD) model = KNeighbors(n_neighbors=5)\", \"options\": [\"A\", \"B\", \"C\", \"D\"], \"answer\": \"A\"}', 170, '2026-02-24 15:48:14'),
(9, 2, 'Which function is typically used to split data into training and test sets in scikit-learn?', 'code_mcq', '{\"code\": \"A) split_train_test(X, y)\\nB) train_test_split(X, y, test_size=0.2, random_state=42)\\nC) holdout(X, y)\\nD) data_split(X, y)\", \"options\": [\"A\", \"B\", \"C\", \"D\"], \"answer\": \"B\"}', 150, '2026-02-24 15:48:14'),
(10, 4, 'Which snippet is best for seeing precision/recall/F1 for each class?', 'code_mcq', '{\"code\": \"A) accuracy_score(y_test, y_pred)\\nB) mean_squared_error(y_test, y_pred)\\nC) classification_report(y_test, y_pred)\\nD) silhouette_score(X, labels)\", \"options\": [\"A\", \"B\", \"C\", \"D\"], \"answer\": \"C\"}', 160, '2026-02-24 15:48:14'),
(11, 3, 'Which line makes predictions after fitting a classifier?', 'code_mcq', '{\"code\": \"model.fit(X_train, y_train)\\n\\nA) y_pred = model.predict(X_test)\\nB) y_pred = model.score(X_test)\\nC) y_pred = model.transform(X_test)\\nD) y_pred = model.compile(X_test)\", \"options\": [\"A\", \"B\", \"C\", \"D\"], \"answer\": \"A\"}', 150, '2026-02-24 15:55:51'),
(12, 1, 'KNN is sensitive to feature scale. Which preprocessing step helps most?', 'code_mcq', '{\"code\": \"A) StandardScaler()\\nB) OneHotEncoder()\\nC) LabelEncoder()\\nD) PCA(n_components=2)\", \"options\": [\"A\", \"B\", \"C\", \"D\"], \"answer\": \"A\"}', 150, '2026-02-24 15:55:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `created_at`) VALUES
(1, 'Ethan', '2026-02-24 13:43:39'),
(2, 'test', '2026-02-24 14:01:46'),
(3, 'Ron', '2026-02-24 14:45:15'),
(4, 'Bogdan', '2026-02-25 12:22:00'),
(5, 'ED', '2026-02-25 13:03:13'),
(6, 'Ensi', '2026-02-25 13:33:11');

-- --------------------------------------------------------

--
-- Table structure for table `user_badges`
--

CREATE TABLE `user_badges` (
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `earned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_badges`
--

INSERT INTO `user_badges` (`user_id`, `badge_id`, `earned_at`) VALUES
(2, 1, '2026-02-24 14:30:01'),
(2, 2, '2026-02-24 14:32:02'),
(4, 1, '2026-02-25 12:25:23'),
(4, 2, '2026-02-25 12:25:24'),
(5, 1, '2026-02-25 13:03:48');

-- --------------------------------------------------------

--
-- Table structure for table `user_state`
--

CREATE TABLE `user_state` (
  `user_id` int(11) NOT NULL,
  `revenue` int(11) NOT NULL DEFAULT 0,
  `level` int(11) NOT NULL DEFAULT 1,
  `xp` int(11) NOT NULL DEFAULT 0,
  `booster_tokens` int(11) NOT NULL DEFAULT 2,
  `last_crisis_id` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `income_per_turn` int(11) NOT NULL DEFAULT 5,
  `happiness` int(11) NOT NULL DEFAULT 50,
  `tasks_correct` int(11) NOT NULL DEFAULT 0,
  `mcq_correct` int(11) NOT NULL DEFAULT 0,
  `ordering_correct` int(11) NOT NULL DEFAULT 0,
  `match_correct` int(11) NOT NULL DEFAULT 0,
  `code_correct` int(11) NOT NULL DEFAULT 0,
  `boosters_used` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_state`
--

INSERT INTO `user_state` (`user_id`, `revenue`, `level`, `xp`, `booster_tokens`, `last_crisis_id`, `updated_at`, `income_per_turn`, `happiness`, `tasks_correct`, `mcq_correct`, `ordering_correct`, `match_correct`, `code_correct`, `boosters_used`) VALUES
(1, 240, 1, 240, 2, 2, '2026-02-24 13:44:40', 5, 50, 0, 0, 0, 0, 0, 0),
(2, 8110, 1, 8380, 2, 4, '2026-02-25 11:56:02', 17, 51, 0, 0, 0, 0, 0, 0),
(3, 318, 1, 508, 1, 3, '2026-02-24 14:47:07', 14, 49, 0, 0, 0, 0, 0, 0),
(4, 1714, 1, 1954, 1, 2, '2026-02-25 12:25:25', 17, 58, 0, 0, 0, 0, 0, 0),
(5, 360, 1, 360, 2, 6, '2026-02-25 13:03:48', 5, 50, 0, 0, 0, 0, 0, 0),
(6, 40, 1, 160, 2, 4, '2026-02-25 13:42:00', 11, 54, 0, 0, 0, 0, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`,`created_at`);

--
-- Indexes for table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `city_tiles`
--
ALTER TABLE `city_tiles`
  ADD PRIMARY KEY (`user_id`,`x`,`y`);

--
-- Indexes for table `crises`
--
ALTER TABLE `crises`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leaderboard_scores`
--
ALTER TABLE `leaderboard_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `score` (`score`),
  ADD KEY `fk_lb_user` (`user_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_task_crisis` (`crisis_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_badges`
--
ALTER TABLE `user_badges`
  ADD PRIMARY KEY (`user_id`,`badge_id`),
  ADD KEY `fk_ub_badge` (`badge_id`);

--
-- Indexes for table `user_state`
--
ALTER TABLE `user_state`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=234;

--
-- AUTO_INCREMENT for table `badges`
--
ALTER TABLE `badges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `crises`
--
ALTER TABLE `crises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `leaderboard_scores`
--
ALTER TABLE `leaderboard_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `fk_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `city_tiles`
--
ALTER TABLE `city_tiles`
  ADD CONSTRAINT `fk_tiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leaderboard_scores`
--
ALTER TABLE `leaderboard_scores`
  ADD CONSTRAINT `fk_lb_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_task_crisis` FOREIGN KEY (`crisis_id`) REFERENCES `crises` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_badges`
--
ALTER TABLE `user_badges`
  ADD CONSTRAINT `fk_ub_badge` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ub_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_state`
--
ALTER TABLE `user_state`
  ADD CONSTRAINT `fk_state_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
