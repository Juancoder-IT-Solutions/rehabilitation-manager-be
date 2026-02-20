<?php
class Services extends Connection
{
    private $table = 'tbl_services';
    public $pk = 'service_id';
    public $name = 'service_name';
    public $module_name = "Services";
    private $dbname = DBNAME;

    public $inputs = [];
    public $response = "";

    public $searchable = ['service_name'];
    public $uri = "Services";

    public $authUserId = 0;
    public $authRehabCenterId = 0;

    public function add()
    {
        try {
            $this->response = "success";


            $this->checker();
            $this->begin_transaction();
            $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
            $this->query("USE rehab_management_{$rehab_center_id}_db");

            $service_name = $this->clean($this->inputs[$this->name]);
            $is_exist = $this->select($this->table, $this->pk, "service_name = '$service_name'");

            if (!is_object($is_exist))
                throw new Exception($is_exist);

            if ($is_exist->num_rows > 0) {
                return -2;
            }

            $form = array(
                $this->name         => $this->clean($this->inputs[$this->name]),
                'service_fee'       => $this->clean($this->inputs['service_fee']),
                'service_desc'      => $this->clean($this->inputs['service_desc']),
                'rehab_center_id'   => $rehab_center_id
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

    public function add_stages()
    {
        try {
            $this->response = "success";

            $this->checker();
            $this->begin_transaction();

            $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
            $this->query("USE rehab_management_{$rehab_center_id}_db");

            $stage_name = $this->clean($this->inputs['stage_name']);
            $is_exist = $this->select('tbl_services_stages', 'stage_id', "stage_name = '$stage_name' AND service_id = '{$this->inputs['service_id']}'");

            if (!is_object($is_exist))
                throw new Exception($is_exist);

            if ($is_exist->num_rows > 0) {
                return -2;
            }

            $form = array(
                'stage_name'         => $this->clean($this->inputs['stage_name']),
                'service_id'         => $this->clean($this->inputs['service_id']),
            );
            $insert_query = $this->insert('tbl_services_stages', $form);
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

    public function update_stages()
    {
        try {
            $this->response = "success";

            $this->checker();
            $this->begin_transaction();
            $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
            $this->query("USE rehab_management_{$rehab_center_id}_db");


            $stage_id = $this->clean($this->inputs['stage_id']);
            $stage_name = $this->clean($this->inputs['stage_name']);
            $service_id = $this->clean($this->inputs['service_id']);

            $is_exist = $this->select(
                'tbl_services_stages',
                'stage_id',
                "stage_name = '$stage_name' AND service_id = '$service_id' AND stage_id != '$stage_id'"
            );

            if (!is_object($is_exist)) {
                throw new Exception($is_exist);
            }

            if ($is_exist->num_rows > 0) {
                return -2; // already exists
            }

            $form = array(
                'stage_name' => $stage_name,
                // 'service_id' => $service_id
            );

            $update_query = $this->update(
                'tbl_services_stages',
                $form,
                "stage_id = '$stage_id'"
            );

            if (!is_int($update_query)) {
                throw new Exception($update_query);
            }

            $this->commit();
            return 1; // success
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
            $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
            $this->query("USE rehab_management_{$rehab_center_id}_db");


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

    public function add_task()
    {
        try {
            $this->response = "success";

            $this->checker();
            $this->begin_transaction();
            $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
            $this->query("USE rehab_management_{$rehab_center_id}_db");


            $task_name = $this->clean($this->inputs['task_name']);
            $is_exist = $this->select('tbl_service_stages_task', 'stage_id', "task_name = '$task_name' AND stage_id = '{$this->inputs['stage_id']}'");

            if (!is_object($is_exist))
                throw new Exception($is_exist);

            if ($is_exist->num_rows > 0) {
                return -2;
            }

            $form = array(
                'task_name'       => $this->clean($this->inputs['task_name']),
                'task_desc'       => $this->clean($this->inputs['task_desc']),
                'stage_id'        => $this->clean($this->inputs['stage_id']),
                'service_id'      => $this->clean($this->inputs['service_id']),
            );
            $insert_query = $this->insert('tbl_service_stages_task', $form);
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

    public function update_task()
    {
        try {
            $this->response = "success";

            $this->checker();
            $this->begin_transaction();
            $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
            $this->query("USE rehab_management_{$rehab_center_id}_db");

            $task_id = $this->clean($this->inputs['task_id']);
            $task_name = $this->clean($this->inputs['task_name']);

            $is_exist = $this->select('tbl_service_stages_task', 'task_id', "task_name = '$task_name' AND task_id != '$task_id'");

            if (!is_object($is_exist)) {
                throw new Exception($is_exist);
            }

            if ($is_exist->num_rows > 0) {
                return -2; // already exists
            }

            $form = array(
                'task_name'     => $task_name,
                'task_desc'     => $this->clean($this->inputs['task_desc'])
            );

            $update_query = $this->update('tbl_service_stages_task', $form, "task_id = '$task_id'");

            if (!is_int($update_query)) {
                throw new Exception($update_query);
            }

            $this->commit();
            return 1; // success
        } catch (Exception $e) {
            $this->rollback();
            $this->response = "error";
            return $e->getMessage();
        }
    }


    public function show()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $this->query("USE rehab_management_{$rehab_center_id}_db");
        // $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $rows = array();
        $count = 1;
        $result = $this->select($this->table, '*');
        while ($row = $result->fetch_assoc()) {
            $row['count'] = $count++;
            $rows[] = $row;
        }
        return $rows;
    }

    public function show_stages()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $this->query("USE rehab_management_{$rehab_center_id}_db");

        $service_id = $this->clean($this->inputs['service_id']);
        $rows = array();
        $count = 1;
        $result = $this->select('tbl_services_stages', '*', 'service_id = ' . $service_id);
        while ($row = $result->fetch_assoc()) {
            $row['count'] = $count++;
            $rows[] = $row;
        }
        return $rows;
    }

    public function show_stages_mobile()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $this->query("USE rehab_management_{$rehab_center_id}_db");

        $service_id = $this->clean($this->inputs['service_id']);
        $rows = array();
        $count = 1;

        $result = $this->select('tbl_services_stages ss LEFT JOIN tbl_service_stages_task st ON ss.stage_id=st.stage_id', 'ss.*, COUNT(st.task_id) AS task_count', "ss.service_id='$service_id' GROUP BY ss.stage_id");
        while ($row = $result->fetch_assoc()) {
            $row['count'] = $count++;
            $rows[] = $row;
        }
        return $rows;
    }

    public function show_stages_progress_mobile()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $this->query("USE rehab_management_{$rehab_center_id}_db");

        $service_id = $this->clean($this->inputs['service_id']);
        $admission_reference_id = $this->clean($this->inputs['admission_reference_id']);
        $rows = array();
        $count = 1;

        $result = $this->select('tbl_services_stages', '*', "service_id='$service_id'");
        while ($row = $result->fetch_assoc()) {
            $task_rows = array();
            $fetch_tasks = $this->select("tbl_service_stages_task sst LEFT JOIN tbl_admission_tasks adt ON sst.task_id=adt.task_id", "sst.*, admission_task_id", "sst.stage_id='$row[stage_id]'");
            while($task_row = $fetch_tasks->fetch_assoc()){
                // check if has task
                $task_row['is_done'] = $task_row['admission_task_id'] > 0 ? 1 : 0;
                $task_rows[] = $task_row;
            }

            $row['tasks_row'] = $task_rows;
            $rows[] = $row;
        }
        return $rows;
    }

    public function show_task()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $this->query("USE rehab_management_{$rehab_center_id}_db");

        $stage_id = $this->clean($this->inputs['stage_id']);
        $rows = array();
        $count = 1;
        $result = $this->select('tbl_service_stages_task', '*', 'stage_id = ' . $stage_id);
        while ($row = $result->fetch_assoc()) {
            $row['count'] = $count++;
            $rows[] = $row;
        }
        return $rows;
    }


    public function remove()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $this->query("USE rehab_management_{$rehab_center_id}_db");

        $ids = implode(",", $this->inputs['ids']);
        return $this->delete($this->table, "$this->pk IN ($ids)");
    }

    public function remove_stages()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $this->query("USE rehab_management_{$rehab_center_id}_db");

        $stage_id = $this->clean($this->inputs['id']);
        return $this->delete('tbl_services_stages', "stage_id = '$stage_id'");
    }

    public function delete_task()
    {

        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $this->query("USE rehab_management_{$rehab_center_id}_db");

        $task_id = $this->clean($this->inputs['id']);
        return $this->delete('tbl_service_stages_task', "task_id = '$task_id'");
    }

    public static function name($primary_id)
    {
        $self = new self;
        $result = $self->select($self->table, $self->name, "$self->pk  = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row[$self->name];
    }


    public function total_services()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $this->query("USE rehab_management_{$rehab_center_id}_db");
        
        $result = $this->select($this->table, "count(*) as total");
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
