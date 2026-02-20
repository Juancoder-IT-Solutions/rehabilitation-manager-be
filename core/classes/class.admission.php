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

    public function add_service_admission()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $this->query("USE rehab_management_{$rehab_center_id}_db");

        $service_id = $this->clean($this->inputs['service_id']);
        $admission_id = $this->clean($this->inputs['admission_id']);
        $is_exist = $this->select('tbl_admission_services', '*', "service_id = '$service_id' AND admission_id = '{$this->inputs['admission_id']}'");

        if ($is_exist->num_rows > 0) {
            return -1;
        }

        $form = array(
            'admission_id'        => $this->clean($this->inputs['admission_id']),
            'service_id'          => $this->clean($this->inputs['service_id']),
        );
        $insert_query = $this->insert('tbl_admission_services', $form, 'Y');

        if ($insert_query)
            $this->update($this->table, ['status' => 'O'], "admission_id='$admission_id'");
        $fetch_stages = $this->select("tbl_service_stages_task", "*", "service_id='$service_id'");
        while ($sRow = $fetch_stages->fetch_assoc()) {
            $form_task = array(
                'admission_id'         => $this->clean($this->inputs['admission_id']),
                'admission_service_id' => $insert_query,
                'stage_id'             => $sRow['stage_id'],
                'task_id'              => $sRow['task_id']
            );
            $this->insert('tbl_admission_tasks', $form_task);
        }
        return 1;
    }

    public function delete_service()
    {
        try {
            $admission_service_id = $this->clean($this->inputs['admission_service_id']);
            $rehab_center_id      = $this->clean($this->inputs['rehab_center_id']);

            if (empty($admission_service_id) || empty($rehab_center_id)) {
                throw new Exception("Missing parameters.");
            }

            $this->query("USE rehab_management_{$rehab_center_id}_db");
            $this->begin_transaction();

            $delete_tasks = $this->delete('tbl_admission_tasks', "admission_service_id='$admission_service_id'");
            if ($delete_tasks === false) {
                throw new Exception("Failed to delete associated tasks.");
            }

            $delete_service = $this->delete('tbl_admission_services', "admission_service_id='$admission_service_id'");
            if ($delete_service === false) {
                throw new Exception("Failed to delete the service.");
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

    public function get_services_avail()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $admission_id = $this->clean($this->inputs['admission_id']);
        $this->query("USE rehab_management_{$rehab_center_id}_db");
        $fetch = $this->select('tbl_admission_services a LEFT JOIN tbl_services s ON s.service_id=a.service_id', "*", "a.admission_id='$admission_id'");
        while ($row = $fetch->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function fetch_admission_tasks()
    {
        $rehab_center_id     = $this->clean($this->inputs['rehab_center_id']);
        $admission_id        = $this->clean($this->inputs['admission_id']);
        $admission_service_id = $this->clean($this->inputs['admission_service_id']);

        $this->query("USE rehab_management_{$rehab_center_id}_db");

        $fetch = $this->select(
            "tbl_admission_tasks at
         LEFT JOIN tbl_services_stages ss 
            ON ss.stage_id = at.stage_id
         LEFT JOIN tbl_service_stages_task sst 
            ON sst.task_id = at.task_id 
           AND sst.stage_id = at.stage_id",
            "at.*, ss.stage_name, sst.task_name, sst.task_desc",
            "at.admission_id = '$admission_id' 
         AND at.admission_service_id = '$admission_service_id'"
        );

        $rows = [];
        while ($row = $fetch->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function update_admission_tasks()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $admission_id = $this->clean($this->inputs['admission_id']);
        $admission_service_id = $this->clean($this->inputs['admission_service_id']);
        $checkedTasks = $this->inputs['checkedTasks'];

        $this->query("USE rehab_management_{$rehab_center_id}_db");

        $ids = implode(",", array_map('intval', $checkedTasks));

        $this->update("tbl_admission_tasks", ['status' => 0], "admission_id = '$admission_id' AND admission_service_id='$admission_service_id'");

        if (!empty($ids)) {
            $this->update("tbl_admission_tasks", ['status' => 1],  "admission_id = '$admission_id' AND admission_service_id='$admission_service_id' AND admission_task_id IN ($ids)");
        }

        return 1;
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

    public function total_admission()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $this->query("USE rehab_management_{$rehab_center_id}_db");

        $result = $this->select($this->table, "count(admission_id) as total");
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function show_admission_history()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $user_id   = $this->clean($this->inputs['user_id']);

        $this->query("USE rehab_management_{$rehab_center_id}_db");

        $rows = [];

        $admission = $this->select(
            "tbl_admission",
            "*",
            "user_id = '$user_id'"
        );

        if ($admission && $admission->num_rows > 0) {
            $row = $admission->fetch_assoc();
            $stat = $row['status'] == "A" ? "(Approved)" : "(Pending)";
            $rows[] = [
                'action'      => 'Admission Created',
                'description' => 'Patient admission was created ' . $stat,
                'created_at'  => $row['date_added']
            ];
        }

        $services = $this->select(
            "tbl_admission_services ad 
         LEFT JOIN tbl_services s ON s.service_id = ad.service_id LEFT JOIN tbl_admission a ON a.admission_id=ad.admission_id",
            "ad.admission_service_id, s.service_name, ad.date_added",
            "a.user_id = '$user_id'"
        );

        while ($sRow = $services->fetch_assoc()) {
            $rows[] = [
                'action'      => 'Service Added',
                'description' => 'Service added: ' . $sRow['service_name'],
                'created_at'  => $sRow['date_added']
            ];
        }

        usort($rows, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $rows;
    }

    public function show_admission_history_dashboard()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $this->query("USE rehab_management_{$rehab_center_id}_db");

        $result = [
            'total_admissions' => 0,
            'active_admissions' => 0,
            'completed_admissions' => 0,
            'monthly_admissions' => [], 
            'total_services' => 0,
            'service_utilization' => 0
        ];

        // Fetch all admissions
        $admissions = $this->select("tbl_admission", "*", "status != 'P' AND status != ''");

        if ($admissions && $admissions->num_rows > 0) {
            $allAdmissions = [];
            while ($row = $admissions->fetch_assoc()) {
                $allAdmissions[] = $row;
                $result['total_admissions']++;

                if ($row['status'] === "F") {
                    $result['completed_admissions']++;
                } else {
                    $result['active_admissions']++;
                }

                // Group by month
                $month = date('Y-m', strtotime($row['date_added']));
                if (!isset($result['monthly_admissions'][$month])) {
                    $result['monthly_admissions'][$month] = 0;
                }
                $result['monthly_admissions'][$month]++;
            }
        }

        // Fetch total services
        $services = $this->select("tbl_services", "*");
        if ($services && $services->num_rows > 0) {
            $result['total_services'] = $services->num_rows;

            // Service utilization: admissions / total services * 100
            $result['service_utilization'] = $result['total_services']
                ? round(($result['total_admissions'] / $result['total_services']) * 100)
                : 0;
        }

        return $result;
    }

    public function finish_admission()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
        $admission_id = $this->clean($this->inputs['admission_id']);

        $this->query("USE rehab_management_{$rehab_center_id}_db");

        $update = $this->update(
            $this->table,
            [
                'status' => 'F',
                'end_date' => $this->getCurrentDate()
            ],
            "admission_id = '$admission_id'"
        );

        if (!is_int($update)) {
            return $update;
        }

        // update main DB
        $this->query("USE rehab_management_main_db");
        $this->update(
            $this->table,
            [
                'status'   => 'F',
                'end_date' => $this->getCurrentDate()
            ],
            "admission_reference_id = '$admission_id' AND rehab_center_id = '$rehab_center_id'"
        );

        return 1;
    }

    public function approve()
    {
        try {
            $admission_ids = $this->inputs['admission_ids'];
            $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
            $start_date = $this->clean($this->inputs['start_date']);

            if (empty($admission_ids) || empty($start_date)) {
                throw new Exception("Admission IDs and start date are required.");
            }

            $this->query("USE rehab_management_{$rehab_center_id}_db");
            $this->begin_transaction();

            $ids = implode(",", array_map('intval', $admission_ids));

            $update = $this->update(
                $this->table,
                [
                    'status' => 'A',
                    'start_date' => $start_date
                ],
                "admission_id IN ($ids)"
            );

            if (!is_int($update)) {
                throw new Exception($update);
            }

            // Update main DB
            $this->query("USE rehab_management_main_db");
            $this->update(
                $this->table,
                [
                    'status'   => 'F',
                    'start_date' => $start_date
                ],
                "admission_id IN ($ids) AND rehab_center_id = '$rehab_center_id'"
            );

            $this->commit();
            return 1;
        } catch (Exception $e) {
            $this->rollback();
            $this->response = "error";
            return $e->getMessage();
        }
    }

    public function admission_trends()
    {
        $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);

        // Switch to the specific rehab center DB
        $this->query("USE rehab_management_{$rehab_center_id}_db");

        // Fetch admissions count per day for last 7 days
        $result = $this->query("
        SELECT DATE(date_added) as date, COUNT(*) as admissions
        FROM {$this->table}
        WHERE date_added >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(date_added)
        ORDER BY DATE(date_added) ASC
    ");

        $trends = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $trends[$date] = 0;
        }

        while ($row = $result->fetch_assoc()) {
            $trends[$row['date']] = (int)$row['admissions'];
        }

        $formatted = [];
        foreach ($trends as $date => $count) {
            $formatted[] = [
                'date' => $date,
                'admissions' => $count
            ];
        }

        return $formatted;
    }
}
