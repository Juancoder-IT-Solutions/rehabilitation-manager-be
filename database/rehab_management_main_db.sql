-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table rehab_management_main_db.tbl_admission
CREATE TABLE IF NOT EXISTS `tbl_admission` (
  `admission_id` int(11) NOT NULL AUTO_INCREMENT,
  `rehab_center_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `admission_reference_id` int(11) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`admission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table rehab_management_main_db.tbl_admission: ~2 rows (approximately)
INSERT INTO `tbl_admission` (`admission_id`, `rehab_center_id`, `user_id`, `admission_reference_id`, `date_added`, `status`) VALUES
	(3, 19, 1, 4, '2025-08-27 13:29:04', 'A'),
	(4, 19, 2, 3, '2025-08-27 16:43:34', 'A'),
	(5, 19, 1, 5, '2025-08-27 16:45:07', 'P');

-- Dumping structure for table rehab_management_main_db.tbl_admission_details
CREATE TABLE IF NOT EXISTS `tbl_admission_details` (
  `admission_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `input_id` int(11) NOT NULL DEFAULT 0,
  `input_value` text NOT NULL DEFAULT '',
  PRIMARY KEY (`admission_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table rehab_management_main_db.tbl_admission_details: ~0 rows (approximately)

-- Dumping structure for table rehab_management_main_db.tbl_admission_services
CREATE TABLE IF NOT EXISTS `tbl_admission_services` (
  `admission_service_id` int(11) NOT NULL AUTO_INCREMENT,
  `admission_id` int(11) NOT NULL DEFAULT 0,
  `service_id` int(11) NOT NULL DEFAULT 0,
  `date_started` date DEFAULT NULL,
  `date_ended` date DEFAULT NULL,
  PRIMARY KEY (`admission_service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table rehab_management_main_db.tbl_admission_services: ~0 rows (approximately)

-- Dumping structure for table rehab_management_main_db.tbl_admission_tasks
CREATE TABLE IF NOT EXISTS `tbl_admission_tasks` (
  `admission_task_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL DEFAULT 0,
  `remarks` varchar(50) NOT NULL DEFAULT '',
  `date_added` datetime DEFAULT NULL,
  PRIMARY KEY (`admission_task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table rehab_management_main_db.tbl_admission_tasks: ~0 rows (approximately)

-- Dumping structure for table rehab_management_main_db.tbl_appointments
CREATE TABLE IF NOT EXISTS `tbl_appointments` (
  `appointment_id` int(11) NOT NULL AUTO_INCREMENT,
  `admission_id` int(11) NOT NULL,
  `rehab_center_id` int(11) NOT NULL,
  `remarks` text NOT NULL,
  `appointment_date` date NOT NULL,
  `status` varchar(1) NOT NULL DEFAULT '',
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`appointment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table rehab_management_main_db.tbl_appointments: ~0 rows (approximately)
INSERT INTO `tbl_appointments` (`appointment_id`, `admission_id`, `rehab_center_id`, `remarks`, `appointment_date`, `status`, `date_added`) VALUES
	(1, 4, 19, '', '0000-00-00', '', '2025-08-28 14:44:49');

-- Dumping structure for table rehab_management_main_db.tbl_inputs
CREATE TABLE IF NOT EXISTS `tbl_inputs` (
  `input_id` int(11) NOT NULL AUTO_INCREMENT,
  `input_label` varchar(50) NOT NULL DEFAULT '',
  `input_type` varchar(50) NOT NULL DEFAULT '' COMMENT 'text, select, textarea',
  `input_require` int(1) NOT NULL DEFAULT 1 COMMENT '1 - yes ; 0 no',
  `rehab_center` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`input_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table rehab_management_main_db.tbl_inputs: ~4 rows (approximately)
INSERT INTO `tbl_inputs` (`input_id`, `input_label`, `input_type`, `input_require`, `rehab_center`) VALUES
	(1, 'First Name', 'text', 1, 0),
	(3, 'Middle Name', 'text', 0, 0),
	(9, 'Last Name', 'text', 1, 0),
	(10, 'Gender', 'select', 1, 0);

-- Dumping structure for table rehab_management_main_db.tbl_input_options
CREATE TABLE IF NOT EXISTS `tbl_input_options` (
  `input_option_id` int(11) NOT NULL AUTO_INCREMENT,
  `input_id` int(11) NOT NULL DEFAULT 0,
  `input_option_label` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`input_option_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table rehab_management_main_db.tbl_input_options: ~6 rows (approximately)
INSERT INTO `tbl_input_options` (`input_option_id`, `input_id`, `input_option_label`) VALUES
	(1, 8, 'asd sad'),
	(2, 8, 'asdssda'),
	(3, 10, 'Female'),
	(4, 10, 'Male sda'),
	(5, 10, 'asd'),
	(6, 10, 'as');

-- Dumping structure for table rehab_management_main_db.tbl_rehab_centers
CREATE TABLE IF NOT EXISTS `tbl_rehab_centers` (
  `rehab_center_id` int(11) NOT NULL AUTO_INCREMENT,
  `rehab_center_name` varchar(150) NOT NULL,
  `rehab_center_desc` text NOT NULL,
  `hospital_code` varchar(75) NOT NULL,
  `med_record_no` varchar(75) NOT NULL,
  `rehab_center_city` varchar(150) NOT NULL,
  `rehab_center_complete_address` varchar(150) NOT NULL,
  `rehab_center_coordinates` text NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`rehab_center_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table rehab_management_main_db.tbl_rehab_centers: ~19 rows (approximately)
INSERT INTO `tbl_rehab_centers` (`rehab_center_id`, `rehab_center_name`, `rehab_center_desc`, `hospital_code`, `med_record_no`, `rehab_center_city`, `rehab_center_complete_address`, `rehab_center_coordinates`, `date_added`, `date_updated`) VALUES
	(1, 'The New Beginnings Foundation, Inc. - Bacolod', '[Center Name] is a compassionate and professional rehabilitation facility dedicated to helping individuals overcome substance abuse, behavioral health issues, and physical impairments. Our center provides a safe, supportive, and structured environment where patients can focus on their recovery journey with dignity and care.\n\nWe offer a comprehensive range of services including medical detox, inpatient and outpatient rehabilitation, individual and group counseling, physical therapy, occupational therapy, and holistic wellness programs. Our multidisciplinary team of doctors, therapists, nurses, and support staff work together to develop personalized treatment plans tailored to each patient’s unique needs and recovery goals.\n\nAt [Center Name], we believe that recovery is a lifelong process. We are committed to providing continuous support, education, and aftercare services to ensure long-term success and reintegration into the community. Our goal is to help individuals reclaim their lives, restore their independence, and build a brighter future.', '002', '', 'Bacolod City', '28 Atis corner Kamachile Streets, La Salle Ave, Bacolod, 6100', '', '2025-03-13 14:51:59', '2025-07-14 16:02:19'),
	(2, 'Balay Silangan', 'At [Center Name], we believe that recovery is a lifelong process. We are committed to providing continuous support, education, and aftercare services to ensure long-term success and reintegration into the community. Our goal is to help individuals reclaim their lives, restore their independence, and build a brighter future.', '003', '', 'Bacolod City', '28 Atis corner Kamachile Streets, La Salle Ave, Bacolod, 6100', '', '2025-03-13 14:51:59', '2025-07-14 16:05:31'),
	(3, 'Negros Occidental Drug Rehabilitation Center', 'Contact us at nodrc@yahoo.com', '004', '', 'Bacolod City', 'Camp Gen. Aniceto Lacson St., Victorias City Negros Occidental', '', '2025-03-13 14:51:59', '2025-07-14 16:05:31'),
	(4, 'IJK', '', 'k', 'k', 'k', 'k', '10.6147522,122.9324862', '2025-08-10 16:26:09', '2025-08-26 15:20:49'),
	(5, 'GHI', '', 'k', 'k', 'k', 'k', '10.6147522,122.9324862', '2025-08-10 16:27:45', '2025-08-26 15:20:36'),
	(6, 'HIJ', '', 'k', 'k', 'k', 'k', '10.6147522,122.9324862', '2025-08-10 16:30:52', '2025-08-26 15:20:41'),
	(7, 'BCD', '', 'a', 'a', 'a', 'a', '10.6147522,122.9324862', '2025-08-10 16:40:28', '2025-08-26 15:20:15'),
	(8, 'CDE', '', 'a', 'a', 'a', 'a', '10.6147522,122.9324862', '2025-08-10 16:42:55', '2025-08-26 15:20:18'),
	(9, 'DEF', '', 'a', 'a', 'a', 'a', '10.6147522,122.9324862', '2025-08-10 16:43:56', '2025-08-26 15:20:20'),
	(10, 'EFG', '', 'a', 'a', 'a', 'a', '10.6147522,122.9324862', '2025-08-10 16:44:41', '2025-08-26 15:20:23'),
	(11, 'FGH', '', 'j', 'j', 'j', 'j', '10.6147522,122.9324862', '2025-08-10 16:49:16', '2025-08-26 15:20:31'),
	(12, 'sample', '', 'l909', '0', '0', '0', '14.5995133,120.984234', '2025-08-11 09:44:57', '2025-08-11 09:44:57'),
	(13, 'sample12', '', 'l909', '0', '0', '0', '14.5995133,120.984234', '2025-08-11 10:15:33', '2025-08-11 10:15:33'),
	(14, 'JKL', '', '0', '0', '0', '0', '10.6826963,122.9436615', '2025-08-11 10:26:53', '2025-08-26 15:20:59'),
	(15, 'KLM', '', '112', 'a', '1', '1', '10.6827282,122.9436926', '2025-08-11 11:34:41', '2025-08-26 15:21:02'),
	(16, 'LMN', '', 'k', 'k', 'k', 'k', '14.5995133,120.984234', '2025-08-11 14:52:09', '2025-08-26 15:21:06'),
	(17, 'MNO', '', 'o', 'o', 'o', 'o', '14.5995133,120.984234', '2025-08-11 14:59:51', '2025-08-26 15:21:09'),
	(18, 'NOP', '', 'k', 'k', 'k', 'k', '14.5995133,120.984234', '2025-08-11 15:00:31', '2025-08-26 15:21:12'),
	(19, 'ABC Center', '', 'j', 'j', 'j', 'j', '14.5995133,120.984234', '2025-08-11 16:14:07', '2025-08-26 15:20:01');

-- Dumping structure for table rehab_management_main_db.tbl_rehab_center_gallery
CREATE TABLE IF NOT EXISTS `tbl_rehab_center_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rehab_center_id` int(11) NOT NULL DEFAULT 0,
  `file` text NOT NULL,
  `file_desc` varchar(100) NOT NULL DEFAULT '',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table rehab_management_main_db.tbl_rehab_center_gallery: ~7 rows (approximately)
INSERT INTO `tbl_rehab_center_gallery` (`id`, `rehab_center_id`, `file`, `file_desc`, `date_added`) VALUES
	(28, 1, '1-67d292ae164659.71144237.png', 'img 1', '2025-03-13 15:58:40'),
	(29, 1, '1-67d292db750b01.44214893.png', 'img 2', '2025-03-13 16:09:37'),
	(30, 1, 'img1.png', 'img 3', '2025-03-13 16:09:37'),
	(31, 1, 'img2.png', 'img 4', '2025-03-13 16:09:37'),
	(32, 1, 'img3.png', 'img 5', '2025-03-13 16:09:37'),
	(33, 1, 'img4.png', 'img 6', '2025-03-13 16:09:37'),
	(34, 2, 'img4.png', 'img 7', '2025-03-13 16:09:37');

-- Dumping structure for table rehab_management_main_db.tbl_services
CREATE TABLE IF NOT EXISTS `tbl_services` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `rehab_center_id` int(11) NOT NULL DEFAULT 0,
  `service_name` varchar(50) NOT NULL DEFAULT '0',
  `service_fee` decimal(11,2) NOT NULL DEFAULT 0.00,
  `service_desc` text NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table rehab_management_main_db.tbl_services: ~4 rows (approximately)
INSERT INTO `tbl_services` (`service_id`, `rehab_center_id`, `service_name`, `service_fee`, `service_desc`, `date_added`, `date_updated`) VALUES
	(11, 1, 'Detoxification Packages', 30000.00, 'sample only', '2025-03-09 17:54:11', '2025-07-24 15:29:55'),
	(18, 1, 'Mental Rehabilitation', 25000.00, 'sample', '2025-03-11 16:45:47', '2025-07-24 15:29:59'),
	(20, 2, 'Detoxification Packasd', 31500.00, 'u', '2025-03-13 10:34:31', '2025-08-15 09:30:12'),
	(25, 1, 'sample', 234.00, '', '2025-08-15 09:31:15', '2025-08-15 09:31:15');

-- Dumping structure for table rehab_management_main_db.tbl_services_availed
CREATE TABLE IF NOT EXISTS `tbl_services_availed` (
  `service_availed_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `rehab_center_id` int(11) NOT NULL DEFAULT 0,
  `admission_id` int(11) NOT NULL DEFAULT 0,
  `service_id` int(11) NOT NULL DEFAULT 0,
  `service_date` datetime NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`service_availed_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table rehab_management_main_db.tbl_services_availed: ~0 rows (approximately)

-- Dumping structure for table rehab_management_main_db.tbl_services_stages
CREATE TABLE IF NOT EXISTS `tbl_services_stages` (
  `stage_id` int(11) NOT NULL AUTO_INCREMENT,
  `stage_name` varchar(50) NOT NULL DEFAULT '',
  `service_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`stage_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table rehab_management_main_db.tbl_services_stages: ~6 rows (approximately)
INSERT INTO `tbl_services_stages` (`stage_id`, `stage_name`, `service_id`) VALUES
	(9, 'Stage 1', 11),
	(10, 'Stage 2', 11),
	(11, 'Stage 3', 11),
	(14, 'sad', 18),
	(16, 'sample', 20),
	(17, 'sad', 25);

-- Dumping structure for table rehab_management_main_db.tbl_service_stages_task
CREATE TABLE IF NOT EXISTS `tbl_service_stages_task` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `stage_id` int(11) NOT NULL DEFAULT 0,
  `task_name` varchar(50) NOT NULL DEFAULT '',
  `task_desc` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table rehab_management_main_db.tbl_service_stages_task: ~3 rows (approximately)
INSERT INTO `tbl_service_stages_task` (`task_id`, `stage_id`, `task_name`, `task_desc`) VALUES
	(10, 9, 'qwe', 'wdad'),
	(12, 16, 'as', ''),
	(13, 17, '23asdasdasdasdsadasd', '3');

-- Dumping structure for table rehab_management_main_db.tbl_users
CREATE TABLE IF NOT EXISTS `tbl_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_fname` varchar(30) NOT NULL,
  `user_mname` varchar(30) NOT NULL,
  `user_lname` varchar(30) NOT NULL,
  `permanent_address` text NOT NULL,
  `contact_number` varchar(15) NOT NULL DEFAULT '',
  `birthdate` date NOT NULL,
  `birth_place` varchar(100) NOT NULL DEFAULT '',
  `nationality` varchar(25) NOT NULL DEFAULT '',
  `religion` varchar(50) NOT NULL DEFAULT '',
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
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table rehab_management_main_db.tbl_users: ~19 rows (approximately)
INSERT INTO `tbl_users` (`user_id`, `user_fname`, `user_mname`, `user_lname`, `permanent_address`, `contact_number`, `birthdate`, `birth_place`, `nationality`, `religion`, `occupation`, `employer`, `employer_address`, `father_name`, `father_address`, `mother_name`, `mother_address`, `user_category`, `username`, `password`, `rehab_center_id`, `date_added`, `date_updated`) VALUES
	(1, 'test', '', 'test', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', 'U', 'test', '$2y$10$EuesKykzJ5M.ZAMr4ZEb1eA6KFjzuD9mmitnwKm8zorFzOi/3/6QW', 0, '2025-07-10 16:38:22', '2025-07-10 16:38:22'),
	(2, 'test', 'middle', 'a', 'Purok Kingfisher-A, Barangay 16, Bacolod City, Negros Occidental', '', '0000-00-00', '', 'Filipino', '', '', '', '', '', '', '', '', 'U', 'a', '$2y$10$IpW0W0YnbDX3oSicUvYFH.qFZ3L7YJZJ4Lv8XbQB3wKReDcNpeily', 19, '2025-07-24 09:37:24', '2025-08-12 13:50:40'),
	(3, 'Jeffred', 'Pacheco', 'Lim', 'Purok Kingfisher, Barangay 16, Bacolod City, Negros Occidental', '09107980997', '2025-06-01', 'Bacolod City', 'Filipino', 'Christian', 'Software developer', 'BPFC', 'Bacolod City', 'Godofredo Lim', 'Bacolod City', 'Gemma Lim', 'Bacolod City', 'U', 'jep', '$2y$10$X44rBYbbn8EjZkgjJsIJR.uZ99jkNJUaUCgCds6px2jhVS/WrRCQC', 0, '2025-07-24 09:39:58', '2025-07-25 15:07:16'),
	(4, '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', 'k', '$2y$10$vi5GONXpRuhYg9DG8ISOqOnJaiM/ja0zIluR3r1SbbLqvqAMREhUm', 4, '2025-08-10 16:26:09', '0000-00-00 00:00:00'),
	(5, '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', 'kasd', '$2y$10$iyqlrclmExWMUWIaVH0IK.1j7.lrMj75hKTUUO.5fsF7r3LWTcbtm', 5, '2025-08-10 16:27:45', '0000-00-00 00:00:00'),
	(6, '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', 'kasds', '$2y$10$i/vXBIZW1HnLZhhlSyiCtejaER7HRQGXG0y2sL7Vf4Y61n1Y.n/C2', 6, '2025-08-10 16:30:52', '0000-00-00 00:00:00'),
	(7, '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', 'ass', '$2y$10$TqFaLEMR0oxSSl/.BfFRfuCWk4.QQLyCSDFd3ATlwVpt8UPb8iuO2', 7, '2025-08-10 16:40:28', '0000-00-00 00:00:00'),
	(8, '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', 'dasdsad', '$2y$10$e8nMc8n.NBlIRM0N3AF.le5UUAUcJOTgnKSm6UKkcyFaSs3k2trZm', 8, '2025-08-10 16:42:55', '0000-00-00 00:00:00'),
	(9, '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', 'as', '$2y$10$0jccDMVc3CrC32q/Pt6JTu0Dt/A6y4oPgQi8pjsemBlibx5rMWYXa', 9, '2025-08-10 16:43:56', '0000-00-00 00:00:00'),
	(10, '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', 'assss', '$2y$10$CRBcAWi8LG9zP4mNE7q1AOLKAulI0PzyhRWnmegmB4x2TXNo2glLa', 10, '2025-08-10 16:44:41', '0000-00-00 00:00:00'),
	(11, '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', 'j', '$2y$10$gmrcwq6fmxTybwQjIqmCVOK8RCx6wiUi5.VEUYg2ztteVYyv7WoS6', 11, '2025-08-10 16:49:16', '0000-00-00 00:00:00'),
	(12, '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', 'sample', '$2y$10$THxmtOBcOhK5tCJ79d/bIOI1/7A6Hk9XNMygdgi7AINw6y0Ur21ue', 12, '2025-08-11 09:44:57', '0000-00-00 00:00:00'),
	(13, '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', 'sample1', '$2y$10$U8z48fkafZzfdeq1r4WbXOFjvREMpU6r2pel4pWGo8CHpwYUuFizK', 13, '2025-08-11 10:15:33', '0000-00-00 00:00:00'),
	(14, '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', '0', '$2y$10$SZxMCvUhi.XFmJ1Hll9mYe4EyI26KfU1XEvlUGXabQ.LLJf91AS6G', 14, '2025-08-11 10:26:53', '0000-00-00 00:00:00'),
	(15, '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', 'aaa', '$2y$10$LHCiQO.5Zv8y2cebCX0PAeQNKQpgF0OOSoiSD2Hat6ZbP6cnTYwfG', 15, '2025-08-11 11:34:41', '0000-00-00 00:00:00'),
	(16, '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', 'kk', '$2y$10$0lrthSMUzOdtOGSD6nJhWeSNCnbiqy4rwyAuhCZnPcPZa4/t92hkO', 16, '2025-08-11 14:52:09', '0000-00-00 00:00:00'),
	(17, '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', 'o1', '$2y$10$W6QrRG0wrzxCmMnzYT9bqe0isPnLeNGqyeFlWigWXFVkE2qdlMTQ2', 17, '2025-08-11 14:59:51', '0000-00-00 00:00:00'),
	(18, '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', 'kaye', '$2y$10$D0ELyTdIBgF8OL3K2uLuVeNJSIboCVp6H9kB49f5FlC3zTxl1dBfW', 18, '2025-08-11 15:00:31', '0000-00-00 00:00:00'),
	(19, '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', 'jade', '$2y$10$lqV2cUCWhfO6n6FHlKIoJuY4FRm76.xv2iFSD72l98kVubxscyEee', 19, '2025-08-11 16:14:07', '0000-00-00 00:00:00');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
