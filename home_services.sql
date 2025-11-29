-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2025 at 11:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `home_services`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appointment_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `appointment_time` datetime NOT NULL,
  `status` enum('requested','confirmed','completed','cancelled') DEFAULT 'requested',
  `location` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `provider_bookings`
--

CREATE TABLE `provider_bookings` (
  `booking_id` int(11) NOT NULL,
  `booking_ref` varchar(20) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `client_user_id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `client_email` varchar(255) NOT NULL,
  `client_mobile` varchar(20) NOT NULL,
  `service_type` varchar(100) NOT NULL,
  `service_description` text DEFAULT NULL,
  `preferred_date` date NOT NULL,
  `preferred_time` time DEFAULT NULL,
  `estimated_duration` varchar(50) DEFAULT NULL,
  `service_address` varchar(255) NOT NULL,
  `service_latitude` decimal(10,8) DEFAULT NULL,
  `service_longitude` decimal(11,8) DEFAULT NULL,
  `client_vaccinated` tinyint(1) DEFAULT 0,
  `client_test_provided` tinyint(1) DEFAULT 0,
  `mask_agreement` tinyint(1) DEFAULT 0,
  `status` enum('pending','confirmed','in_progress','completed','cancelled') DEFAULT 'pending',
  `provider_notes` text DEFAULT NULL,
  `client_notes` text DEFAULT NULL,
  `quoted_price` decimal(10,2) DEFAULT NULL,
  `final_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `service_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `provider_id` int(11) NOT NULL,
  `category` enum('cleaning','repairs','maintenance','other') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `servicepackage`
--

CREATE TABLE `servicepackage` (
  `package_id` int(11) NOT NULL,
  `package_name` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_bookings`
--

CREATE TABLE `service_bookings` (
  `booking_id` int(11) NOT NULL,
  `booking_ref` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `service_type` varchar(50) NOT NULL,
  `package_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `preferred_date` date NOT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `preferred_cost` decimal(10,2) DEFAULT 0.00,
  `address` text NOT NULL,
  `additional_details` text DEFAULT NULL,
  `inspection_findings` text DEFAULT NULL,
  `covid_vaccinated` tinyint(1) DEFAULT 0,
  `covid_test_required` tinyint(1) DEFAULT 0,
  `mask_required` tinyint(1) DEFAULT 0,
  `status` enum('pending','confirmed','in_progress','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_bookings`
--

INSERT INTO `service_bookings` (`booking_id`, `booking_ref`, `user_id`, `service_id`, `service_type`, `package_id`, `name`, `email`, `mobile`, `preferred_date`, `duration`, `preferred_cost`, `address`, `additional_details`, `inspection_findings`, `covid_vaccinated`, `covid_test_required`, `mask_required`, `status`, `created_at`, `updated_at`) VALUES
(1, 'SB-DB853820-825', 3, NULL, 'Plumbing Check', NULL, 'Promise', 'promrizal@gmail.com', '0432720963', '2025-12-31', '2-4 hours', 200.00, 'My address 123', 'Urgent action.', NULL, 1, 1, 0, 'pending', '2025-11-29 08:24:24', '2025-11-29 08:41:50'),
(2, 'SB-380D9145-415', 3, NULL, 'full_inspection', NULL, 'Denis', 'denis@gmail.com', '0432720963', '2025-11-06', 'half_day', 50.00, '49 street, wilson road', 'bring the equipments', NULL, 1, 1, 1, 'pending', '2025-11-29 08:49:04', '2025-11-29 08:49:04'),
(3, 'SB-3F465651-663', 3, NULL, 'full_inspection', NULL, 'Denis', 'denis@gmail.com', '0432720963', '2025-11-06', 'half_day', 50.00, '49 street, wilson road', 'bring the equipments', NULL, 1, 1, 1, 'pending', '2025-11-29 08:51:00', '2025-11-29 08:51:00'),
(4, 'SB-5074B45E-124', 3, NULL, 'full_inspection', NULL, 'Denis', 'denis@gmail.com', '0432720963', '2025-11-06', 'half_day', 50.00, '49 street, wilson road', 'bring the equipments', NULL, 1, 1, 1, 'pending', '2025-11-29 08:55:35', '2025-11-29 08:55:35');

-- --------------------------------------------------------

--
-- Table structure for table `service_providers`
--

CREATE TABLE `service_providers` (
  `provider_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `service_category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `covid_vaccinated` tinyint(1) DEFAULT 0,
  `covid_safe_certified` tinyint(1) DEFAULT 0,
  `covid_test_required` tinyint(1) DEFAULT 0,
  `mask_required` tinyint(1) DEFAULT 0,
  `max_bookings_per_day` int(11) DEFAULT 5,
  `available_days` varchar(50) DEFAULT 'Mon,Tue,Wed,Thu,Fri',
  `working_hours_start` time DEFAULT '08:00:00',
  `working_hours_end` time DEFAULT '18:00:00',
  `is_active` tinyint(1) DEFAULT 1,
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_reviews` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('client','provider','admin') NOT NULL,
  `health_status` varchar(50) DEFAULT NULL,
  `otp_code` varchar(10) DEFAULT NULL,
  `otp_expires_at` datetime DEFAULT NULL,
  `oauth_provider` varchar(50) DEFAULT NULL,
  `oauth_id` varchar(200) DEFAULT NULL,
  `profile_picture` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `email`, `password_hash`, `role`, `health_status`, `otp_code`, `otp_expires_at`, `oauth_provider`, `oauth_id`, `profile_picture`, `created_at`) VALUES
(3, 'Promise', 'promrizal@gmail.com', '$2y$10$4R.l8RF5E6LEgtDVQqP4nuwfKXgXWDBQDJ2NRAQaFYNfx2IdKr4ge', 'admin', NULL, '734033', '2025-11-29 11:21:10', NULL, NULL, NULL, '2025-11-29 01:17:59'),
(4, 'Denis', 'denis@gmail.com', '$2y$10$3Hyqol2TcJKY2UjLVNdc4eB5e6f2Idy2cO.Zp/N3I2Id7vbgSwA.K', 'client', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-29 01:20:10'),
(5, 'aniket', 'aniket.ga8@gmail.com', '$2y$10$GPQp6joBlyi8i4r83.xTfuUZ8Wn9MwsbzyIVM2.ga2GvbOV4B60yO', 'client', NULL, '815523', '2025-11-29 11:36:45', NULL, NULL, NULL, '2025-11-29 10:17:31');

-- --------------------------------------------------------

--
-- Table structure for table `visitlog`
--

CREATE TABLE `visitlog` (
  `visit_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `check_in_time` datetime DEFAULT NULL,
  `checkout_time` datetime DEFAULT NULL,
  `covid_status` enum('healthy','infected','quarantined','unknown') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `provider_bookings`
--
ALTER TABLE `provider_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD UNIQUE KEY `booking_ref` (`booking_ref`),
  ADD KEY `idx_booking_provider` (`provider_id`),
  ADD KEY `idx_booking_client` (`client_user_id`),
  ADD KEY `idx_booking_date` (`preferred_date`),
  ADD KEY `idx_booking_status` (`status`),
  ADD KEY `idx_booking_ref` (`booking_ref`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `servicepackage`
--
ALTER TABLE `servicepackage`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `service_bookings`
--
ALTER TABLE `service_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD UNIQUE KEY `booking_ref` (`booking_ref`),
  ADD KEY `idx_sb_user` (`user_id`),
  ADD KEY `idx_sb_service` (`service_id`),
  ADD KEY `idx_sb_package` (`package_id`),
  ADD KEY `idx_sb_status` (`status`),
  ADD KEY `idx_sb_date` (`preferred_date`);

--
-- Indexes for table `service_providers`
--
ALTER TABLE `service_providers`
  ADD PRIMARY KEY (`provider_id`),
  ADD KEY `idx_provider_location` (`latitude`,`longitude`),
  ADD KEY `idx_provider_category` (`service_category`),
  ADD KEY `idx_provider_active` (`is_active`),
  ADD KEY `idx_provider_user` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `visitlog`
--
ALTER TABLE `visitlog`
  ADD PRIMARY KEY (`visit_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `provider_bookings`
--
ALTER TABLE `provider_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `servicepackage`
--
ALTER TABLE `servicepackage`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_bookings`
--
ALTER TABLE `service_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `service_providers`
--
ALTER TABLE `service_providers`
  MODIFY `provider_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `visitlog`
--
ALTER TABLE `visitlog`
  MODIFY `visit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `service` (`service_id`);

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `provider_bookings`
--
ALTER TABLE `provider_bookings`
  ADD CONSTRAINT `provider_bookings_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `service_providers` (`provider_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `provider_bookings_ibfk_2` FOREIGN KEY (`client_user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `service_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `service_bookings`
--
ALTER TABLE `service_bookings`
  ADD CONSTRAINT `service_bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_bookings_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `service` (`service_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `service_bookings_ibfk_3` FOREIGN KEY (`package_id`) REFERENCES `servicepackage` (`package_id`) ON DELETE SET NULL;

--
-- Constraints for table `service_providers`
--
ALTER TABLE `service_providers`
  ADD CONSTRAINT `service_providers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `visitlog`
--
ALTER TABLE `visitlog`
  ADD CONSTRAINT `visitlog_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `visitlog_ibfk_2` FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`appointment_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
