-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 30, 2026 at 12:01 PM
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
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `signup`
--

INSERT INTO `signup` (`id`, `first_name`, `middlename`, `last_name`, `suffix`, `email`, `contact_number`, `gender`, `birthdate`, `password`, `created_at`, `verification_code`, `verification_expiry`, `email_verified`, `status`, `address`, `profile_picture`) VALUES
(27, 'Ryou', '', 'Tatsuki', '', 'ryoutatsukiii@gmail.com', '+639425454425', 'Male', '2009-01-05', '$2y$10$T4wa6EIub0jWsK0WhRoNJudy0ZxLrz5VobilTHzEiD0tcIUkzH/1a', '2026-08-14 09:37:11', '', '0000-00-00 00:00:00', 1, 'Active', '123 Poblacion Alaminos City Pangasinan', 'profile_picture/6a68b13a9bc06.jpg'),
(28, 'Lamine', '', 'Yamal', '', 'miguelanganangan10@gmail.com', '+639123456789', 'Male', '2005-02-08', '$2y$10$dvcM1bgIFWqQXhiORk1Fp.uJ97edTSUW.dFWr/nKNRmPBJv85qR/O', '2026-08-05 06:04:55', '', '0000-00-00 00:00:00', 1, 'Active', NULL, NULL),
(29, 'Ivan', 'Quarantine', 'Reate', 'JR', 'reateivan5@gmail.com', '+631234567890', 'Male', '2003-02-03', '$2y$10$aKXHOcH7/1jXBVkTdZxBqeNOU11SVPhCdNQQw0rlVDfdK0Ts//yqW', '2026-08-10 09:53:02', '', '0000-00-00 00:00:00', 1, 'Active', NULL, NULL),
(32, 'Orphelia', 'Wow', 'Singes', '', 'miguelcarloanganangan@gmail.com', '+639484567878', 'Female', '2011-02-08', '$2y$10$0.3.dvFeuhHmuct99EnjS..7zcpNeE1eoawGKlFh7YF6deef752S6', '2026-08-13 14:00:11', '', '0000-00-00 00:00:00', 1, 'Active', '', 'profile_picture/member_32_1786382345.webp'),
(33, 'Rene', '', 'Baterbonia', '', 'renebaterbonia@gmail.com', '09248645188', 'Male', '2010-02-02', '', '2026-08-10 10:37:53', '', '0000-00-00 00:00:00', 0, 'Inactive', NULL, NULL),
(34, 'Diwata', 'Pares', 'Overload', 'Sr.', 'diwatapares13@gmail.com', '09123555154', 'Female', '2010-01-10', '', '2026-08-10 11:12:25', '', '0000-00-00 00:00:00', 0, 'Active', 'Pasay 123St.', NULL),
(35, 'The', '', 'Weeknd', 'III', 'theweeknd11@gmail.com', '09208952419', 'Male', '2009-03-05', '', '2026-08-10 14:20:02', '', '0000-00-00 00:00:00', 0, 'Active', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `signup`
--
ALTER TABLE `signup`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `signup`
--
ALTER TABLE `signup`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
