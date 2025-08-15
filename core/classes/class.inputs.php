<?php

class Inputs extends Connection
{
    private $table = 'tbl_inputs';
    private $pk = 'input_id';
    private $name = 'input_label';

    public $module = "Inputs";
    public $module_name = "Inputs";
    public $inputs = [];
    public $uri = "Inputs";
    public $response = "";

    public $authUserId = 0;

    public function add()
    {
        try {
            $this->response = "success";

            $this->checker();
            $this->begin_transaction();

            $input_label = $this->clean($this->inputs['input_label']);
            $is_exist = $this->select($this->table, $this->pk, "input_label = '$input_label'");

            if (!is_object($is_exist))
                throw new Exception($is_exist);

            if ($is_exist->num_rows > 0) {
                return -2;
            }

            $input_type = $this->clean($this->inputs['input_type']);
            if ($input_type === '' || $input_type === null) {
                $input_type = 'text';
            }

            $input_require = $this->clean($this->inputs['input_require']);
            if ($input_require === '' || $input_require === null) {
                $input_require = 1; // default required
            }

            $form = array(
                'input_label'   => $input_label,
                'input_type'    => $input_type,
                'input_require' => $input_require,
            );

            $insert_query = $this->insert($this->table, $form, "Y");
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
            $input_label       = $this->clean($this->inputs[$this->name]);
            $is_exist           = $this->select($this->table, $this->pk, "input_label = '$input_label' AND  $this->pk != '$primary_id'");

            if ($is_exist->num_rows > 0) {
                return -2;
            }



            $form = array(
                'input_label'      => $input_label,
                'input_type'       => $this->clean($this->inputs['input_type']),
                'input_require'    => $this->clean($this->inputs['input_require']),
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
    
    public function show_options()
    {
        $input_id = $this->clean($this->inputs['input_id']);
        $rows = array();
        $count = 1;
        $result = $this->select('tbl_input_options', '*', "input_id = '$input_id'");
        while ($row = $result->fetch_assoc()) {
            $row['count'] = $count++;
            $rows[] = $row;
        }
        return $rows;
    }

    public function remove_option()
    {
        $stage_id = $this->clean($this->inputs['id']);
        return $this->delete('tbl_input_options', "input_id = '$stage_id'");
    }


    public static function name($primary_id)
    {
        $self = new self;
        $result = $self->select($self->table, $self->name, "$self->pk  = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row[$self->name];
    }

    public function remove()
    {
        $ids = implode(",", $this->inputs['ids']);
        return $this->delete($this->table, "$this->pk IN ($ids)");
    }

    public function add_option()
    {
        try {
            $this->response = "success";
            $this->checker();
            $this->begin_transaction();

            $input_option_label = $this->clean($this->inputs['input_option_label']);
            $input_id = $this->clean($this->inputs['input_id']);
            $is_exist = $this->select('tbl_input_options', 'input_option_id', "input_option_label = '$input_option_label' AND input_id = '$input_id'");

            if (!is_object($is_exist))
                throw new Exception($is_exist);

            if ($is_exist->num_rows > 0) {
                return -2;
            }

            $form = array(
                'input_option_label'   => $input_option_label,
                'input_id'             => $input_id
            );

            $insert_query = $this->insert('tbl_input_options', $form, "Y");
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
    
    public function update_option()
    {
        try {
            $this->response = "success";
            $this->checker();
            $this->begin_transaction();

            $input_option_id = $this->clean($this->inputs['input_option_id']);
            $input_option_label = $this->clean($this->inputs['input_option_label']);

            $existing = $this->select('tbl_input_options', 'input_id', "input_option_id = '$input_option_id'");
            if (!is_object($existing))
                throw new Exception($existing);

            if ($existing->num_rows === 0)
                throw new Exception("Option not found.");

            $row = $existing->fetch_assoc();
            $input_id = $row['input_id'];

            $is_exist = $this->select(
                'tbl_input_options',
                'input_option_id',
                "input_option_label = '$input_option_label' AND input_id = '$input_id' AND input_option_id != '$input_option_id'"
            );

            if (!is_object($is_exist))
                throw new Exception($is_exist);

            if ($is_exist->num_rows > 0) {
                return -2; // duplicate
            }

            // Update query
            $form = array(
                'input_option_label' => $input_option_label
            );

            $update_query = $this->update('tbl_input_options', $form, "input_option_id = '$input_option_id'");
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
}
