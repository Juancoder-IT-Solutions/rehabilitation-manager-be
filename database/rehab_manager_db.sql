-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.30-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table rehab_manager_db.tbl_admission
CREATE TABLE IF NOT EXISTS `tbl_admission` (
  `admission_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `rehab_center_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
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
  `admission_diagnosis` text,
  `other_diagnosis` text,
  `principal_operation` varchar(100) DEFAULT NULL,
  `other_operation` varchar(100) DEFAULT NULL,
  `accident_injury_poisoning` varchar(100) DEFAULT NULL,
  `place_of_occurence` varchar(100) DEFAULT NULL,
  `disposition` varchar(25) DEFAULT NULL COMMENT 'DISCHARGE, TRANSFERRED, DAMA, ABSCONDED, RECOVERED, DIED, -48 HOURS, +48 HOURS',
  `results` varchar(25) DEFAULT NULL COMMENT 'IMPROVED, UNIMPROVED, AUTOPSY, NO AUTOPSY',
  `attending_physician` varchar(100) DEFAULT NULL,
  `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` varchar(1) DEFAULT 'P',
  PRIMARY KEY (`admission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_manager_db.tbl_admission: ~1 rows (approximately)
INSERT INTO `tbl_admission` (`admission_id`, `user_id`, `rehab_center_id`, `service_id`, `admission_date`, `discharge_date`, `admitting_physician`, `ward`, `type_of_admission`, `referred_by`, `social_service_classification`, `allergic_to`, `hospitalization_plan`, `health_insurance_name`, `medicare`, `data_furnish_by`, `address_of_informant`, `relation_to_patient`, `admission_diagnosis`, `other_diagnosis`, `principal_operation`, `other_operation`, `accident_injury_poisoning`, `place_of_occurence`, `disposition`, `results`, `attending_physician`, `date_added`, `date_updated`, `status`) VALUES
	(9, 3, 1, 11, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '', 'non', 'A', 'none', 'none', 'maxicare', 'SSS', 'Kaye', 'Pulupandan', 'fb', '', '', '', '', '', '', '', '', '', '2025-07-25 15:27:13', '2025-07-25 15:42:08', 'P');

-- Dumping structure for table rehab_manager_db.tbl_rehab_centers
CREATE TABLE IF NOT EXISTS `tbl_rehab_centers` (
  `rehab_center_id` int(11) NOT NULL AUTO_INCREMENT,
  `rehab_center_name` varchar(150) NOT NULL,
  `rehab_center_desc` text NOT NULL,
  `hospital_code` varchar(75) NOT NULL,
  `med_record_no` varchar(75) NOT NULL,
  `rehab_center_city` varchar(150) NOT NULL,
  `rehab_center_complete_address` varchar(150) NOT NULL,
  `rehab_center_coordinates` text NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rehab_center_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_manager_db.tbl_rehab_centers: ~3 rows (approximately)
INSERT INTO `tbl_rehab_centers` (`rehab_center_id`, `rehab_center_name`, `rehab_center_desc`, `hospital_code`, `med_record_no`, `rehab_center_city`, `rehab_center_complete_address`, `rehab_center_coordinates`, `date_added`, `date_updated`) VALUES
	(1, 'The New Beginnings Foundation, Inc. - Bacolod', '[Center Name] is a compassionate and professional rehabilitation facility dedicated to helping individuals overcome substance abuse, behavioral health issues, and physical impairments. Our center provides a safe, supportive, and structured environment where patients can focus on their recovery journey with dignity and care.\n\nWe offer a comprehensive range of services including medical detox, inpatient and outpatient rehabilitation, individual and group counseling, physical therapy, occupational therapy, and holistic wellness programs. Our multidisciplinary team of doctors, therapists, nurses, and support staff work together to develop personalized treatment plans tailored to each patient’s unique needs and recovery goals.\n\nAt [Center Name], we believe that recovery is a lifelong process. We are committed to providing continuous support, education, and aftercare services to ensure long-term success and reintegration into the community. Our goal is to help individuals reclaim their lives, restore their independence, and build a brighter future.', '002', '', 'Bacolod City', '28 Atis corner Kamachile Streets, La Salle Ave, Bacolod, 6100', '', '2025-03-13 14:51:59', '2025-07-14 16:02:19'),
	(2, 'Balay Silangan', 'At [Center Name], we believe that recovery is a lifelong process. We are committed to providing continuous support, education, and aftercare services to ensure long-term success and reintegration into the community. Our goal is to help individuals reclaim their lives, restore their independence, and build a brighter future.', '003', '', 'Bacolod City', '28 Atis corner Kamachile Streets, La Salle Ave, Bacolod, 6100', '', '2025-03-13 14:51:59', '2025-07-14 16:05:31'),
	(3, 'Negros Occidental Drug Rehabilitation Center', 'Contact us at nodrc@yahoo.com', '004', '', 'Bacolod City', 'Camp Gen. Aniceto Lacson St., Victorias City Negros Occidental', '', '2025-03-13 14:51:59', '2025-07-14 16:05:31');

-- Dumping structure for table rehab_manager_db.tbl_rehab_center_gallery
CREATE TABLE IF NOT EXISTS `tbl_rehab_center_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rehab_center_id` int(11) NOT NULL DEFAULT '0',
  `file` text NOT NULL,
  `file_desc` varchar(100) NOT NULL DEFAULT '',
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_manager_db.tbl_rehab_center_gallery: ~7 rows (approximately)
INSERT INTO `tbl_rehab_center_gallery` (`id`, `rehab_center_id`, `file`, `file_desc`, `date_added`) VALUES
	(28, 1, '1-67d292ae164659.71144237.png', 'img 1', '2025-03-13 15:58:40'),
	(29, 1, '1-67d292db750b01.44214893.png', 'img 2', '2025-03-13 16:09:37'),
	(30, 1, 'img1.png', 'img 3', '2025-03-13 16:09:37'),
	(31, 1, 'img2.png', 'img 4', '2025-03-13 16:09:37'),
	(32, 1, 'img3.png', 'img 5', '2025-03-13 16:09:37'),
	(33, 1, 'img4.png', 'img 6', '2025-03-13 16:09:37'),
	(34, 2, 'img4.png', 'img 7', '2025-03-13 16:09:37');

-- Dumping structure for table rehab_manager_db.tbl_services
CREATE TABLE IF NOT EXISTS `tbl_services` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `rehab_center_id` int(11) NOT NULL DEFAULT '0',
  `service_name` varchar(50) NOT NULL DEFAULT '0',
  `service_fee` decimal(11,2) NOT NULL DEFAULT '0.00',
  `service_desc` text NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_manager_db.tbl_services: ~3 rows (approximately)
INSERT INTO `tbl_services` (`service_id`, `rehab_center_id`, `service_name`, `service_fee`, `service_desc`, `date_added`, `date_updated`) VALUES
	(11, 1, 'Detoxification Packages', 30000.00, 'sample only', '2025-03-09 17:54:11', '2025-07-24 15:29:55'),
	(18, 1, 'Mental Rehabilitation', 25000.00, 'sample', '2025-03-11 16:45:47', '2025-07-24 15:29:59'),
	(20, 2, 'Detoxification Packages', 31500.00, 'u', '2025-03-13 10:34:31', '2025-07-24 15:30:04');

-- Dumping structure for table rehab_manager_db.tbl_services_availed
CREATE TABLE IF NOT EXISTS `tbl_services_availed` (
  `service_availed_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `rehab_center_id` int(11) NOT NULL DEFAULT '0',
  `admission_id` int(11) NOT NULL DEFAULT '0',
  `service_id` int(11) NOT NULL DEFAULT '0',
  `service_date` datetime NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`service_availed_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_manager_db.tbl_services_availed: ~0 rows (approximately)

-- Dumping structure for table rehab_manager_db.tbl_users
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
  `rehab_center_id` int(11) NOT NULL DEFAULT '0' COMMENT '0 if user',
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_manager_db.tbl_users: ~3 rows (approximately)
INSERT INTO `tbl_users` (`user_id`, `user_fname`, `user_mname`, `user_lname`, `permanent_address`, `contact_number`, `birthdate`, `birth_place`, `nationality`, `religion`, `occupation`, `employer`, `employer_address`, `father_name`, `father_address`, `mother_name`, `mother_address`, `user_category`, `username`, `password`, `rehab_center_id`, `date_added`, `date_updated`) VALUES
	(1, 'test', '', 'test', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', 'U', 'test', '$2y$10$EuesKykzJ5M.ZAMr4ZEb1eA6KFjzuD9mmitnwKm8zorFzOi/3/6QW', 0, '2025-07-10 16:38:22', '2025-07-10 16:38:22'),
	(2, 'test', 'middle', 'a', 'Purok Kingfisher-A, Barangay 16, Bacolod City, Negros Occidental', '', '0000-00-00', '', 'Filipino', '', '', '', '', '', '', '', '', 'U', 'a', '$2y$10$IpW0W0YnbDX3oSicUvYFH.qFZ3L7YJZJ4Lv8XbQB3wKReDcNpeily', 0, '2025-07-24 09:37:24', '2025-07-24 10:34:29'),
	(3, 'Jeffred', 'Pacheco', 'Lim', 'Purok Kingfisher, Barangay 16, Bacolod City, Negros Occidental', '09107980997', '2025-06-01', 'Bacolod City', 'Filipino', 'Christian', 'Software developer', 'BPFC', 'Bacolod City', 'Godofredo Lim', 'Bacolod City', 'Gemma Lim', 'Bacolod City', 'U', 'jep', '$2y$10$X44rBYbbn8EjZkgjJsIJR.uZ99jkNJUaUCgCds6px2jhVS/WrRCQC', 0, '2025-07-24 09:39:58', '2025-07-25 15:07:16');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
