<?php
class RehabGallery extends Connection
{
    private $table = 'tbl_rehab_center_gallery';
    public $pk = 'id';
    public $name = 'file';
    public $module_name = "Rehab Gallery";

    public $inputs = [];
    public $response = "";

    public $searchable = ['file'];

    public $authUserId = 0;
    public $authRehabCenterId = 0;

    // public function add()
    // {
    //     if (isset($_FILES['file']['tmp_name'])) {
    //         $file = $_FILES['file']['name'];
    //         move_uploaded_file($_FILES['file']['tmp_name'], './uploads/' . $file);
    //     } else {
    //         $file = "";
    //     }

    //     $form = array(
    //         'file'      => $file,
    //     );
    //     return $this->insert($this->table, $form);

    // }

    public function add()
    {
        try {
            $this->response = "success";

            $this->checker();
            $this->begin_transaction();

            $file_b64 = $this->clean($this->inputs[$this->name]);
            $upload_dir = realpath(__DIR__ . '/../../gallery/') . '/';


            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (preg_match('/^data:image\/(\w+);base64,/', $file_b64, $matches)) {
                $image_type = $matches[1]; // jpg, png, etc.
                $file_b64 = substr($file_b64, strpos($file_b64, ',') + 1);
                $file_b64 = base64_decode($file_b64); // Decode Base64

                if ($file_b64 === false) {
                    throw new Exception("Invalid Base64 encoding.");
                }

                $file_name = uniqid($this->authRehabCenterId . '-', true) . "." . $image_type;
                $file_path = $upload_dir . $file_name;

                // Save the image file
                if (!file_put_contents($file_path, $file_b64)) {
                    throw new Exception("Failed to save image.");
                }

                $form = array(
                    $this->name => $file_name,
                    'rehab_center_id' => $this->authRehabCenterId
                );
                $insert_query = $this->insert($this->table, $form);

                if (!is_int($insert_query)) {
                    throw new Exception($insert_query);
                }

                $this->commit();
                return 1;
            } else {
                throw new Exception("Invalid file format.");
            }
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

            $service_id = $this->clean($this->inputs[$this->pk]); // Primary Key
            $file_b64 = $this->clean($this->inputs[$this->name]);

            // Check if record exists
            $is_exist = $this->select($this->table, "{$this->name}", "{$this->pk} = '$service_id'");

            if (!is_object($is_exist)) {
                throw new Exception($is_exist);
            }

            if ($is_exist->num_rows == 0) {
                return -1; // Record not found
            }

            $row = $is_exist->fetch_assoc();
            $old_file_name = $row[$this->name];

            $upload_dir = realpath(__DIR__ . '/../../gallery/') . '/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Process base64 image
            if (preg_match('/^data:image\/(\w+);base64,/', $file_b64, $matches)) {
                $image_type = $matches[1]; // jpg, png, etc.
                $file_b64 = substr($file_b64, strpos($file_b64, ',') + 1);
                $file_b64 = base64_decode($file_b64); // Decode Base64

                if ($file_b64 === false) {
                    throw new Exception("Invalid Base64 encoding.");
                }

                $file_name = uniqid($this->authRehabCenterId . '-', true) . "." . $image_type;
                $file_path = $upload_dir . $file_name;

                // Save new image
                if (!file_put_contents($file_path, $file_b64)) {
                    throw new Exception("Failed to save image.");
                }

                // Delete old image if exists
                if (!empty($old_file_name) && file_exists($upload_dir . $old_file_name)) {
                    unlink($upload_dir . $old_file_name);
                }

                // Prepare updated data
                $form = array(
                    $this->name => $file_name,
                );

                // Perform update
                $update_query = $this->update($this->table, $form, "{$this->pk} = '$service_id'");

                if (!is_int($update_query)) {
                    throw new Exception($update_query);
                }

                $this->commit();
                return 1; // Success
            } else {
                throw new Exception("Invalid file format.");
            }
        } catch (Exception $e) {
            $this->rollback();
            $this->response = "error";
            return $e->getMessage();
        }
    }


    public function show()
{
    $param = $this->inputs['param'] ?? null;
    $rows = [];
    $count = 1;
    $result = $this->select($this->table, '*', $param);

    while ($row = $result->fetch_assoc()) {
        $row['count'] = $count++;
        
        if (!empty($row['file'])) {
            $filePath = realpath(__DIR__ . '/../../gallery/') . '/' . $row['file'];

            if (file_exists($filePath)) {
                $row['file_b64'] = base64_encode(file_get_contents($filePath));
            } else {
                $row['file_b64'] = null;
            }
        } else {
            $row['file_b64'] = null;
        }

        unset($row['file']); // Remove original file path if not needed
        $rows[] = $row;
    }

    return $rows;
}


    public function remove()
    {
        try {
            $this->response = "success";
            $this->checker();
            $this->begin_transaction();

            $ids = implode(",", array_map([$this, 'clean'], $this->inputs['ids']));
            $upload_dir = __DIR__ . "/../../gallery/";

            $result = $this->select($this->table, $this->name, "$this->pk IN ($ids)");

            while ($row = $result->fetch_assoc()) {
                $file_path = $upload_dir . $row[$this->name];
                if (file_exists($file_path)) unlink($file_path); // Delete file
            }

            $this->delete($this->table, "$this->pk IN ($ids)");

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
