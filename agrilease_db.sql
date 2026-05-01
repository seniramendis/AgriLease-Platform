-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 01, 2026 at 02:49 PM
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
-- Database: `agrilease_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `target_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `lessee_id` int(11) NOT NULL,
  `machine_id` int(11) NOT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `field_id` int(11) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` enum('Pending','In Progress','Completed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `lessee_id`, `machine_id`, `driver_id`, `total_amount`, `field_id`, `start_date`, `end_date`, `status`, `created_at`) VALUES
(2, 5, 41, 10, 23000.00, 1, NULL, NULL, 'Completed', '2026-04-28 21:39:39'),
(3, 5, 42, 10, 24200.00, 1, NULL, NULL, 'Completed', '2026-04-28 21:40:19'),
(4, 5, 80, 10, 27000.00, 1, NULL, NULL, 'Cancelled', '2026-04-28 21:49:09'),
(5, 5, 42, 10, 24200.00, 1, NULL, NULL, 'Pending', '2026-04-30 04:49:29'),
(6, 12, 41, 10, 23000.00, 1, NULL, NULL, 'Pending', '2026-04-30 05:48:23'),
(7, 5, 41, 10, 23000.00, 1, NULL, NULL, 'Pending', '2026-04-30 06:22:33'),
(8, 5, 88, 10, 4200.00, 1, NULL, NULL, 'Pending', '2026-05-01 05:25:46'),
(9, 5, 89, 10, 3000.00, 1, NULL, NULL, 'In Progress', '2026-05-01 05:40:10');

-- --------------------------------------------------------

--
-- Table structure for table `disputes`
--

CREATE TABLE `disputes` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `reported_by` int(11) NOT NULL,
  `category` enum('Machine Breakdown','Client Dispute','Payment Issue','Other') NOT NULL,
  `description` text NOT NULL,
  `severity` enum('Low','Medium','Critical') DEFAULT 'Low',
  `status` enum('Unresolved','Investigating','Resolved') DEFAULT 'Unresolved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `driver_ratings`
--

CREATE TABLE `driver_ratings` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `rated_by` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL COMMENT '1-5 stars',
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fields`
--

CREATE TABLE `fields` (
  `id` int(11) NOT NULL,
  `lessee_id` int(11) NOT NULL,
  `field_name` varchar(100) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `area_acres` decimal(10,2) DEFAULT 0.00,
  `location_lat` decimal(10,8) DEFAULT NULL,
  `location_lng` decimal(10,8) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fields`
--

INSERT INTO `fields` (`id`, `lessee_id`, `field_name`, `latitude`, `longitude`, `address`, `area_acres`, `location_lat`, `location_lng`, `created_at`) VALUES
(1, 2, 'Main Paddy Plot A', NULL, NULL, NULL, 2.50, 7.87310000, 80.77180000, '2026-04-28 20:58:37'),
(2, 5, 'Current GPS Captured Area', 6.95250000, 79.92240000, 'Captured via Live GPS', 0.00, NULL, NULL, '2026-04-29 05:41:43'),
(3, 5, 'Current GPS Captured Area', 6.95250000, 79.92240000, 'Captured via Live GPS', 0.00, NULL, NULL, '2026-04-29 05:42:03'),
(4, 5, 'My Live Area', 6.88450389, 79.86064140, 'Live GPS Captured', 0.00, NULL, NULL, '2026-04-30 04:48:04'),
(5, 5, 'hehe', 6.88450389, 79.86064140, 'Live GPS Captured', 0.00, NULL, NULL, '2026-04-30 04:48:23'),
(6, 12, 'My Live Area', 6.88450246, 79.86064175, 'Live GPS Captured', 0.00, NULL, NULL, '2026-04-30 05:51:02');

-- --------------------------------------------------------

--
-- Table structure for table `job_cards`
--

CREATE TABLE `job_cards` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `initial_reading` decimal(10,2) NOT NULL,
  `final_reading` decimal(10,2) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `machines`
--

CREATE TABLE `machines` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `model_name` varchar(100) NOT NULL,
  `serial_number` varchar(50) NOT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `image_url` text DEFAULT NULL,
  `status` enum('Available','Rented','Maintenance') DEFAULT 'Available',
  `description` text DEFAULT NULL,
  `total_hours` int(11) DEFAULT 0,
  `next_service_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `machines`
--

INSERT INTO `machines` (`id`, `owner_id`, `category`, `model_name`, `serial_number`, `hourly_rate`, `image_url`, `status`, `description`, `total_hours`, `next_service_date`) VALUES
(41, 1, 'Tractor', 'TAFE 45DI Dyna Track', 'MF-101', 4500.00, 'https://images.unsplash.com/photo-1594488311306-791771146747?q=80&w=600', 'Available', NULL, 0, NULL),
(42, 1, 'Tractor', 'Massey Ferguson 240', 'MF-202', 4800.00, 'https://images.unsplash.com/photo-1592919016382-79354062271a?q=80&w=600', 'Available', NULL, 0, NULL),
(43, 1, 'Tractor', 'John Deere 5050E', 'JD-505', 5500.00, 'https://images.unsplash.com/photo-1599708153386-62e260848464?q=80&w=600', 'Available', NULL, 0, NULL),
(44, 1, 'Tractor', 'Farm Master 2-Wheel', '2W-101', 1750.00, 'https://images.unsplash.com/photo-1530268571404-e58f27367683?q=80&w=600', 'Available', NULL, 0, NULL),
(45, 1, 'Harvester', 'Kubota DC-70G', 'KB-808', 8500.00, 'https://images.unsplash.com/photo-1590250711900-510006323812?q=80&w=600', 'Available', NULL, 0, NULL),
(46, 1, 'Harvester', 'Yanmar AW70V', 'YN-909', 9200.00, 'https://images.unsplash.com/photo-1523348837708-15d4a09cfac2?q=80&w=600', 'Available', NULL, 0, NULL),
(47, 1, 'Other', 'JCB 3DX Eco', 'JCB-707', 7200.00, 'https://images.unsplash.com/photo-1589923188900-85dae523342b?q=80&w=600', 'Available', NULL, 0, NULL),
(48, 1, 'Pump', 'Arpico 2HP Pump', 'AP-404', 850.00, 'https://images.unsplash.com/photo-1585314062340-f1a5a7c9328d?q=80&w=600', 'Available', NULL, 0, NULL),
(79, 1, 'Harvester', 'Kubota DC-70 Plus', 'KB-DC70-001', 12500.00, 'https://images.unsplash.com/photo-1592982537447-7440770cbfc9?auto=format&fit=crop&q=80&w=800', 'Available', 'High-speed combined harvester perfect for muddy paddy fields.', 0, NULL),
(80, 1, 'Tractor', 'TAFE 45 DI 4WD', 'TF-45DI-002', 5500.00, 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&q=80&w=800', 'Available', 'Versatile 47HP tractor suitable for both tilling and haulage.', 0, NULL),
(81, 1, 'Tractor', 'Massey Ferguson 240', 'MF-240-003', 4800.00, 'https://images.unsplash.com/photo-1594132174009-58d0426f041b?auto=format&fit=crop&q=80&w=800', 'Available', 'The industry standard for small-scale land preparation in Sri Lanka.', 0, NULL),
(82, 1, 'Tractor', 'John Deere 5050E', 'JD-5050-004', 6200.00, 'https://images.unsplash.com/photo-1530268577195-699b8d488831?auto=format&fit=crop&q=80&w=800', 'Available', 'Heavy-duty performance with superior fuel efficiency.', 0, NULL),
(83, 1, 'Other', 'Yanmar ViO35 Mini', 'YN-VIO35-005', 8500.00, 'https://images.unsplash.com/photo-1579412691511-2f34297e5c46?auto=format&fit=crop&q=80&w=800', 'Available', 'Mini excavator ideal for irrigation canal maintenance.', 0, NULL),
(84, 1, 'Pump', 'Honda WB30XH', 'HN-WB30-006', 3500.00, 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&q=80&w=800', 'Available', 'High-volume 3-inch water pump for seasonal paddy irrigation.', 0, NULL),
(85, 1, 'Tractor', 'New Holland TD5.90', 'NH-TD5-007', 7500.00, 'https://images.unsplash.com/photo-1563200192-311689255294?auto=format&fit=crop&q=80&w=800', 'Available', 'Premium 90HP tractor for large-scale dry zone cultivation.', 0, NULL),
(86, 1, 'Tractor', 'Sonalika Worldtrac 60', 'SL-WT60-008', 5800.00, 'https://images.unsplash.com/photo-1473976339459-735002888636?auto=format&fit=crop&q=80&w=800', 'Available', 'Rugged tractor with heavy lift capacity for industrial tasks.', 0, NULL),
(87, 1, 'Tractor', 'Kubota L4508', 'KB-L4508-009', 5200.00, 'https://images.unsplash.com/photo-1589923188900-85dae523342b?auto=format&fit=crop&q=80&w=800', 'Available', 'Compact 4WD tractor specialized for inter-cultivation.', 0, NULL),
(88, 1, 'Other', 'AgroMaster Seed Drill', 'AM-SD-010', 4200.00, 'https://images.unsplash.com/photo-1622383529957-cf1ca4603ba0?auto=format&fit=crop&q=80&w=800', 'Available', 'Precision seeding attachment to reduce labor costs.', 0, NULL),
(89, 11, 'Pump', 'hehe', 'mrs-43', 3000.00, NULL, 'Available', 'it is good', 0, '2026-05-30');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_logs`
--

CREATE TABLE `maintenance_logs` (
  `id` int(11) NOT NULL,
  `machine_id` int(11) NOT NULL,
  `service_type` varchar(100) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `service_date` date NOT NULL,
  `status` enum('Scheduled','Completed','Overdue') DEFAULT 'Completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance_logs`
--

INSERT INTO `maintenance_logs` (`id`, `machine_id`, `service_type`, `cost`, `service_date`, `status`) VALUES
(1, 89, 'adfgdhjgm,', 50000.00, '2026-05-20', 'Scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `platform_fee` decimal(12,2) NOT NULL,
  `lessor_payout` decimal(12,2) NOT NULL,
  `status` enum('Held','Released','Refunded') DEFAULT 'Held',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('Admin','Lessor','Lessee','Driver') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `district` varchar(50) DEFAULT NULL,
  `nic_number` varchar(20) DEFAULT NULL,
  `status` enum('Active','Banned') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `role`, `phone`, `district`, `nic_number`, `status`, `created_at`) VALUES
(1, 'Premium Lessor', 'lessor@agrilease.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lessor', NULL, NULL, NULL, 'Active', '2026-04-28 20:46:43'),
(2, 'Rashmika Kodithuwakku', 'rash@gmail.com', '$2y$10$OyraVnI.CN7zDjoQS4NmhO1TzKMXVOd4TpV3JsBhgWBcTvRVHe8Ue', 'Driver', NULL, NULL, NULL, '', '2026-04-28 18:36:43'),
(3, 'System Admin', 'admin@agrilease.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', NULL, NULL, NULL, '', '2026-04-28 18:40:14'),
(4, 'Nimal Lessor', 'owner@agrilease.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lessor', NULL, NULL, NULL, '', '2026-04-28 18:40:14'),
(5, 'Saman Lessee', 'farmer@agrilease.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lessee', '94703151200', 'Anuradhapura', '200432301940', '', '2026-04-28 18:40:14'),
(6, 'Kamal Driver', 'driver@agrilease.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Driver', NULL, NULL, NULL, 'Banned', '2026-04-28 18:40:14'),
(10, 'Default Operator', 'operator@agrilease.lk', 'hashed_pass', 'Driver', NULL, NULL, NULL, 'Active', '2026-04-28 20:59:52'),
(11, 'Pamod Dhananjana', 'pamod@gmail.com', '$2y$10$hUQt0.3XINc/6ENUoWFPl.OLZifoVVe0S5uWuJWBHj1oD7kLP9xAa', 'Lessor', NULL, NULL, NULL, 'Active', '2026-04-30 05:11:51'),
(12, 'Senira Mendis', 'senira@gmail.com', '$2y$10$f1maIk1QpKmK/o2G3POb/uryCT0WWExQXCDfBrK72dEfLlkvhXGNS', 'Lessee', '+94725954646', 'Anuradhapura', '200432301940', 'Active', '2026-04-30 05:12:31'),
(13, 'Ananda Perera', 'anandaperera@gmail.com', '$2y$10$pj7i.2vxLUPaMfQpvJJGM.TfU3PRsh2Hs9qzxQypuw/wHDgVN17/y', 'Lessee', NULL, NULL, NULL, 'Active', '2026-04-30 05:43:17'),
(14, 'Lyra Zeno', 'zenolk0000@gmail.com', '$2y$10$QFjOyXNCXmCpgL3nNKXlveiB5aL5yQASZvnwDd6ZSoh0uur7zO2RG', 'Lessee', '+94725954646', 'Anuradhapura', NULL, 'Active', '2026-04-30 08:42:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lessee_id` (`lessee_id`),
  ADD KEY `machine_id` (`machine_id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `bookings_ibfk_4` (`field_id`);

--
-- Indexes for table `disputes`
--
ALTER TABLE `disputes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `reported_by` (`reported_by`);

--
-- Indexes for table `driver_ratings`
--
ALTER TABLE `driver_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_driver_rating` (`booking_id`,`driver_id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `rated_by` (`rated_by`);

--
-- Indexes for table `fields`
--
ALTER TABLE `fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lessee_id` (`lessee_id`);

--
-- Indexes for table `job_cards`
--
ALTER TABLE `job_cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `machines`
--
ALTER TABLE `machines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `serial_number` (`serial_number`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `machine_id` (`machine_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `disputes`
--
ALTER TABLE `disputes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `driver_ratings`
--
ALTER TABLE `driver_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fields`
--
ALTER TABLE `fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `job_cards`
--
ALTER TABLE `job_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `machines`
--
ALTER TABLE `machines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`lessee_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_4` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `disputes`
--
ALTER TABLE `disputes`
  ADD CONSTRAINT `disputes_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `disputes_ibfk_2` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `driver_ratings`
--
ALTER TABLE `driver_ratings`
  ADD CONSTRAINT `dr_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dr_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `dr_ibfk_3` FOREIGN KEY (`rated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `fields`
--
ALTER TABLE `fields`
  ADD CONSTRAINT `fields_ibfk_1` FOREIGN KEY (`lessee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_cards`
--
ALTER TABLE `job_cards`
  ADD CONSTRAINT `job_cards_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_cards_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `machines`
--
ALTER TABLE `machines`
  ADD CONSTRAINT `machines_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD CONSTRAINT `maintenance_logs_ibfk_1` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
