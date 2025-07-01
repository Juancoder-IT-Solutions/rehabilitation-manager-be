<?php

class Users extends Connection
{
    private $table = 'tbl_users';
    private $pk = 'user_id';
    private $name = 'username';

    public $module = "Users";
    public $module_name = "Users";
    public $inputs = [];
    public $uri = "users";
    public $response = "";

    public $authUserId = 0;

    public function login()
    {
        try {
            $this->response = "success";
            $this->checker();

            $username = $this->clean($this->inputs['username']);
            $inputPassword = $this->inputs['password'];

            $result = $this->select("tbl_users AS u LEFT JOIN tbl_rehab_centers AS r ON r.rehab_center_id = u.rehab_center_id", "u.user_id,  u.username, u.password, u.rehab_center_id, r.rehab_center_name, r.rehab_center_city, r.rehab_center_complete_address, r.rehab_center_coordinates", "u.username = '$username' LIMIT 1");

            if ($result->num_rows === 0) {
                return 0; 
            }

            $user = $result->fetch_assoc();
            if (!password_verify($inputPassword, $user['password'])) {
                return 0; 
            }

            unset($user['password']);

            return $user;
        } catch (Exception $e) {
            $this->response = "error";
            return $e->getMessage();
        }
    }



    public function add()
    {
        try {
            $this->response = "success";

            $this->checker();
            $this->begin_transaction();

            $username = $this->clean($this->inputs['username']);
            $is_exist = $this->select($this->table, $this->pk, "username = '$username'");

            if (!is_object($is_exist))
                throw new Exception($is_exist);

            if ($is_exist->num_rows > 0) {
                return 2;
            }

            $form = array(
                'user_fullname'     => $this->clean($this->inputs['user_fullname']),
                'user_category'     => '',
                'username'          => $username,
                'password'          => md5($this->inputs['password'])
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


    public static function name($primary_id)
    {
        $self = new self;
        $result = $self->select($self->table, $self->name, "$self->pk  = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row[$self->name];
    }
}
