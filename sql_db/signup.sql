-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 18, 2026 at 11:14 AM
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
-- Table structure for table `signup`
--

CREATE TABLE `signup` (
  `id` int(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `contact_number` varchar(30) NOT NULL,
  `gender` enum('Male','Female','Rather not say') NOT NULL,
  `birthdate` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `verification_code` varchar(6) NOT NULL,
  `verification_expiry` datetime NOT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `address` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `qr_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `signup`
--

INSERT INTO `signup` (`id`, `first_name`, `middlename`, `last_name`, `suffix`, `email`, `contact_number`, `gender`, `birthdate`, `password`, `created_at`, `verification_code`, `verification_expiry`, `email_verified`, `status`, `address`, `profile_picture`, `qr_token`) VALUES
(27, 'Ryou', '', 'Tatsuki', '', 'ryoutatsukiii@gmail.com', '+639425454425', 'Male', '2009-01-05', '$2y$10$JxfX7HaoTBrCCfs/61wW3e2xaqR/QPdWWpM2Ebc3JGh//0oRQIABG', '2026-09-15 23:01:04', '', '0000-00-00 00:00:00', 1, 'Active', '123 Poblacion Alaminos City Pangasinan', 'profile_picture/6a68b13a9bc06.jpg', NULL),
(29, 'Ivan', 'Quarantine', 'Reate', 'JR', 'reateivan5@gmail.com', '+631234567890', 'Male', '2003-02-03', '$2y$10$aKXHOcH7/1jXBVkTdZxBqeNOU11SVPhCdNQQw0rlVDfdK0Ts//yqW', '2026-09-16 14:07:33', '', '0000-00-00 00:00:00', 1, 'Active', NULL, NULL, '13e104d2a76ccff952f9da44b72f8ea827dfdfb7b6158376223b59ab644cf22b'),
(32, 'Orphelia', 'Wow', 'Singes', '', 'miguelcarloanganangan@gmail.com', '+639484567878', 'Female', '2011-02-08', '$2y$10$0.3.dvFeuhHmuct99EnjS..7zcpNeE1eoawGKlFh7YF6deef752S6', '2026-09-16 14:19:59', '', '0000-00-00 00:00:00', 1, 'Active', '', 'profile_picture/member_32_1786382345.webp', '7479578360210b5cc8341823b3b653611d06dce39d16a4727aabd5099e18e68e'),
(36, 'Miguel', '', 'Anganangan', '', 'miguelanganangan1@gmail.com', '09928331855', 'Male', '2004-10-05', '', '2026-09-13 01:43:41', '', '0000-00-00 00:00:00', 0, 'Active', NULL, '', NULL),
(37, 'Jong', '', 'Gil', '', 'ginjacamarse1@gmail.com', '09928441844', 'Male', '2008-01-29', '', '2026-09-16 23:40:16', '', '0000-00-00 00:00:00', 0, 'Active', NULL, '', NULL),
(39, 'Lamine', '', 'Yamal', '', 'miguelanganangan10@gmail.com', '09923881844', 'Male', '2009-02-10', '$2y$10$Bm6c2C5vVE42d4Z8nuwKzeqjrgnyb2cGPUKVytEWsxKgDz9jE1Tuq', '2026-09-17 00:56:27', '', '0000-00-00 00:00:00', 1, 'Active', NULL, '', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `signup`
--
ALTER TABLE `signup`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `qr_token` (`qr_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `signup`
--
ALTER TABLE `signup`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
