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
                return -2;
            }

            $password = $this->clean($this->inputs['password']);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $form = array(
                'user_fname' => $this->clean($this->inputs['user_fname']),
                'user_lname' => $this->clean($this->inputs['user_lname']),
                'user_category' => $this->clean($this->inputs['user_category']),
                'birthdate' => $this->clean($this->inputs['birthdate']),
                'username'      => $username,
                'password'      => $hashed_password
            );

            $insert_query = $this->insert($this->table, $form, "Y");
            if (!is_int($insert_query))
                throw new Exception($insert_query);

            $this->commit();
            return $insert_query;
        } catch (Exception $e) {
            $this->rollback();
            $this->response = "error";
            return $e->getMessage();
        }
    }

    public function get_user()
    {
        $primary_id = $this->clean($this->inputs['id']);
        $result = $this->select($this->table, "*", "$this->pk  = '$primary_id'");
        $row = $result->fetch_assoc();
        unset($row['password']);
        return $row;
    }

    public function update_user()
    {
        $user_id = $this->clean($this->inputs['user_id']);

        $form = array(
            'user_fname'         => $this->clean($this->inputs['user_fname']),
            'user_mname'         => $this->clean($this->inputs['user_mname']),
            'user_lname'         => $this->clean($this->inputs['user_lname']),
            'permanent_address'  => $this->clean($this->inputs['permanent_address']),
            'birthdate'          => $this->clean($this->inputs['birthdate']),
            'birth_place'        => $this->clean($this->inputs['birth_place']),
            'nationality'        => $this->clean($this->inputs['nationality']),
            'religion'           => $this->clean($this->inputs['religion']),
            'occupation'         => $this->clean($this->inputs['occupation']),
            'employer'           => $this->clean($this->inputs['employer']),
            'employer_address'   => $this->clean($this->inputs['employer_address']),
            'father_name'        => $this->clean($this->inputs['father_name']),
            'father_address'     => $this->clean($this->inputs['father_address']),
            'mother_name'        => $this->clean($this->inputs['mother_name']),
            'mother_address'     => $this->clean($this->inputs['mother_address'])
        );
        
        $result = $this->update($this->table, $form, "$this->pk  = '$user_id'");
        return $result;
    }

    public static function name($primary_id)
    {
        $self = new self;
        $result = $self->select($self->table, $self->name, "$self->pk  = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row[$self->name];
    }

    public function login_mobile()
    {
        $username = $this->clean($this->inputs['username']);
        $inputPassword = $this->clean($this->inputs['password']);

        $result = $this->select($this->table, "*", "username = '$username' LIMIT 1");

        if ($result->num_rows === 0) {
            return 0;
        }

        $user = $result->fetch_assoc();
        if (!password_verify($inputPassword, $user['password'])) {
            return 0;
        }

        unset($user['password']);

        return $user;
    }

    public function register_mobile(){
        $this->inputs['user_category'] = 'U';
        return $this->add();
    }
}
