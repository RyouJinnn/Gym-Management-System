-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2026 at 07:57 AM
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
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `check_in` datetime DEFAULT NULL,
  `check_out` datetime DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('Checked In','Checked Out') DEFAULT 'Checked In',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `member_id`, `check_in`, `check_out`, `attendance_date`, `status`, `created_at`) VALUES
(2, 27, '2026-08-03 16:11:42', '2026-08-03 20:06:51', '2026-08-03', 'Checked Out', '2026-08-03 08:11:42'),
(3, 27, '2026-08-04 11:13:16', '2026-08-04 16:53:35', '2026-08-04', 'Checked Out', '2026-08-04 03:13:16'),
(5, 27, '2026-08-06 13:31:14', '2026-08-06 15:20:21', '2026-08-06', 'Checked Out', '2026-08-06 05:31:14'),
(6, 27, '2026-08-09 17:39:50', '2026-08-09 21:22:57', '2026-08-09', 'Checked Out', '2026-08-09 09:39:50'),
(7, 27, '2026-08-13 14:24:32', '2026-08-13 14:24:40', '2026-08-13', 'Checked Out', '2026-08-13 06:24:32'),
(8, 27, '2026-08-18 16:28:18', '2026-08-18 16:28:27', '2026-08-18', 'Checked Out', '2026-08-18 08:28:18'),
(9, 32, '2026-08-18 16:28:31', '2026-08-18 16:28:34', '2026-08-18', 'Checked Out', '2026-08-18 08:28:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `member_id` (`member_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `signup` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
