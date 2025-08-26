-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.11-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.10.0.7023
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table rehab_management_19_db.tbl_admission
CREATE TABLE IF NOT EXISTS `tbl_admission` (
  `admission_id` int(11) NOT NULL AUTO_INCREMENT,
  `rehab_center_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`admission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_management_19_db.tbl_admission: ~0 rows (approximately)

-- Dumping structure for table rehab_management_19_db.tbl_admission_details
CREATE TABLE IF NOT EXISTS `tbl_admission_details` (
  `admission_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `input_id` int(11) NOT NULL DEFAULT 0,
  `input_value` text NOT NULL,
  PRIMARY KEY (`admission_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_management_19_db.tbl_admission_details: ~0 rows (approximately)

-- Dumping structure for table rehab_management_19_db.tbl_admission_services
CREATE TABLE IF NOT EXISTS `tbl_admission_services` (
  `admission_service_id` int(11) NOT NULL AUTO_INCREMENT,
  `admission_id` int(11) NOT NULL DEFAULT 0,
  `service_id` int(11) NOT NULL DEFAULT 0,
  `date_started` date DEFAULT NULL,
  `date_ended` date DEFAULT NULL,
  PRIMARY KEY (`admission_service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_management_19_db.tbl_admission_services: ~0 rows (approximately)

-- Dumping structure for table rehab_management_19_db.tbl_admission_tasks
CREATE TABLE IF NOT EXISTS `tbl_admission_tasks` (
  `admission_task_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL DEFAULT 0,
  `remarks` varchar(50) NOT NULL DEFAULT '',
  `date_added` datetime DEFAULT NULL,
  PRIMARY KEY (`admission_task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_management_19_db.tbl_admission_tasks: ~0 rows (approximately)

-- Dumping structure for table rehab_management_19_db.tbl_inputs
CREATE TABLE IF NOT EXISTS `tbl_inputs` (
  `input_id` int(11) NOT NULL AUTO_INCREMENT,
  `input_label` varchar(50) NOT NULL DEFAULT '',
  `input_type` varchar(50) NOT NULL DEFAULT '',
  `input_require` int(1) NOT NULL DEFAULT 1,
  `rehab_center` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`input_id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_management_19_db.tbl_inputs: ~41 rows (approximately)
INSERT INTO `tbl_inputs` (`input_id`, `input_label`, `input_type`, `input_require`, `rehab_center`) VALUES
	(4, 'First Name', 'text', 1, 0),
	(5, 'Middle Name', 'text', 0, 0),
	(6, 'Last Name', 'text', 1, 0),
	(7, 'Permanent Address', 'textarea', 1, 0),
	(8, 'Tel No.', 'text', 1, 0),
	(9, 'Sex', 'select', 1, 0),
	(10, 'Civil Status', 'select', 1, 0),
	(11, 'Birthdate', 'text', 1, 0),
	(12, 'Age', 'text', 1, 0),
	(13, 'Birthplace', 'text', 1, 0),
	(14, 'Nationality', 'text', 1, 0),
	(15, 'Religion', 'text', 1, 0),
	(16, 'Occupation', 'text', 1, 0),
	(17, 'Employeer', 'text', 0, 0),
	(18, 'Employeer Address', 'textarea', 0, 0),
	(19, 'Father\'s Name', 'text', 0, 0),
	(20, 'Father\'s Address', 'textarea', 0, 0),
	(21, 'Mother\'s Name', 'text', 1, 0),
	(22, 'Mother\'s Address', 'textarea', 1, 0),
	(23, 'Admission Date', 'text', 1, 0),
	(24, 'Admission Time', 'text', 1, 0),
	(25, 'Discharge Date', 'text', 0, 0),
	(26, 'Discharge Time', 'text', 0, 0),
	(27, 'Total No. of Days', 'text', 0, 0),
	(28, 'Admitting Physician', 'text', 1, 0),
	(29, 'Type of Admission', 'select', 1, 0),
	(30, 'Referred By', 'text', 1, 0),
	(31, 'Social Service Classification', 'select', 1, 0),
	(32, 'Alert Allergic To', 'text', 1, 0),
	(33, 'Hospitalition Plan', 'text', 1, 0),
	(34, 'Health Insurance Name', 'text', 1, 0),
	(35, 'Medicare', 'select', 0, 0),
	(36, 'Data Furnish By', 'text', 0, 0),
	(37, 'Address of Informant', 'text', 0, 0),
	(38, 'Relation to Patient', 'text', 0, 0),
	(39, 'Admission Diagnosis', 'text', 0, 0),
	(40, 'ICD Code No.', 'text', 0, 0),
	(41, 'Principal Diagnosis', 'text', 0, 0),
	(42, 'Other Diagnosis', 'text', 0, 0),
	(43, 'Disposition', 'select', 0, 0),
	(44, 'Results', 'select', 0, 0);

-- Dumping structure for table rehab_management_19_db.tbl_input_options
CREATE TABLE IF NOT EXISTS `tbl_input_options` (
  `input_option_id` int(11) NOT NULL AUTO_INCREMENT,
  `input_id` int(11) NOT NULL DEFAULT 0,
  `input_option_label` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`input_option_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_management_19_db.tbl_input_options: ~28 rows (approximately)
INSERT INTO `tbl_input_options` (`input_option_id`, `input_id`, `input_option_label`) VALUES
	(5, 3, 'sadd'),
	(6, 9, 'Male'),
	(7, 9, 'Female'),
	(8, 10, 'Single'),
	(9, 10, 'Married'),
	(10, 10, 'Widowed'),
	(11, 10, 'Seperated'),
	(12, 29, 'New'),
	(13, 29, 'Old'),
	(14, 29, 'Former OPD'),
	(15, 31, 'A'),
	(16, 31, 'B'),
	(17, 31, 'C'),
	(18, 31, 'D'),
	(19, 35, 'SSS'),
	(20, 35, 'GSIS'),
	(21, 43, 'Discharge'),
	(22, 43, 'Transfered'),
	(23, 43, 'Dama'),
	(24, 43, 'Adsconded'),
	(25, 43, 'Recovered'),
	(26, 43, 'Died'),
	(27, 43, '- 48 Hours'),
	(28, 43, '+ 48 Hours'),
	(29, 44, 'Improved'),
	(30, 44, 'Unimproved'),
	(31, 44, 'Autopsy'),
	(32, 44, 'No Autopsy');

-- Dumping structure for table rehab_management_19_db.tbl_rehab_centers
CREATE TABLE IF NOT EXISTS `tbl_rehab_centers` (
  `rehab_center_id` int(11) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_management_19_db.tbl_rehab_centers: ~0 rows (approximately)
INSERT INTO `tbl_rehab_centers` (`rehab_center_id`, `rehab_center_name`, `rehab_center_desc`, `hospital_code`, `med_record_no`, `rehab_center_city`, `rehab_center_complete_address`, `rehab_center_coordinates`, `date_added`, `date_updated`) VALUES
	(19, 'NEGROS OCCIDENTAL REHABILITATION CENTER', '', 'j', 'j', 'j', 'j', '14.5995133,120.984234', '2025-08-11 16:14:07', '2025-08-22 15:34:41');

-- Dumping structure for table rehab_management_19_db.tbl_rehab_center_gallery
CREATE TABLE IF NOT EXISTS `tbl_rehab_center_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rehab_center_id` int(11) NOT NULL DEFAULT 0,
  `file` text NOT NULL,
  `file_desc` varchar(100) NOT NULL DEFAULT '',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_management_19_db.tbl_rehab_center_gallery: ~0 rows (approximately)

-- Dumping structure for table rehab_management_19_db.tbl_services
CREATE TABLE IF NOT EXISTS `tbl_services` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `rehab_center_id` int(11) NOT NULL DEFAULT 0,
  `service_name` varchar(50) NOT NULL DEFAULT '0',
  `service_fee` decimal(11,2) NOT NULL DEFAULT 0.00,
  `service_desc` text NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_management_19_db.tbl_services: ~0 rows (approximately)
INSERT INTO `tbl_services` (`service_id`, `rehab_center_id`, `service_name`, `service_fee`, `service_desc`, `date_added`, `date_updated`) VALUES
	(1, 19, 'TREATMENT AND REHABILITATION PROGRAM', 30000.00, '', '2025-08-22 08:02:54', '2025-08-22 15:40:15');

-- Dumping structure for table rehab_management_19_db.tbl_services_availed
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_management_19_db.tbl_services_availed: ~0 rows (approximately)

-- Dumping structure for table rehab_management_19_db.tbl_services_stages
CREATE TABLE IF NOT EXISTS `tbl_services_stages` (
  `stage_id` int(11) NOT NULL AUTO_INCREMENT,
  `stage_name` varchar(50) NOT NULL DEFAULT '',
  `service_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`stage_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_management_19_db.tbl_services_stages: ~6 rows (approximately)
INSERT INTO `tbl_services_stages` (`stage_id`, `stage_name`, `service_id`) VALUES
	(1, 'Stage 12', 0),
	(2, 'stage1', 0),
	(3, 'sddsf 1', 0),
	(5, 'PHASE 1: ADMISSION AND DETOXIFICATION', 1),
	(6, 'TRANSITION PHASE', 1),
	(7, 'PHASE 3: INTEGRATION CARE', 1);

-- Dumping structure for table rehab_management_19_db.tbl_service_stages_task
CREATE TABLE IF NOT EXISTS `tbl_service_stages_task` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `stage_id` int(11) NOT NULL DEFAULT 0,
  `task_name` varchar(50) NOT NULL DEFAULT '',
  `task_desc` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_management_19_db.tbl_service_stages_task: ~6 rows (approximately)
INSERT INTO `tbl_service_stages_task` (`task_id`, `stage_id`, `task_name`, `task_desc`) VALUES
	(2, 4, 'same', 'da'),
	(4, 5, 'Admission and Orientation with Folks', ''),
	(5, 5, 'Laboratory Testing', ''),
	(6, 5, 'Medical Detoxifcation', ''),
	(7, 5, 'Medical Examination', ''),
	(8, 6, 'Dorm Transfer', ''),
	(9, 6, 'Patient Orientation and Observation of Daily Activ', '');

-- Dumping structure for table rehab_management_19_db.tbl_users
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table rehab_management_19_db.tbl_users: ~0 rows (approximately)
INSERT INTO `tbl_users` (`user_id`, `user_fname`, `user_mname`, `user_lname`, `permanent_address`, `contact_number`, `birthdate`, `birth_place`, `nationality`, `religion`, `occupation`, `employer`, `employer_address`, `father_name`, `father_address`, `mother_name`, `mother_address`, `user_category`, `username`, `password`, `rehab_center_id`, `date_added`, `date_updated`) VALUES
	(1, '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '', 'R', '', '$2y$10$h8eYbcAwpZFcvQK6GKHnwecv4DrcVtNzjrMSCVz3RbeAzL/OANZOe', 19, '2025-08-11 16:14:07', '2025-08-22 15:35:13');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
