<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

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
    public $authRehabCenterId = 0;

    public function login()
    {
        try {
            $username = $this->clean($this->inputs['username']);
            $inputPassword = $this->inputs['password'];


            $result = $this->select("tbl_users AS u LEFT JOIN tbl_rehab_centers AS r ON r.rehab_center_id = u.rehab_center_id", "u.*", "u.username = '$username' AND user_category != 'U' LIMIT 1");

            if ($result->num_rows === 0) {
                return -1;
            }

            $user = $result->fetch_assoc();
            $user['user_category'] = $user['user_category'];
            if (!password_verify($inputPassword, $user['password'])) {
                return  -1;
            }

            unset($user['password']);

            return $user;
        } catch (Exception $e) {
            return -2;
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
                'birthdate' => $this->clean($this->inputs['birthdate']) ?? "0000-00-00",
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

    public function add_rehab_user()
    {
        try {
            $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
            $user_category   = $this->clean($this->inputs['user_category'] ?? 'R');

            $userData = [
                'user_fname'        => $this->clean($this->inputs['user_fname']),
                'user_mname'        => $this->clean($this->inputs['user_mname']),
                'user_lname'        => $this->clean($this->inputs['user_lname']),
                'username'          => $this->clean($this->inputs['username']),
                'permanent_address' => $this->clean($this->inputs['permanent_address']),
                'contact_number'    => $this->clean($this->inputs['contact_number']),
                'rehab_center_id'   => $rehab_center_id,
                'user_category'     => 'S',
                'password'          => password_hash($this->inputs['password'], PASSWORD_DEFAULT)
            ];

            $insertMain = $this->insert($this->table, $userData);

            if ($user_category === 'R' && $rehab_center_id) {
                $this->query("USE rehab_management_{$rehab_center_id}_db");
                $insertRehab = $this->insert('tbl_users', $userData);

                if ($insertMain && $insertRehab) {
                    return 1;
                } else {
                    return 0;
                }
            }

            return $insertMain ? 1 : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function update_rehab_user()
    {
        try {
            $user_id         = $this->clean($this->inputs['user_id']);
            $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
            // $user_category   = $this->clean($this->inputs['user_category'] ?? 'R');

            $userData = [
                'user_fname'        => $this->clean($this->inputs['user_fname']),
                'user_mname'        => $this->clean($this->inputs['user_mname']),
                'user_lname'        => $this->clean($this->inputs['user_lname']),
                'username'          => $this->clean($this->inputs['username']),
                'permanent_address' => $this->clean($this->inputs['permanent_address']),
                'rehab_center_desc' => $this->clean($this->inputs['rehab_center_desc']),
                'contact_number'    => $this->clean($this->inputs['contact_number']),
            ];

            $updateMain = $this->update($this->table, $userData, "$this->pk = '$user_id'");

            // if ($user_category === 'R' && $rehab_center_id) {
            $this->query("USE rehab_management_{$rehab_center_id}_db");
            $updateRehab = $this->update('tbl_users', $userData, "user_id = '$user_id'");

            if ($updateMain && $updateRehab) {
                return 1;
            } else {
                return 0;
            }
            // }

            return $updateMain ? 1 : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function update_profile()
    {
        try {
            $user_id = $this->clean($this->inputs['user_id']);
            $rehab_center_id = $this->clean($this->inputs['rehab_center_id'] ?? 0);
            $user_category = $this->clean($this->inputs['user_category'] ?? 'S');

            if ($user_category === 'S') {
                $form = [
                    'user_fname'        => $this->clean($this->inputs['user_fname']),
                    'user_mname'        => $this->clean($this->inputs['user_mname']),
                    'user_lname'        => $this->clean($this->inputs['user_lname']),
                    'username'          => $this->clean($this->inputs['username']),
                    'permanent_address' => $this->clean($this->inputs['permanent_address']),
                    'birthdate'         => $this->clean($this->inputs['birthdate']),
                    'birth_place'       => $this->clean($this->inputs['birth_place']),
                    'nationality'       => $this->clean($this->inputs['nationality']),
                    'religion'          => $this->clean($this->inputs['religion']),
                    'occupation'        => $this->clean($this->inputs['occupation']),
                    'employer'          => $this->clean($this->inputs['employer']),
                    'employer_address'  => $this->clean($this->inputs['employer_address']),
                    'father_name'       => $this->clean($this->inputs['father_name']),
                    'father_address'    => $this->clean($this->inputs['father_address']),
                    'mother_name'       => $this->clean($this->inputs['mother_name']),
                    'mother_address'    => $this->clean($this->inputs['mother_address']),
                ];
            } else {
                $form = [
                    'username' => $this->clean($this->inputs['username']),
                ];
            }

            $updateUserMain = $this->update($this->table, $form, "$this->pk = '$user_id'");

            $updateUserRehab = true;
            $updateRehabCenter = true;

            if ($user_category == 'R' && $rehab_center_id) {
                $this->query("USE rehab_management_{$rehab_center_id}_db");

                $userFormRehab = [
                    'username' => $this->clean($this->inputs['username']),
                ];
                $updateUserRehab = $this->update('tbl_users', $userFormRehab, "user_id = '$user_id'");

                $rehabForm = [
                    'rehab_center_name'             => $this->clean($this->inputs['rehab_center_name']),
                    'rehab_center_complete_address' => $this->clean($this->inputs['rehab_center_complete_address']),
                    'rehab_center_city'             => $this->clean($this->inputs['rehab_center_city']),
                    'hospital_code'                 => $this->clean($this->inputs['hospital_code']),
                ];
                $updateRehabCenter = $this->update('tbl_rehab_centers', $rehabForm, "rehab_center_id = '$rehab_center_id'");
            }

            return ($updateUserMain && $updateUserRehab && $updateRehabCenter) ? 1 : 0;
        } catch (Exception $e) {
            return 0; // error
        }
    }

    public function view_rehab()
    {
        $user_id = $this->clean($this->inputs['user_id']);
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);

        $this->query("USE rehab_management_{$rehab_center_id}_db");

        $rehabResult = $this->select("tbl_rehab_centers", "*", "rehab_center_id='$rehab_center_id'");
        if ($rehabResult->num_rows === 0) {
            return null;
        }
        $rehabRow = $rehabResult->fetch_assoc();

        $userResult = $this->select("tbl_users", "username, user_category", "user_id='$user_id' LIMIT 1");
        if ($userResult->num_rows === 0) {
            return null;
        }
        $userRow = $userResult->fetch_assoc();

        $data = array_merge($rehabRow, $userRow);
        unset($data['password']);

        return $data;
    }

    public function update_password()
    {
        try {
            $user_id = $this->clean($this->inputs['user_id']);
            $oldPassword = $this->inputs['oldPassword'];
            $newPassword = $this->inputs['newPassword'];
            $rehab_center_id = $this->clean($this->inputs['rehab_center_id'] ?? 0);
            $user_category = $this->clean($this->inputs['user_category'] ?? 'S');

            $mainUserResult = $this->select($this->table, '*', "$this->pk = '$user_id' LIMIT 1");
            if ($mainUserResult->num_rows === 0) {
                return -1;
            }

            $user = $mainUserResult->fetch_assoc();
            if (!password_verify($oldPassword, $user['password'])) {
                return -2;
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateMain = $this->update($this->table, ['password' => $hashedPassword], "$this->pk = '$user_id'");

            $updateRehab = true;
            if ($user_category === 'R' && $rehab_center_id) {
                $this->query("USE rehab_management_{$rehab_center_id}_db");
                $updateRehab = $this->update('tbl_users', ['password' => $hashedPassword], "user_id = '$user_id'");
            }

            return ($updateMain && $updateRehab) ? 1 : 0;
        } catch (Exception $e) {
            return 0;
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

        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $this->query("USE rehab_management_{$rehab_center_id}_db");

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

    public function register_mobile()
    {
        $this->inputs['user_category'] = 'U';
        return $this->add();
    }

    public function show()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $this->query("USE rehab_management_{$rehab_center_id}_db");
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

    public function request_password_reset()
    {
        try {

            $username = $this->clean($this->inputs['username']);

            $fetch = $this->select($this->table, "*", "username = '$username' LIMIT 1");
            if ($fetch->num_rows === 0) {
                return -1;
            }

            $row = $fetch->fetch_assoc();

            $email = $row['email'];


            $otp = random_int(100000, 999999);

            $mail = new PHPMailer(true);

            // SMTP Settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'berehabmanagement.info@gmail.com';
            $mail->Password   = 'wwxd idsz vpix prok';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // Recipients
            $mail->setFrom('berehabmanagement.info@gmail.com', 'Blockchain-Enhanced Rehabilitation Manager');
            $mail->addAddress($email);

            $fullName = $row['user_fname'] . ' ' . $row['user_mname'] . ' ' . $row['user_lname'];

            $this->update($this->table, ['otp_code' => $otp], "username = '$username'");

            // Email Content (Modern UI)
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code | Blockchain-Enhanced Rehabilitation Manager';

            $mail->Body = "
        <div style='max-width:600px;margin:auto;font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;padding:30px'>
            <div style='background:#ffffff;border-radius:10px;padding:30px;box-shadow:0 4px 10px rgba(0,0,0,0.08)'>
                
                <h2 style='color:#1f2937;text-align:center;margin-bottom:10px'>
                    Blockchain-Enhanced Rehabilitation Manager
                </h2>

                <p style='font-size:14px;color:#374151'>Hi <b>{$fullName}</b>,</p>

                <p style='font-size:14px;color:#374151;line-height:1.6'>
                    We received a request to reset your password. Please use the One-Time Password (OTP) below to continue.
                </p>

                <div style='text-align:center;margin:25px 0'>
                    <span style='display:inline-block;
                        background:#1f2937;
                        color:#ffffff;
                        font-size:28px;
                        letter-spacing:5px;
                        padding:12px 25px;
                        border-radius:8px;
                        font-weight:bold;'>
                        {$otp}
                    </span>
                </div>

                <p style='font-size:13px;color:#6b7280;text-align:center'>
                    This OTP is valid for a limited time. Do not share this code with anyone.
                </p>

                <hr style='border:none;border-top:1px solid #e5e7eb;margin:25px 0'>

                <p style='font-size:12px;color:#6b7280;text-align:center'>
                    If you did not request a password reset, you can safely ignore this email.
                </p>

                <p style='font-size:12px;color:#9ca3af;text-align:center;margin-top:20px'>
                    © " . date('Y') . " Blockchain-Enhanced Rehabilitation Manager
                </p>

            </div>
        </div>
        ";

            $mail->AltBody = "Hi {$fullName},\n\nYour OTP code is: {$otp}\n\nThis code is valid for a limited time.\n\nBlockchain-Enhanced Rehabilitation Manager";

            $mail->send();


            return $otp;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function reset_password_otp()
    {
        try {
            $username    = $this->clean($this->inputs['username']);
            $otp         = $this->inputs['otp_code'];
            $newPassword = trim($this->inputs['password']);

            $fetch = $this->select($this->table, "*", "username = '$username' LIMIT 1");
            if ($fetch->num_rows === 0) {
                return -1;
            }

            $user = $fetch->fetch_assoc();

            if ($user['otp_code'] != $otp) {
                return -2;
            }

            $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);

            $update = $this->update(
                $this->table,
                ['password' => $hashed_password, 'otp_code' => ''],
                "username = '$username'"
            );

            return $update ? 1 : 0;
        } catch (Exception $e) {
            return 0;
        }
    }
}
