<?php
class Payments extends Connection
{
    private $table = 'tbl_payments';
    public $pk = 'payment_id';
    public $module_name = "Payments";

    public $inputs = [];
    public $response = "";

    public $searchable = [];
    public $uri = "Payments";

    public $authUserId = 0;
    public $authRehabCenterId = 0;

    private $PAYMONGO_SECRET_KEY = "sk_test_tNwv1cm2rhrE6pUgRE52KxNR";
    private $PAYMONGO_PUBLIC_KEY = "pk_test_RsSJcBqjbQUnNsUHXW2gK6oy";

    public function add()
    {
        try {
            $this->response = "success";

            $this->checker();
            $this->begin_transaction();

            $admission_id = $this->clean($this->inputs['admission_id']);
            $rehab_center_id = $this->clean($this->inputs['rehab_center_id']);
            $reference_number = $this->clean($this->inputs['reference_number']);
            $user_id = $this->clean($this->inputs['user_id']);

            $this->query("USE rehab_management_{$rehab_center_id}_db");

            $is_exist = $this->select($this->table, $this->pk, "reference_number = '$reference_number'");

            if (!is_object($is_exist))
                throw new Exception($is_exist);

            if ($is_exist->num_rows > 0) {
                return -2;
            }

            $form = array(
                'admission_id' => $admission_id,
                'user_id' => $user_id,
                'reference_number' => $reference_number,
                'payment_date' => $this->getCurrentDate()
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

            $primary_id = $this->clean($this->inputs[$this->pk]);
            $service_name = $this->clean($this->inputs[$this->name]);
            $is_exist = $this->select($this->table, $this->pk, "service_name = '$service_name' AND $this->pk != '$primary_id'");

            if ($is_exist->num_rows > 0) {
                return -2;
            }

            $form = array(
                $this->name => $this->clean($this->inputs[$this->name]),
                'service_fee' => $this->clean($this->inputs['service_fee']),
                'service_desc' => $this->clean($this->inputs['service_desc'])
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

    /* ---------------------- PAYMONGO FUNCTIONS ---------------------- */

    public function add_payment_intent()
    {
        $amount = $this->inputs['amount'];
        $payment_method = $this->inputs['payment_method'];
        $rehab_center_id = $this->inputs['rehab_center_id'];
        $admission_id = $this->inputs['admission_id'];
        $user_id = $this->inputs['user_id'];

        // Create Payment Intent
        $data = [
            "data" => [
                "attributes" => [
                    "amount" => $amount,
                    "currency" => "PHP",
                    "payment_method_allowed" => [$payment_method],
                    "capture_type" => "automatic"
                ]
            ]
        ];

        $ch = curl_init("https://api.paymongo.com/v1/payment_intents");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Basic " . base64_encode($this->PAYMONGO_SECRET_KEY . ":")
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $result = json_decode($response, true);

        if (!isset($result['data']['id'])) {
            return ["error" => "Payment intent creation failed"];
        }

        $intentId = $result['data']['id'];

        // Save to DB
        $form = array(
            'admission_id' => $admission_id,
            'payment_intent_id' => $intentId,
            'payment_method' => $payment_method,
            'user_id' => $user_id,
            'payment_date' => $this->getCurrentDate()
        );

        try {
            $this->checker();
            $this->begin_transaction();
            $this->query("USE rehab_management_{$rehab_center_id}_db");
            $this->insert($this->table, $form);
            $this->commit();
        } catch (\Throwable $th) {
            $this->rollback();
            return ["error" => "DB insert failed: " . $th->getMessage()];
        }

        return ["intent_id" => $intentId];
    }

    public function attach_payment(){
        return $intentId = $this->inputs['intent_id'];
        $type = $this->inputs['payment_method']; // gcash or paymaya

        // Step 1: Create payment method
        $methodData = [
            "data" => [
                "attributes" => [
                    "type" => $type
                ]
            ]
        ];

        $ch = curl_init("https://api.paymongo.com/v1/payment_methods");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Basic " . base64_encode($this->PAYMONGO_SECRET_KEY . ":")
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($methodData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $methodResponse = curl_exec($ch);

        if(curl_errno($ch)) {
            return ["status" => "error", "message" => curl_error($ch)];
        }

        $methodResult = json_decode($methodResponse, true);

        if(!isset($methodResult['data']['id'])) {
            return ["status" => "error", "message" => "Payment method creation failed", "response" => $methodResult];
        }

        $paymentMethodId = $methodResult['data']['id'];

        // Step 2: Attach to intent
        $attachData = [
            "data" => [
                "attributes" => [
                    "payment_method" => $paymentMethodId,
                    "return_url" => "rehabmanager://payment-success?intent_id=$intentId"
                ]
            ]
        ];

        $ch = curl_init("https://api.paymongo.com/v1/payment_intents/$intentId/attach");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Basic " . base64_encode($this->PAYMONGO_SECRET_KEY . ":")
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($attachData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $attachResponse = curl_exec($ch);

        if(curl_errno($ch)) {
            return ["status" => "error", "message" => curl_error($ch)];
        }

        $attachResult = json_decode($attachResponse, true);

        if(!isset($attachResult['data']['attributes']['next_action']['redirect']['url'])) {
            return ["status" => "unknown", "redirect_url" => null, "attach_result" => $attachResult];
        }

        return [
            "status" => $attachResult['data']['attributes']['status'],
            "redirect_url" => $attachResult['data']['attributes']['next_action']['redirect']['url'],
            "attach_result" => $attachResult
        ];
    }

    public function check_status()
    {
        $intentId = $this->inputs['intent_id'];

        $ch = curl_init("https://api.paymongo.com/v1/payment_intents/$intentId");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Basic " . base64_encode($this->PAYMONGO_SECRET_KEY . ":")
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $result = json_decode($response, true);

        $status = $result['data']['attributes']['status'] ?? 'unknown';

        return ["status" => $status, "data" => $result];
    }
}
