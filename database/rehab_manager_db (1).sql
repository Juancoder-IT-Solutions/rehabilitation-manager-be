-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2025 at 03:34 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rehab_manager_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admission`
--

CREATE TABLE `tbl_admission` (
  `admission_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rehab_center_id` int(11) DEFAULT NULL,
  `admission_date` datetime DEFAULT NULL,
  `discharge_date` datetime DEFAULT NULL,
  `admitting_physician` varchar(100) DEFAULT NULL,
  `ward` varchar(100) DEFAULT NULL,
  `type_of_admission` varchar(10) DEFAULT NULL COMMENT 'NEW,\r\nOLD.\r\nFORMER OPD',
  `referred_by` varchar(100) DEFAULT NULL,
  `social_service_classification` varchar(1) DEFAULT NULL COMMENT 'A, B, C, D',
  `allergic_to` varchar(100) DEFAULT NULL,
  `hospitalization_plan` varchar(100) DEFAULT NULL,
  `health_insurance_name` varchar(100) DEFAULT NULL,
  `medicare` varchar(5) DEFAULT NULL COMMENT 'SSS, GSIS',
  `data_furnish_by` varchar(100) DEFAULT NULL,
  `address_of_informant` varchar(100) DEFAULT NULL,
  `relation_to_patient` varchar(25) DEFAULT NULL,
  `admission_diagnosis` text DEFAULT NULL,
  `other_diagnosis` text DEFAULT NULL,
  `principal_operation` varchar(100) DEFAULT NULL,
  `other_operation` varchar(100) DEFAULT NULL,
  `accident_injury_poisoning` varchar(100) DEFAULT NULL,
  `place_of_occurence` varchar(100) DEFAULT NULL,
  `disposition` varchar(25) DEFAULT NULL COMMENT 'DISCHARGE, TRANSFERRED, DAMA, ABSCONDED, RECOVERED, DIED, -48 HOURS, +48 HOURS',
  `results` varchar(25) DEFAULT NULL COMMENT 'IMPROVED, UNIMPROVED, AUTOPSY, NO AUTOPSY',
  `attending_physician` varchar(100) DEFAULT NULL,
  `date_added` datetime DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rehab_centers`
--

CREATE TABLE `tbl_rehab_centers` (
  `rehab_center_id` int(11) NOT NULL,
  `rehab_center_name` varchar(150) NOT NULL,
  `hospital_code` varchar(75) NOT NULL,
  `med_record_no` varchar(75) NOT NULL,
  `rehab_center_city` varchar(150) NOT NULL,
  `rehab_center_complete_address` varchar(150) NOT NULL,
  `rehab_center_coordinates` text NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_rehab_centers`
--

INSERT INTO `tbl_rehab_centers` (`rehab_center_id`, `rehab_center_name`, `hospital_code`, `med_record_no`, `rehab_center_city`, `rehab_center_complete_address`, `rehab_center_coordinates`, `date_added`, `date_updated`) VALUES
(1, 'The New Beginnings Foundation, Inc. - Bacolod', '002', '', '', '28 Atis corner Kamachile Streets, La Salle Ave, Bacolod, 6100', '', '2025-03-13 14:51:59', '2025-03-13 14:52:07');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rehab_center_gallery`
--

CREATE TABLE `tbl_rehab_center_gallery` (
  `id` int(11) NOT NULL,
  `rehab_center_id` int(11) NOT NULL DEFAULT 0,
  `file` text NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_rehab_center_gallery`
--

INSERT INTO `tbl_rehab_center_gallery` (`id`, `rehab_center_id`, `file`, `date_added`) VALUES
(28, 1, '1-67d292ae164659.71144237.png', '2025-03-13 15:58:40'),
(29, 1, '1-67d292db750b01.44214893.png', '2025-03-13 16:09:37');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_services`
--

CREATE TABLE `tbl_services` (
  `service_id` int(11) NOT NULL,
  `rehab_center_id` int(11) NOT NULL DEFAULT 0,
  `service_name` varchar(50) NOT NULL DEFAULT '0',
  `service_fee` decimal(11,2) NOT NULL DEFAULT 0.00,
  `service_desc` text NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_services`
--

INSERT INTO `tbl_services` (`service_id`, `rehab_center_id`, `service_name`, `service_fee`, `service_desc`, `date_added`, `date_updated`) VALUES
(11, 0, 'Service 1', '120.00', 'sample only', '2025-03-09 17:54:11', '2025-03-09 17:54:11'),
(18, 0, 'sample 1', '3424.00', 'sample', '2025-03-11 16:45:47', '2025-03-12 10:00:15'),
(20, 0, '2', '2.00', 'u', '2025-03-13 10:34:31', '2025-03-13 10:34:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_services_availed`
--

CREATE TABLE `tbl_services_availed` (
  `service_availed_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `rehab_center_id` int(11) NOT NULL DEFAULT 0,
  `admission_id` int(11) NOT NULL DEFAULT 0,
  `service_id` int(11) NOT NULL DEFAULT 0,
  `service_date` datetime NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `user_id` int(11) NOT NULL,
  `user_fname` varchar(30) NOT NULL,
  `user_mname` varchar(30) NOT NULL,
  `user_lname` varchar(30) NOT NULL,
  `permanent_address` text NOT NULL,
  `birthdate` date NOT NULL,
  `birth_place` varchar(100) NOT NULL DEFAULT '',
  `nationality` varchar(25) NOT NULL DEFAULT '',
  `religion` varchar(25) NOT NULL DEFAULT '',
  `occupation` varchar(100) NOT NULL DEFAULT '',
  `employer` varchar(100) NOT NULL DEFAULT '',
  `employer_address` varchar(100) NOT NULL DEFAULT '',
  `father_name` varchar(100) NOT NULL DEFAULT '',
  `father_address` varchar(100) NOT NULL DEFAULT '',
  `mother_name` varchar(100) NOT NULL DEFAULT '',
  `mother_address` varchar(100) NOT NULL DEFAULT '',
  `user_category` varchar(1) NOT NULL COMMENT 'U = user; R = Rehab center;',
  `username` varchar(30) NOT NULL,
  `password` text NOT NULL,
  `rehab_center_id` int(11) NOT NULL DEFAULT 0 COMMENT '0 if user',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_admission`
--
ALTER TABLE `tbl_admission`
  ADD PRIMARY KEY (`admission_id`);

--
-- Indexes for table `tbl_rehab_centers`
--
ALTER TABLE `tbl_rehab_centers`
  ADD PRIMARY KEY (`rehab_center_id`);

--
-- Indexes for table `tbl_rehab_center_gallery`
--
ALTER TABLE `tbl_rehab_center_gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_services`
--
ALTER TABLE `tbl_services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `tbl_services_availed`
--
ALTER TABLE `tbl_services_availed`
  ADD PRIMARY KEY (`service_availed_id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_admission`
--
ALTER TABLE `tbl_admission`
  MODIFY `admission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_rehab_centers`
--
ALTER TABLE `tbl_rehab_centers`
  MODIFY `rehab_center_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_rehab_center_gallery`
--
ALTER TABLE `tbl_rehab_center_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `tbl_services`
--
ALTER TABLE `tbl_services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbl_services_availed`
--
ALTER TABLE `tbl_services_availed`
  MODIFY `service_availed_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
