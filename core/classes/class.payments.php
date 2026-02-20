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

    /** ----------------------
     * Add payment record (local DB)
     * ---------------------- */
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
            if (!is_object($is_exist)) throw new Exception($is_exist);
            if ($is_exist->num_rows > 0) return -2;

            $form = [
                'admission_id' => $admission_id,
                'user_id' => $user_id,
                'reference_number' => $reference_number,
                'payment_date' => $this->getCurrentDate()
            ];
            $insert_query = $this->insert($this->table, $form);
            if (!is_int($insert_query)) throw new Exception($insert_query);

            $this->commit();
            return 1;
        } catch (Exception $e) {
            $this->rollback();
            $this->response = "error";
            return $e->getMessage();
        }
    }

    /** ----------------------
     * Create PayMongo Payment Intent
     * ---------------------- */
    public function add_payment_intent()
    {
        try {
            $amount = intval($this->inputs['amount']);
            $payment_method = $this->inputs['payment_method']; // gcash or paymaya
            $admission_id = $this->clean($this->inputs['admission_id']);
            $user_id = $this->clean($this->inputs['user_id']);
            $rehab_center_id = $this->inputs['rehab_center_id'];

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
            curl_setopt_array($ch, [
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Authorization: Basic " . base64_encode($this->PAYMONGO_SECRET_KEY . ":")
                ],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_RETURNTRANSFER => true
            ]);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));

            $result = json_decode($response, true);
            if (!isset($result['data']['id'])) throw new Exception("Payment intent failed");

            $intentId = $result['data']['id'];

            // Save to DB
            $this->checker();
            $this->begin_transaction();
            $this->query("USE rehab_management_{$rehab_center_id}_db");
            $form = [
                'admission_id' => $admission_id,
                'payment_intent_id' => $intentId,
                'payment_method' => $payment_method,
                'user_id' => $user_id,
                'payment_date' => $this->getCurrentDate()
            ];
            $this->insert($this->table, $form);
            $this->commit();

            return [
                "status" => "success",
                "intent_id" => $intentId,
                "client_key" => $result['data']['attributes']['client_key']
            ];

        } catch (\Throwable $th) {
            $this->rollback();
            return ["status" => "error", "message" => $th->getMessage()];
        }
    }

    /** ----------------------
     * Attach Payment Method and return redirect URL
     * ---------------------- */
    public function attach_payment()
    {
        try {
            $intentId = $this->inputs['intent_id'];
            $methodType = $this->inputs['payment_method']; // gcash / paymaya
            $returnUrl = "rehabmanager://payment-success?intent_id=$intentId";

            // Step 1: Create Payment Method
            $methodData = [
                "data" => [
                    "attributes" => [
                        "type" => $methodType
                    ]
                ]
            ];

            $ch = curl_init("https://api.paymongo.com/v1/payment_methods");
            curl_setopt_array($ch, [
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Authorization: Basic " . base64_encode($this->PAYMONGO_SECRET_KEY . ":")
                ],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($methodData),
                CURLOPT_RETURNTRANSFER => true
            ]);

            $methodResp = curl_exec($ch);
            if ($methodResp === false) throw new Exception(curl_error($ch));

            $methodRes = json_decode($methodResp, true);
            if (!isset($methodRes['data']['id'])) throw new Exception("Payment method creation failed");

            $paymentMethodId = $methodRes['data']['id'];

            // Step 2: Attach Payment Method to Intent
            $attachData = [
                "data" => [
                    "attributes" => [
                        "payment_method" => $paymentMethodId,
                        "return_url" => $returnUrl
                    ]
                ]
            ];

            $ch = curl_init("https://api.paymongo.com/v1/payment_intents/$intentId/attach");
            curl_setopt_array($ch, [
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Authorization: Basic " . base64_encode($this->PAYMONGO_SECRET_KEY . ":")
                ],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($attachData),
                CURLOPT_RETURNTRANSFER => true
            ]);

            $attachResp = curl_exec($ch);
            if ($attachResp === false) throw new Exception(curl_error($ch));

            $attachRes = json_decode($attachResp, true);
            $redirectUrl = $attachRes['data']['attributes']['next_action']['redirect']['url'] ?? null;

            if (!$redirectUrl) throw new Exception("Redirect URL not returned");

            return [
                "status" => "redirect",
                "redirect_url" => $redirectUrl,
                "intent_id" => $intentId
            ];

        } catch (\Throwable $th) {
            return ["status" => "error", "message" => $th->getMessage()];
        }
    }

    /** ----------------------
     * Check Payment Intent Status
     * ---------------------- */
    public function check_status()
    {
        try {
            $intentId = $this->inputs['intent_id'];

            $ch = curl_init("https://api.paymongo.com/v1/payment_intents/$intentId");
            curl_setopt_array($ch, [
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Authorization: Basic " . base64_encode($this->PAYMONGO_SECRET_KEY . ":")
                ],
                CURLOPT_RETURNTRANSFER => true
            ]);

            $resp = curl_exec($ch);
            if ($resp === false) throw new Exception(curl_error($ch));

            $result = json_decode($resp, true);
            $status = $result['data']['attributes']['status'] ?? 'unknown';

            return [
                "intent_id" => $intentId,
                "status" => $status
            ];

        } catch (\Throwable $th) {
            return ["status" => "error", "message" => $th->getMessage()];
        }
    }
}
