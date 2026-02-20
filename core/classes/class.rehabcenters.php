<?php
class RehabCenters extends Connection
{
    private $table = 'tbl_rehab_centers';
    public $pk = 'rehab_center_id';
    public $name = 'rehab_center_name';
    public $module_name = "Rehab Centers";

    public $inputs = [];
    public $response = "";

    public $searchable = ['rehab_center_name'];
    public $uri = "Rehab Centers";

    private $servername = HOST;
    private $username = USER;
    private $password = PASSWORD;
    private $dbname = DBNAME;
    private $result = array();
    private $mysqli = '';

    public $authUserId = 0;

    public function register()
    {
        try {
            $this->response = "success";

            $this->checker();
            $this->begin_transaction();

            // Clean inputs
            $rehab_center_name           = $this->clean($this->inputs['rehab_center_name']);
            $hospital_code               = $this->clean($this->inputs['hospital_code']);
            $med_record_no               = $this->clean($this->inputs['med_record_no']);
            $rehab_center_city           = $this->clean($this->inputs['rehab_center_city']);
            $rehab_center_complete_addr = $this->clean($this->inputs['rehab_center_complete_address']);
            $rehab_center_coordinates    = $this->clean($this->inputs['rehab_center_coordinates']);
            $password_plain              = $this->clean($this->inputs['password']);

            $username = $this->clean($this->inputs['username']);

            $existing_center = $this->select($this->table, $this->pk, "rehab_center_name = '$rehab_center_name'");
            if (!is_object($existing_center)) throw new Exception($existing_center);
            if ($existing_center->num_rows > 0) return 2; // rehab_center_name already exists

            $existing_user = $this->select('tbl_users', $this->pk, "username = '$username'");
            if (!is_object($existing_user)) throw new Exception($existing_user);
            if ($existing_user->num_rows > 0) return 3;

            $hashed_password = password_hash($password_plain, PASSWORD_DEFAULT);

            $form = array(
                'rehab_center_name'             => $rehab_center_name,
                'hospital_code'                 => $hospital_code,
                'med_record_no'                 => $med_record_no,
                'rehab_center_city'             => $rehab_center_city,
                'rehab_center_complete_address' => $rehab_center_complete_addr,
                'rehab_center_coordinates'      => $rehab_center_coordinates,
            );

            // Insert
            $rehab_center_id = $this->insert($this->table, $form, 'Y');
            if (!is_int($rehab_center_id)) throw new Exception($rehab_center_id);

            $form = [
                'username'   => $username,
                'password'   => $hashed_password,
                'rehab_center_id' => $rehab_center_id
            ];

            $this->insert("tbl_users", $form);

            $this->commit();
            $this->createRehabCenterDatabase('rehab_management_' . $rehab_center_id, $rehab_center_id);
            return 1;
        } catch (Exception $e) {
            $this->rollback();
            $this->response = "error";
            return $e->getMessage();
        }
    }

    private function createRehabCenterDatabase($rehab_center_name, $rehab_center_id)
    {
        $dbName = preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($rehab_center_name)) . '_db';

        // Create a fresh connection for DB creation
        $conn = $this->mysqli = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Create database
        if (!$conn->query("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
            throw new Exception("Error creating database: " . $conn->error);
        }

        // Switch to new DB
        if (!$conn->select_db($dbName)) {
            throw new Exception("Error selecting database: " . $conn->error);
        }

        // Create tables
        $this->createTblAdmission($conn);
        $this->createTblAdmissionDetails($conn);
        $this->createTblAdmissionServices($conn);
        $this->createTblAdmissionTasks($conn);
        $this->createTblAppointments($conn);
        $this->createTblPayments($conn);
        $this->createTblInputs($conn);
        $this->createTblInputOptions($conn);
        $this->createTblServices($conn);
        $this->createTblServicesStages($conn);
        $this->createTblServiceStagesTask($conn);
        $this->createTblUsers($conn, $rehab_center_id, $this->inputs['username'], $this->inputs['password']);
        // $this->createTblAdmissionServices($conn);
        $this->createTblServicesAvailed($conn);
        $this->createRehab($conn, $rehab_center_id);
        $this->createRehabGallery($conn);

        $conn->close();
    }

    private function createTblAdmission($conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_admission` (
        `admission_id` int(11) NOT NULL AUTO_INCREMENT,
        `rehab_center_id` int(11) NOT NULL DEFAULT 0,
        `admission_reference_id` int(11) NOT NULL DEFAULT 0,
        `user_id` int(11) NOT NULL DEFAULT 0,
        `date_added` datetime NOT NULL DEFAULT current_timestamp(),
        `start_date` date DEFAULT NULL,
        `end_date` date DEFAULT NULL,
        `status` varchar(1) NOT NULL DEFAULT '',
        PRIMARY KEY (`admission_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        if (!$conn->query($sql)) throw new Exception("Error creating tbl_admission: " . $conn->error);
    }

    private function createTblAppointments($conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_appointments` (
        `appointment_id` INT(11) NOT NULL AUTO_INCREMENT,
        `admission_id` INT(11) NOT NULL,
        `rehab_center_id` INT(11) NOT NULL,
        `remarks` TEXT DEFAULT NULL,
        `appointment_date` DATE NOT NULL,
        `status` VARCHAR(1) NOT NULL DEFAULT '',
        `date_added` DATETIME NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`appointment_id`),
        KEY `idx_status_date` (`status`, `appointment_date`),
        CONSTRAINT `fk_appointments_admission` FOREIGN KEY (`admission_id`) REFERENCES `tbl_admission`(`admission_id`) ON DELETE CASCADE,
        CONSTRAINT `fk_appointments_rehab_center` FOREIGN KEY (`rehab_center_id`) REFERENCES `tbl_rehab_centers`(`rehab_center_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        if (!$conn->query($sql)) {
            throw new Exception("Error creating tbl_appointments: " . $conn->error);
        }
    }

    private function createTblPayments($conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_payments` (
        `payment_id` INT(11) NOT NULL AUTO_INCREMENT,
        `admission_id` INT(11) NOT NULL DEFAULT 0,
        `payment_intent_id` VARCHAR(50) NOT NULL DEFAULT '0',
        `reference_number` VARCHAR(50) DEFAULT NULL,
        `payment_method` VARCHAR(50) DEFAULT NULL,
        `user_id` INT(11) NOT NULL DEFAULT 0,
        `payment_date` DATE DEFAULT NULL,
        `status` VARCHAR(1) DEFAULT 'S',
        `date_added` DATETIME NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`payment_id`),
        KEY `idx_admission_user` (`admission_id`, `user_id`),
        CONSTRAINT `fk_payments_admission` FOREIGN KEY (`admission_id`) REFERENCES `tbl_admission`(`admission_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        if (!$conn->query($sql)) {
            throw new Exception("Error creating tbl_payments: " . $conn->error);
        }
    }

    private function createTblAdmissionDetails($conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_admission_details` (
        `admission_detail_id` int(11) NOT NULL AUTO_INCREMENT,`
        `admission_id` int(11) NOT NULL DEFAULT 0,
        `input_id` int(11) NOT NULL DEFAULT 0,
        `input_value` text NOT NULL,
        PRIMARY KEY (`admission_detail_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        if (!$conn->query($sql)) throw new Exception("Error creating tbl_admission_details: " . $conn->error);
    }

    private function createTblAdmissionServices($conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_admission_services` (
        `admission_service_id` int(11) NOT NULL AUTO_INCREMENT,
        `admission_id` int(11) NOT NULL DEFAULT 0,
        `service_id` int(11) NOT NULL DEFAULT 0,
        `date_started` date DEFAULT NULL,
        `date_ended` date DEFAULT NULL,
        `date_added` datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`admission_service_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        if (!$conn->query($sql)) throw new Exception("Error creating tbl_admission_services: " . $conn->error);
    }

    private function createTblAdmissionTasks($conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_admission_tasks` (
        `admission_task_id` int(11) NOT NULL AUTO_INCREMENT,
        `admission_id` int(11) NOT NULL DEFAULT 0,
        `admission_service_id` int(11) NOT NULL DEFAULT 0,
        `stage_id` int(11) NOT NULL DEFAULT 0,
        `task_id` int(11) NOT NULL DEFAULT 0,
        `remarks` varchar(50) NOT NULL DEFAULT '',
        `status` int(11) NOT NULL DEFAULT 0,
        `date_added` datetime DEFAULT current_timestamp(),
        PRIMARY KEY (`admission_task_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        if (!$conn->query($sql)) throw new Exception("Error creating tbl_admission_tasks: " . $conn->error);
    }


    private function createTblInputs($conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_inputs` (
        `input_id` int(11) NOT NULL AUTO_INCREMENT,
        `input_label` varchar(50) NOT NULL DEFAULT '',
        `input_type` varchar(50) NOT NULL DEFAULT '',
        `input_require` int(1) NOT NULL DEFAULT 1,
        `rehab_center` int(11) NOT NULL DEFAULT 0,
        PRIMARY KEY (`input_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        if (!$conn->query($sql)) throw new Exception("Error creating tbl_inputs: " . $conn->error);
    }

    private function createTblInputOptions($conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_input_options` (
        `input_option_id` int(11) NOT NULL AUTO_INCREMENT,
        `input_id` int(11) NOT NULL DEFAULT 0,
        `input_option_label` varchar(50) NOT NULL DEFAULT '',
        PRIMARY KEY (`input_option_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        if (!$conn->query($sql)) throw new Exception("Error creating tbl_input_options: " . $conn->error);
    }

    private function createRehab($conn, $rehab_center_id)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_rehab_centers` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        if (!$conn->query($sql)) {
            throw new Exception("Error creating tbl_rehab_centers: " . $conn->error);
        }

        $stmt = $conn->prepare("
        INSERT INTO tbl_rehab_centers (
            rehab_center_id,
            rehab_center_name,
            rehab_center_desc,
            hospital_code,
            med_record_no,
            rehab_center_city,
            rehab_center_complete_address,
            rehab_center_coordinates
        ) VALUES (?, ?, '', ?, ?, ?, ?, ?)
    ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param(
            "issssss",
            $rehab_center_id,
            $this->inputs['rehab_center_name'],
            $this->inputs['hospital_code'],
            $this->inputs['med_record_no'],
            $this->inputs['rehab_center_city'],
            $this->inputs['rehab_center_complete_address'],
            $this->inputs['rehab_center_coordinates']
        );

        if (!$stmt->execute()) {
            throw new Exception("Error inserting into tbl_rehab_centers in new DB: " . $stmt->error);
        }

        $stmt->close();
    }

    private function createRehabGallery($conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_rehab_center_gallery` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `rehab_center_id` int(11) NOT NULL DEFAULT 0,
        `file` text NOT NULL,
        `file_desc` varchar(100) NOT NULL DEFAULT '',
        `date_added` datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        if (!$conn->query($sql)) {
            throw new Exception("Error creating tbl_rehab_center_gallery: " . $conn->error);
        }
    }

    private function createTblServices($conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_services` (
        `service_id` int(11) NOT NULL AUTO_INCREMENT,
        `rehab_center_id` int(11) NOT NULL DEFAULT 0,
        `service_name` varchar(50) NOT NULL DEFAULT '0',
        `service_fee` decimal(11,2) NOT NULL DEFAULT 0.00,
        `service_desc` text NOT NULL,
        `date_added` datetime NOT NULL DEFAULT current_timestamp(),
        `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`service_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        if (!$conn->query($sql)) throw new Exception("Error creating tbl_services: " . $conn->error);
    }

    private function createTblServicesAvailed($conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_services_availed` (
        `service_availed_id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL DEFAULT 0,
        `rehab_center_id` int(11) NOT NULL DEFAULT 0,
        `admission_id` int(11) NOT NULL DEFAULT 0,
        `service_id` int(11) NOT NULL DEFAULT 0,
        `service_date` datetime NOT NULL,
        `date_added` datetime NOT NULL DEFAULT current_timestamp(),
        `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`service_availed_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        if (!$conn->query($sql)) {
            throw new Exception("Error creating tbl_services_availed: " . $conn->error);
        }
    }

    private function createTblServicesStages($conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_services_stages` (
        `stage_id` int(11) NOT NULL AUTO_INCREMENT,
        `stage_name` varchar(50) NOT NULL DEFAULT '',
        `service_id` int(11) NOT NULL DEFAULT 0,
        PRIMARY KEY (`stage_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        if (!$conn->query($sql)) throw new Exception("Error creating tbl_services_stages: " . $conn->error);
    }

    private function createTblServiceStagesTask($conn)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_service_stages_task` (
        `task_id` int(11) NOT NULL AUTO_INCREMENT,
        `stage_id` int(11) NOT NULL DEFAULT 0,
        `service_id` int(11) NOT NULL DEFAULT 0,
        `task_name` varchar(50) NOT NULL DEFAULT '',
        `task_desc` varchar(250) NOT NULL DEFAULT '',
        PRIMARY KEY (`task_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        if (!$conn->query($sql)) throw new Exception("Error creating tbl_service_stages_task: " . $conn->error);
    }

    private function createTblUsers($conn, $rehab_center_id, $username = null, $password = null)
    {
        // 1. Create the table
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_users` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        if (!$conn->query($sql)) {
            throw new Exception("Error creating tbl_users: " . $conn->error);
        }

        // 2. If username and password are given, insert default record
        if ($username && $password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO `tbl_users` (
            user_fname, user_mname, user_lname, permanent_address, contact_number,
            birthdate, birth_place, nationality, religion, occupation, employer,
            employer_address, father_name, father_address, mother_name, mother_address,
            user_category, username, password, rehab_center_id) VALUES (
            '', '', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '', '',
            'R', ?, ?, ?)");

            $stmt->bind_param("ssi", $username, $hashed_password, $rehab_center_id);

            if (!$stmt->execute()) {
                throw new Exception("Error inserting default user: " . $stmt->error);
            }
        }
    }


    public function show()
    {
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $rows = array();
        $count = 1;
        $result = $this->select($this->table, '*', $param);
        while ($row = $result->fetch_assoc()) {
            $row['count'] = $count++;
            $rows[] = $row;
        }
        return $rows;
    }

    public function show_public()
    {
        $rows = array();
        $count = 1;
        $result = $this->select("$this->table rc LEFT JOIN tbl_services s ON rc.rehab_center_id=s.rehab_center_id LEFT JOIN tbl_rehab_center_gallery rg ON rc.rehab_center_id=rg.rehab_center_id", 'rc.rehab_center_id, rc.rehab_center_name, rc.rehab_center_city, rc.rehab_center_desc, rg.file as file_name, COUNT(DISTINCT(s.service_id)) as total_services', "rc.rehab_center_id > 0 GROUP BY rc.rehab_center_id ORDER BY rc.rehab_center_name ASC");
        while ($row = $result->fetch_assoc()) {
            $row['count'] = $count++;
            $rows[] = $row;
        }
        return $rows;
    }

    public function show_rehab_center_account()
    {
        $id = $this->clean($this->inputs['id']);
        $allow_show_coordinates = $this->clean($this->inputs['allow_show_coordinates']);

        // use rehab center database
        $this->raw_query("USE rehab_management_" . $id . "_db");
        $row = array();
        $count = 1;

        // main profile
        $fetch_rehab_center = $this->select($this->table, "*", "rehab_center_id='$id'");
        $rehab_center_row = $fetch_rehab_center->fetch_assoc();

        if (!$allow_show_coordinates) {
            unset($rehab_center_row['rehab_center_coordinates']);
            unset($rehab_center_row['rehab_center_complete_address']);
        }

        $row['rehab_center_account'] = $rehab_center_row;

        // services
        $sRow = array();
        $fetch_services = $this->select("tbl_services", "*", "rehab_center_id='$id'");
        while ($services_row = $fetch_services->fetch_assoc()) {
            $sRow[] = $services_row;
        }

        $row['rehab_services'] = $sRow;

        // gallery
        $gRow = array();
        $count = 1;
        $fetch_gallery = $this->select("tbl_rehab_center_gallery", "*", "rehab_center_id='$id'");
        while ($gallery_row = $fetch_gallery->fetch_assoc()) {
            $gallery_row['count'] = $count++;
            $gRow[] = $gallery_row;
        }

        $row['rehab_gallery'] = $gRow;

        return $row;
    }

    public function remove()
    {
        $ids = implode(",", $this->inputs['ids']);
        return $this->delete($this->table, "$this->pk IN ($ids)");
    }

    public static function name($primary_id)
    {
        $self = new self;
        $result = $self->select($self->table, $self->name, "$self->pk  = '$primary_id'");
        if ($result->num_rows == 0) {
            return "";
        } else {
            $row = $result->fetch_assoc();
            return $row[$self->name];
        }
    }
}
