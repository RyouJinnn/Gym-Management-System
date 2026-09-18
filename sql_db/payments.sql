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
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `payer_name` varchar(150) DEFAULT NULL,
  `plan_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('Credit/Debit Card','GCash','Pay at the Counter') NOT NULL,
  `payment_status` enum('Pending','Approved','Declined') DEFAULT 'Pending',
  `transaction_reference` varchar(100) DEFAULT NULL,
  `proof_of_payment` varchar(255) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `member_id`, `payer_name`, `plan_id`, `amount`, `payment_method`, `payment_status`, `transaction_reference`, `proof_of_payment`, `payment_date`) VALUES
(2, 27, NULL, 8, '5508.00', 'GCash', 'Declined', 'FFG-2026080517473966512', 'payment_proofs/proof_6a7306bbd8f46.webp', '2026-08-05 09:47:00'),
(6, 32, NULL, 2, '299.00', 'GCash', 'Approved', 'RESTORE-ORPHELIA-32', NULL, '2026-08-13 14:15:14'),
(7, 27, NULL, 1, '499.00', 'GCash', 'Declined', 'FFG-202609081638373995', NULL, '2026-09-08 08:38:00'),
(8, 27, NULL, 4, '199.00', 'GCash', 'Approved', 'FFG-202609111807213228', NULL, '2026-09-11 10:07:00'),
(9, 36, NULL, 2, '299.00', 'Pay at the Counter', 'Approved', '', NULL, '2026-09-13 04:10:00'),
(10, 27, NULL, 2, '299.00', 'GCash', 'Declined', 'FFG-202609160703204486', NULL, '2026-09-15 23:03:00'),
(11, 29, NULL, 9, '200.00', 'Pay at the Counter', 'Declined', '', NULL, '2026-09-16 14:06:00'),
(12, 32, NULL, 9, '200.00', 'GCash', 'Approved', '', NULL, '2026-09-16 14:19:00'),
(13, NULL, 'John Doe', 1, '499.00', 'Pay at the Counter', 'Approved', '', NULL, '2026-09-17 10:32:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `member_id` (`member_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `signup` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
