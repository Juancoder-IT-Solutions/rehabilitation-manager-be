<?php
class Admission extends Connection
{
    private $table = 'tbl_admission';
    public $pk = 'admission_id';
    public $name = 'type_of_admission';
    public $module_name = "Admission";

    public $inputs = [];
    public $response = "";

    public $searchable = ['type_of_admission'];
    public $uri = "Admission";

    public $authUserId = 0;
    public $authRehabCenterId = 0;

    public function add()
    {
        try {
            $this->response = "success";

            $this->checker();
            $this->begin_transaction();

            $user_id                        =   $this->clean($this->inputs['user_id']);
            $rehab_center_id                =   $this->clean($this->inputs['rehab_center_id']);
            $service_id                     =   $this->clean($this->inputs['service_id']);


            $is_exist = $this->select($this->table, $this->pk, "rehab_center_id = '$rehab_center_id' AND service_id='$service_id' AND user_id='$user_id' AND status='P'");

            if (!is_object($is_exist))
                throw new Exception($is_exist);

            if ($is_exist->num_rows > 0) {
                return -2;
            }

            $form = array(
                'user_id'                        =>   $this->clean($this->inputs['user_id']),
                'rehab_center_id'                =>   $this->clean($this->inputs['rehab_center_id']),
                'service_id'                     =>   $this->clean($this->inputs['service_id']),
                'admission_date'                 =>   $this->clean($this->inputs['admission_date']),
                'discharge_date'                 =>   $this->clean($this->inputs['discharge_date']),
                'admitting_physician'            =>   $this->clean($this->inputs['admitting_physician']),
                'ward'                           =>   $this->clean($this->inputs['ward']),
                'type_of_admission'              =>   $this->clean($this->inputs['type_of_admission']),
                'referred_by'                    =>   $this->clean($this->inputs['referred_by']),
                'social_service_classification'  =>   $this->clean($this->inputs['social_service_classification']),
                'allergic_to'                    =>   $this->clean($this->inputs['allergic_to']),
                'hospitalization_plan'           =>   $this->clean($this->inputs['hospitalization_plan']),
                'health_insurance_name'          =>   $this->clean($this->inputs['health_insurance_name']),
                'medicare'                       =>   $this->clean($this->inputs['medicare']),
                'data_furnish_by'                =>   $this->clean($this->inputs['data_furnish_by']),
                'address_of_informant'           =>   $this->clean($this->inputs['address_of_informant']),
                'relation_to_patient'            =>   $this->clean($this->inputs['relation_to_patient']),
                'admission_diagnosis'            =>   $this->clean($this->inputs['admission_diagnosis']),
                'other_diagnosis'                =>   $this->clean($this->inputs['other_diagnosis']),
                'principal_operation'            =>   $this->clean($this->inputs['principal_operation']),
                'other_operation'                =>   $this->clean($this->inputs['other_operation']),
                'accident_injury_poisoning'      =>   $this->clean($this->inputs['accident_injury_poisoning']),
                'place_of_occurence'             =>   $this->clean($this->inputs['place_of_occurence']),
                'disposition'                    =>   $this->clean($this->inputs['disposition']),
                'results'                        =>   $this->clean($this->inputs['results']),
                'attending_physician'            =>   $this->clean($this->inputs['attending_physician']),
                'date_added'                     =>   $this->getCurrentDate()
            );
            $insert_query = $this->insert($this->table, $form);
            if (!is_int($insert_query))
                throw new Exception($insert_query);

            $this->commit();
            return 1;
        } catch (Exception $e) {
            $this->rollback();
            $this->response = "error";
            return $e->getMessage();
        }
    }

    public function add_mobile()
    {
        try {
            $this->response = "success";

            $this->checker();
            $this->begin_transaction();

            $user_id                        =   $this->clean($this->inputs['user_id']);
            $rehab_center_id                =   $this->clean($this->inputs['rehab_center_id']);
            $form_data                      =   $this->clean($this->inputs['form_data']);

            $this->query("USE rehab_management_{$rehab_center_id}_db");


            $is_exist = $this->select($this->table, $this->pk, "rehab_center_id = '$rehab_center_id' AND user_id='$user_id' AND (status != 'F' AND status != 'D')");

            if (!is_object($is_exist))
                throw new Exception($is_exist);

            if ($is_exist->num_rows > 0) {
                return -2;
            }

            $form = array(
                'user_id'                        =>   $this->clean($this->inputs['user_id']),
                'rehab_center_id'                =>   $this->clean($this->inputs['rehab_center_id']),
                'status'                         =>   'P',
                'date_added'                     =>   $this->getCurrentDate()
            );

            $admission_id = $this->insert($this->table, $form, "Y");

            if (!is_int($admission_id))
                throw new Exception($admission_id);

            $this->commit();

            // insert form data
            foreach ($form_data as $value) {
                $form_detail = array(
                    'input_id' => $value['input_id'],
                    'input_value' => $value['value'],
                    'admission_id' => $admission_id,
                );
                $this->insert("tbl_admission_details", $form_detail);
            }


            if ($admission_id > 0) {
                // duplicate entry to main
                $this->query("USE rehab_management_main_db");
                $main_db_form = array(
                    'admission_reference_id'         =>   $admission_id,
                    'user_id'                        =>   $this->clean($this->inputs['user_id']),
                    'rehab_center_id'                =>   $this->clean($this->inputs['rehab_center_id']),
                    'status'                         =>   'P',
                    'date_added'                     =>   $this->getCurrentDate()
                );
                $this->insert($this->table, $main_db_form);
            }

            return $admission_id;
        } catch (Exception $e) {
            $this->rollback();
            $this->response = "error";
            return $e->getMessage();
        }
    }

    public function edit()
    {
        try {
            $this->response = "success";

            $this->checker();
            $this->begin_transaction();

            $primary_id         = $this->clean($this->inputs[$this->pk]);
            $service_name       = $this->clean($this->inputs[$this->name]);
            $is_exist           = $this->select($this->table, $this->pk, "service_name = '$service_name' AND  $this->pk != '$primary_id'");

            if ($is_exist->num_rows > 0) {
                return -2;
            }

            $form = array(
                $this->name         => $this->clean($this->inputs[$this->name]),
                'service_fee'       => $this->clean($this->inputs['service_fee']),
                'service_desc'      => $this->clean($this->inputs['service_desc'])
            );
            $update_query = $this->update($this->table, $form, "$this->pk = '$primary_id'");

            if (!is_int($update_query))
                throw new Exception($update_query);

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
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $this->query("USE rehab_management_{$rehab_center_id}_db");
        $rows = array();
        $count = 1;
        $result = $this->select("$this->table a LEFT JOIN tbl_users u ON u.user_id=a.user_id", 'a.*, u.user_fname, u.user_mname, u.user_lname');
        while ($row = $result->fetch_assoc()) {
            $row['count'] = $count++;
            $row['user'] = $row['user_fname'] . " " . $row['user_mname'] . " " . $row['user_lname'];
            $rows[] = $row;
        }
        return $rows;
    }

    public function show_mobile()
    {
        $user_id = $this->clean($this->inputs['user_id']);
        $rows = array();
        $count = 1;
        $result = $this->select("$this->table a LEFT JOIN tbl_rehab_centers rc ON a.rehab_center_id=rc.rehab_center_id", '*', "a.user_id='$user_id'");
        while ($row = $result->fetch_assoc()) {
            $row['count'] = $count++;
            unset($row['rehab_center_complete_address']);
            unset($row['rehab_center_coordinates']);
            $rows[] = $row;
        }
        return $rows;
    }

    public function show_detail_mobile()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $admission_id = $this->clean($this->inputs['admission_id']);
        $this->query("USE rehab_management_{$rehab_center_id}_db");

        $fetch = $this->select($this->table, "*", "admission_id='$admission_id'");
        $row = $fetch->fetch_assoc();
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
        $row = $result->fetch_assoc();
        return $row[$self->name];
    }

    public static function total_admission()
    {
        $self = new self;
        $rehab_center_id = $self->clean($self->inputs['rehab_center_id']);
        $self->query("USE rehab_management_{$rehab_center_id}_db");

        $result = $self->select($self->table, "count(admission_id) as total");
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
