<?php
class Appointments extends Connection
{
    private $table = 'tbl_appointments';
    public $pk = 'appointment_id';
    public $module_name = "Appointments";

    public $inputs = [];
    public $response = "";

    public $searchable = [];
    public $uri = "Appointments";

    public $authUserId = 0;
    public $authRehabCenterId = 0;

    public function add()
    {
        try {
            $this->response = "success";

            $this->checker();
            $this->begin_transaction();

            $admission_id                   =   $this->clean($this->inputs['admission_id']);
            $rehab_center_id                =   $this->clean($this->inputs['rehab_center_id']);
            $appointment_date               =   $this->clean($this->inputs['appointment_date']);

            $this->query("USE rehab_management_{$rehab_center_id}_db");

            $is_exist = $this->select($this->table, $this->pk, "admission_id = '$admission_id'");

            if (!is_object($is_exist))
                throw new Exception($is_exist);

            if ($is_exist->num_rows > 0) {
                return -2;
            }

            $form = array(
                'admission_id'                   =>   $this->clean($this->inputs['admission_id']),
                'rehab_center_id'                =>   $this->clean($this->inputs['rehab_center_id']),
                'appointment_date'               =>   $this->clean($this->inputs['appointment_date']),
                'remarks'                        =>   $this->clean($this->inputs['remarks']),
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
        $rows = array();
        $count = 1;
        $result = $this->select($this->table, '*', $param);
        while ($row = $result->fetch_assoc()) {
            $row['count'] = $count++;
            $rows[] = $row;
        }
        return $rows;
    }

    public function remove()
    {
        $ids = implode(",", $this->inputs['ids']);
        return $this->delete($this->table, "$this->pk IN ($ids)");
    }
}
