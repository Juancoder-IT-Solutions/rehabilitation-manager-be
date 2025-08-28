<?php
class AdmissionServices extends Connection
{
    private $table = 'tbl_admission_services';
    public $pk = 'admission_service_id';
    public $module_name = "Admission Services";

    public $inputs = [];
    public $response = "";

    public $searchable = [];
    public $uri = "AdmissionServices";

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
        $result = $this->select("$this->table a LEFT JOIN tbl_users u ON u.user_id=a.user_id LEFT JOIN tbl_services s ON s.service_id=a.service_id", 'a.*, u.user_fname, u.user_mname, u.user_lname, s.service_name', $param);
        while ($row = $result->fetch_assoc()) {
            $row['count'] = $count++;
            $row['user'] = $row['user_fname']." ".$row['user_mname']." ".$row['user_lname'];
            $rows[] = $row;
        }
        return $rows;
    }

    public function show_mobile()
    {
        $user_id = $this->clean($this->inputs['user_id']);
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $admission_id = $this->clean($this->inputs['admission_id']);

        $this->query("USE rehab_management_{$rehab_center_id}_db");
        
        $rows = array();
        $result = $this->select("$this->table h LEFT JOIN tbl_services s ON h.service_id=s.service_id LEFT JOIN tbl_services_stages ss ON s.service_id=ss.service_id LEFT JOIN tbl_service_stages_task sst ON ss.stage_id=sst.stage_id", 's.*, h.admission_service_id, COUNT(ss.stage_id) AS stages_count, COUNT(sst.task_id) AS task_count', "h.admission_id='$admission_id' GROUP BY s.service_id");
        while ($row = $result->fetch_assoc()) {
            // finished tasks
            $fetch_finished_tasks = $this->select("tbl_admission_tasks", "count(admission_task_id) as count", "admission_service_id='$row[admission_service_id]'");
            $finish_task_row = $fetch_finished_tasks->fetch_assoc();

            $row['finish_task_count'] = $finish_task_row['count'];
            $row['progress_perc'] = $row['task_count'] > 0 ? number_format(($finish_task_row['count']/$row['task_count']) * 100, 2) : 0;
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
