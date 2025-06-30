<?php
class RehabCenters extends Connection
{
    private $table = 'tbl_rehab_centers';
    public $pk = 'rehab_center_id';
    public $name = 'rehab_center_name';
    public $module_name = "Rehab Centers";

    public $inputs = [];
    public $response = "";

    public $searchable = ['rehab_center_name'];
    public $uri = "Rehab Centers";

    public $authUserId = 0;

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

    public static function name($primary_id)
    {
        $self = new self;
        $result = $self->select($self->table, $self->name, "$self->pk  = '$primary_id'");
        if($result->num_rows == 0){
            return "";
        }else{
            $row = $result->fetch_assoc();
            return $row[$self->name];
        }
    }
}
