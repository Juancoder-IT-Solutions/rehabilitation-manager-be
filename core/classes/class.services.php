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
                'rehab_center_id' => $this->authRehabCenterId
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
                'service_id' => $service_id
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
                'stage_id'         => $this->clean($this->inputs['stage_id']),
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

            $task_id = $this->clean($this->inputs['task_id']);
            $task_name = $this->clean($this->inputs['task_name']);

            $is_exist = $this->select('tbl_service_stages_task','task_id',"task_name = '$task_name' AND task_id != '$task_id'");

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

            $update_query = $this->update('tbl_service_stages_task',$form,"task_id = '$task_id'");

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

    public function show_stages()
    {
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

    public function show_task()
    {
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
        $ids = implode(",", $this->inputs['ids']);
        return $this->delete($this->table, "$this->pk IN ($ids)");
    }

    public function remove_stages()
    {
        $stage_id = $this->clean($this->inputs['id']);
        return $this->delete('tbl_services_stages', "stage_id = '$stage_id'");
    }

    public function delete_task()
    {
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

    
    public static function total_services()
    {
        $self = new self;
        $result = $self->select($self->table, "count(service_id) as total");
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
