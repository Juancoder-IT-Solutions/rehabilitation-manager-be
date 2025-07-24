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
                'rehab_center_id' => $rehab_center_id,
                'date_added' => date("Y-m-d H:i:s"),
                'date_updated' => null,
            ];

            $this->insert("tbl_users", $form);

            $this->commit();
            return 1;
        } catch (Exception $e) {
            $this->rollback();
            $this->response = "error";
            return $e->getMessage();
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

    public function show_rehab_center_account(){
        $id = $this->clean($this->inputs['id']);
        $row = array();
        $count = 1;

        // main profile
        $fetch_rehab_center = $this->select($this->table, "*", "rehab_center_id='$id'");
        $rehab_center_row = $fetch_rehab_center->fetch_assoc();
        unset($rehab_center_row['rehab_center_coordinates']);
        unset($rehab_center_row['rehab_center_complete_address']);
        $row['rehab_center_account'] = $rehab_center_row;

        // services
        $sRow = array();
        $fetch_services = $this->select("tbl_services", "*", "rehab_center_id='$id'");
        while($services_row = $fetch_services->fetch_assoc()){
            $sRow[] = $services_row;
        }

        $row['rehab_services'] = $sRow;

        // gallery
        $gRow = array();
        $count = 1;
        $fetch_gallery = $this->select("tbl_rehab_center_gallery", "*", "rehab_center_id='$id'");
        while($gallery_row = $fetch_gallery->fetch_assoc()){
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
