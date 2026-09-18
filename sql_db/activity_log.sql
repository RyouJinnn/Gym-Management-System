-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 18, 2026 at 11:13 AM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gym_signup`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `activity_id` int(11) NOT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `recipient_id` int(11) DEFAULT NULL,
  `actor_type` varchar(20) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`activity_id`, `actor_id`, `recipient_id`, `actor_type`, `action`, `description`, `created_at`) VALUES
(1, 1, NULL, 'Admin', 'Member Registration', 'Miguel Anganangan was added as a new member.', '2026-09-13 01:43:41'),
(2, 1, NULL, 'Admin', 'Payment Submission', 'A payment of â‚±299.00 was added for Member #36 with status Approved.', '2026-09-13 04:11:03'),
(3, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-15 14:14:38'),
(4, 27, NULL, 'Member', 'Login Verification Started', 'Ryou requested a login verification code.', '2026-09-15 23:00:37'),
(5, 27, NULL, 'Member', 'Member Login', 'Ryou successfully logged in.', '2026-09-15 23:01:04'),
(6, 27, NULL, 'Member', 'Payment Submission', ' submitted a payment of â‚±299.00 for the Student membership plan using GCash.', '2026-09-15 23:03:20'),
(7, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-15 23:06:37'),
(8, 1, NULL, 'Admin', 'Payment Update', 'Payment #10 for Member #27 was updated to Declined.', '2026-09-15 23:07:24'),
(9, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 10:07:44'),
(10, 1, NULL, 'Admin', 'Staff Update', 'Jong Gii staff account information was updated.', '2026-09-16 10:07:56'),
(11, 1, NULL, 'Admin', 'Admin Logout', 'admin logged out successfully.', '2026-09-16 10:08:51'),
(12, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 10:08:59'),
(13, 1, NULL, 'Admin', 'Admin Logout', 'admin logged out successfully.', '2026-09-16 10:09:28'),
(14, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 10:09:38'),
(15, 1, NULL, 'Admin', 'Admin Logout', 'admin logged out successfully.', '2026-09-16 10:11:25'),
(16, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 10:11:40'),
(17, 1, NULL, 'Admin', 'Member Activation', 'Rene Baterbonia member account was set to Active.', '2026-09-16 10:15:56'),
(18, 1, NULL, 'Admin', 'Admin Logout', 'admin logged out successfully.', '2026-09-16 10:16:14'),
(19, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 10:16:18'),
(20, 1, NULL, 'Admin', 'Admin Logout', 'admin logged out successfully.', '2026-09-16 10:20:55'),
(21, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 10:23:22'),
(22, 1, NULL, 'Admin', 'Admin Logout', 'admin logged out successfully.', '2026-09-16 10:23:31'),
(23, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 10:23:35'),
(24, 1, NULL, 'Admin', 'Admin Logout', 'admin logged out successfully.', '2026-09-16 10:24:27'),
(25, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 10:28:45'),
(26, 1, NULL, 'Admin', 'Admin Logout', 'admin logged out successfully.', '2026-09-16 10:28:54'),
(27, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 10:29:33'),
(28, 1, NULL, 'Admin', 'Admin Logout', 'admin logged out successfully.', '2026-09-16 10:29:44'),
(29, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 10:33:01'),
(30, 1, NULL, 'Admin', 'Admin Logout', 'admin logged out successfully.', '2026-09-16 10:34:18'),
(31, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 10:36:26'),
(32, 1, NULL, 'Admin', 'Admin Logout', 'admin logged out successfully.', '2026-09-16 10:36:45'),
(33, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 10:44:25'),
(34, 1, NULL, 'Admin', 'Admin Logout', 'admin logged out successfully.', '2026-09-16 10:44:47'),
(35, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 10:46:53'),
(36, 1, NULL, 'Admin', 'Admin Logout', 'admin logged out successfully.', '2026-09-16 10:47:25'),
(37, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 10:50:29'),
(38, 1, NULL, 'Admin', 'Admin Logout', 'admin logged out successfully.', '2026-09-16 10:50:38'),
(39, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 10:52:46'),
(40, 1, NULL, 'Admin', 'Member Update', 'Maaaamaaaa Baterbonia updated their member information.', '2026-09-16 11:01:03'),
(41, 1, NULL, 'Admin', 'Member Update', 'Maaaamaaaa Baterbonia updated their member information.', '2026-09-16 11:02:33'),
(42, 1, NULL, 'Admin', 'Member Update', 'Maaaamaaaa Baterbonia updated their member information.', '2026-09-16 11:04:40'),
(43, 1, NULL, 'Admin', 'Member Update', 'Maaaamaaaa Bituin ng Mindanao updated their member information.', '2026-09-16 11:08:09'),
(44, 1, NULL, 'Admin', 'Staff Update', 'Jong Gii staff account information was updated.', '2026-09-16 11:22:10'),
(45, 4, NULL, 'Staff', 'Staff Login', 'Jong Gii successfully logged in.', '2026-09-16 11:22:27'),
(46, 4, NULL, 'Staff', 'Member Update', 'Lamineee Yamal member information was updated by staff.', '2026-09-16 11:22:38'),
(47, 1, NULL, 'Admin', 'Membership Delete', 'Membership #2 was deleted by admin.', '2026-09-16 11:40:18'),
(48, 1, NULL, 'Admin', 'Membership Plan Added', 'MAAAMAAAA membership plan was added with a price of â‚±200.00 and duration of 3 days.', '2026-09-16 11:57:34'),
(49, 1, NULL, 'Admin', 'Membership Creation', 'A new MAAAMAAAA membership was created for Member #29 after an approved payment.', '2026-09-16 14:07:33'),
(50, 1, NULL, 'Admin', 'Payment Submission', 'A payment of â‚±200.00 was added for Member #29 with status Approved.', '2026-09-16 14:07:33'),
(51, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 14:10:49'),
(52, 1, NULL, 'Admin', 'Payment Update', 'Payment #11 for Member #29 was updated to Declined.', '2026-09-16 14:19:01'),
(53, 1, NULL, 'Admin', 'Membership Cancellation', 'Active membership for Member #29 and Plan #9 was cancelled because the payment was declined.', '2026-09-16 14:19:01'),
(54, 1, NULL, 'Admin', 'Membership Creation', 'A new MAAAMAAAA membership was created for Member #32 after an approved payment.', '2026-09-16 14:19:59'),
(55, 1, NULL, 'Admin', 'Payment Submission', 'A payment of â‚±200.00 was added for Member #32 with status Approved.', '2026-09-16 14:19:59'),
(56, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 14:23:52'),
(57, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-16 23:12:00'),
(58, 1, NULL, 'Admin', 'Member Registration', 'Jong Gil was added as a new member.', '2026-09-16 23:40:16'),
(59, 1, NULL, 'Admin', 'Member Deletion', 'Lamineee Yamal was deleted from the system.', '2026-09-17 00:44:20'),
(60, 1, NULL, 'Admin', 'Member Registration', 'Lamine Yamal was added as a new member.', '2026-09-17 00:46:04'),
(61, 1, NULL, 'Admin', 'Member Deletion', 'Lamine Yamal was deleted from the system.', '2026-09-17 00:50:57'),
(62, 1, NULL, 'Admin', 'Member Registration', 'Lamine Yamal was added as a new member.', '2026-09-17 00:52:13'),
(63, 39, NULL, 'Member', 'Login Verification Started', 'Lamine requested a login verification code.', '2026-09-17 00:52:28'),
(64, 39, NULL, 'Member', 'Member Login', 'Lamine successfully logged in.', '2026-09-17 00:56:27'),
(65, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-17 06:02:11'),
(66, 4, NULL, 'Staff', 'Staff Login', 'Jong Gii successfully logged in.', '2026-09-17 06:14:32'),
(67, 1, NULL, 'Admin', 'Member Check-in', 'Orphelia Wow Singes was checked in by admin.', '2026-09-17 07:53:22'),
(68, 1, NULL, 'Admin', 'Payment Submission', 'A payment of â‚±499.00 was added for John Doe with status Approved.', '2026-09-17 10:32:22'),
(69, 1, NULL, 'Admin', 'Member Check-out', 'Orphelia Wow Singes was checked out by admin.', '2026-09-17 15:16:16'),
(70, 1, NULL, 'Admin', 'Walk-in Check-in', 'John Doe was checked in by admin.', '2026-09-17 15:17:13'),
(71, 1, NULL, 'Admin', 'Walk-in Check-out', 'John Doe was checked out by admin.', '2026-09-17 15:17:27'),
(72, 1, NULL, 'Admin', 'Admin Logout', 'admin logged out successfully.', '2026-09-17 16:23:45'),
(73, 4, NULL, 'Staff', 'Staff Login', 'Jong Gii successfully logged in.', '2026-09-17 16:24:09'),
(74, 4, NULL, 'Staff', 'Staff Logout', 'Jong Gii logged out successfully.', '2026-09-17 16:24:22'),
(75, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-17 23:50:48'),
(76, 1, NULL, 'Admin', 'Member Deletion', 'Diwata Overload was deleted from the system.', '2026-09-17 23:59:15'),
(77, 1, NULL, 'Admin', 'Member Deletion', 'The Weeknd was deleted from the system.', '2026-09-17 23:59:24'),
(78, 1, NULL, 'Admin', 'Member Deletion', 'Maaaamaaaa Bituin ng Mindanao was deleted from the system.', '2026-09-18 00:00:35'),
(79, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-18 05:35:54'),
(80, 1, NULL, 'Admin', 'Admin Login', 'admin successfully logged in.', '2026-09-18 08:09:18'),
(81, 4, NULL, 'Staff', 'Staff Login', 'Jong Gii successfully logged in.', '2026-09-18 08:42:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`activity_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
