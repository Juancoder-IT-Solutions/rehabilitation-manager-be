<?php
class Connection
{
    public $que;
    private $servername = HOST;
    private $username = USER;
    private $password = PASSWORD;
    private $dbname = DBNAME;
    private $result = array();
    private $mysqli;
    //private $userID = USERID;


    public function __construct($dbname = null)
    {
        $this->connect($dbname ?? $this->dbname);
    }

    private function connect($dbname)
    {
        $this->mysqli = new mysqli(
            $this->servername,
            $this->username,
            $this->password,
            $dbname
        );

        if ($this->mysqli->connect_error) {
            die("Connection failed: " . $this->mysqli->connect_error);
        }
    }

    public function switchDatabase($dbname)
    {
        $this->connect($dbname);
    }

    public function checker()
    {
        if ($this->mysqli->connect_errno) {
            throw new Exception('Failed to connect to MySQL: ' . $this->mysqli->connect_error);
        }
    }

    public function databaseExists($dbname)
    {
        $check = new mysqli($this->servername, $this->username, $this->password);
        if ($check->connect_error) {
            return false;
        }
        $res = $check->query("SHOW DATABASES LIKE '$dbname'");
        $check->close();
        return $res && $res->num_rows > 0;
    }

    public function begin_transaction()
    {
        $this->mysqli->begin_transaction();
    }

    public function commit()
    {
        $this->mysqli->commit();
    }

    public function rollback()
    {
        $this->mysqli->rollback();
    }

    public function insert($table, $para = array(), $last_id = 'N')
    {
        $table_columns = implode(',', array_keys($para));
        $table_value = implode("','", $para);

        $sql = "INSERT INTO $table($table_columns) VALUES('$table_value')";

        if ($this->mysqli->query($sql) === TRUE)
            return ($last_id == 'Y') ? $this->mysqli->insert_id : 1;
        return $this->mysqli->error;
    }

    public function insert_logs($remarks)
    {
        $form = array(
            'remarks'   => $remarks,
            'user_id'   => $_SESSION['cdms_user_id'],
        );

        $this->insert('tbl_logs', $form);
    }

    public function insert_select($table, $table_select, $para, $where_clause = '')
    {
        $table_columns = array_keys($para);
        $table_value = implode(",", $para);
        $inject = ($where_clause == '') ? "" : "WHERE $where_clause";

        $sql = "INSERT INTO " . $table . " (`" . implode('`,`', $table_columns) . "`) SELECT $table_value FROM $table_select $inject";

        $result = $this->mysqli->query($sql) or die($this->mysqli->error);
        return $result ? 1 : 0;
    }

    public function insertIfNotExist($table, $form, $param = '', $last_id = 'N')
    {
        $inject = $param != '' ? $param : "$this->name = '" . $this->clean($this->inputs[$this->name]) . "'";
        $is_exist = $this->select($table, $this->pk, $inject);
        if ($is_exist->num_rows > 0) {
            return $last_id == 'Y' ? -2 : 2;
        } else {
            return $this->insert($table, $form, $last_id);
        }
    }

    public function update($table, $para = array(), $id)
    {
        $args = array();

        foreach ($para as $key => $value) {
            $args[] = "$key = '$value'";
        }

        $sql = "UPDATE  $table SET " . implode(',', $args);
        $sql .= " WHERE $id";

        return $this->mysqli->query($sql) === TRUE ? 1 : $this->mysqli->error;
    }

    public function updateIfNotExist($table, $form, $param = '')
    {
        $primary_id = $this->inputs[$this->pk];
        $inject = $param != '' ? $param : "$this->name = '" . $this->clean($this->inputs[$this->name]) . "'";
        $inject .= " AND $this->pk != '$primary_id'";
        $is_exist = $this->select($table, $this->pk, $inject);
        if ($is_exist->num_rows > 0) {
            return 2;
        } else {
            return $this->update($table, $form, "$this->pk = '$primary_id'");
        }
    }

    public function delete($table, $id)
    {
        $sql = "DELETE FROM $table";
        $sql .= " WHERE $id ";
        $sql;
        return $this->mysqli->query($sql) or die($this->mysqli->error);
    }

    public function query($sql)
    {
        return $this->mysqli->query($sql) === TRUE ? 1 : $this->mysqli->error;
    }

    public function raw_query($query = "")
    {
        $sql = $this->clean($query);
        return $this->mysqli->query($sql);
    }

    public $sql;

    public function select($table, $rows = "*", $where = null)
    {
        $sql = "SELECT $rows FROM $table";
        $inject = $where != null ? " WHERE $where" : "";

        $sql .= $inject;

        $result = $this->mysqli->query($sql);

        return $result ? $result : $this->mysqli->error;
    }

    public function encrypt($password, $algo = PASSWORD_DEFAULT)
    {
        return password_hash($password, $algo);
    }

    public function clean($slug)
    {
        if (is_string($slug)) {
            return $this->mysqli->real_escape_string($slug);
        } else {
            return $slug;
        }
    }

    public function getCurrentDate()
    {
        ini_set('date.timezone', 'UTC');
        //error_reporting(E_ALL);
        date_default_timezone_set('UTC');
        $today = date('H:i:s');
        $system_date = date('Y-m-d H:i:s', strtotime($today) + 28800);
        return $system_date;
    }

    public function metadata($metas = ['name' => "id", 'type' => 'int', 'length' => 11, 'allow_null' => 'NOT NULL', 'default' => 'CURRENT_TIMESTAMP', 'extra' => 'AUTO_INCREMENT', 'comment' => "'A=Admin' (string)"])
    {
        // $name, $type, $length = '', $allow_null = 'NOT NULL', $default = '', $extra = '', $comment = ''
        return array(
            'name'          => $metas['name'],
            'type'          => $metas['type'],
            'length'        => isset($metas['length']) ? $metas['length'] : '',
            'allow_null'    => isset($metas['allow_null']) ? $metas['allow_null'] : 'NOT NULL',
            'default'       => isset($metas['default']) ? $metas['default'] : '',
            'extra'         => isset($metas['extra']) ? $metas['extra'] : '',
            'comment'       => isset($metas['comment']) ? $metas['comment'] : '',
        );
    }

    public function schemaCreator($tables)
    {
        $create = [];
        foreach ($tables as $table) {
            $name = $table['name'];
            $fields = $table['fields'];
            $is_exists = $this->table_exists($name);

            $field_list = array();
            foreach ($fields as $field) {

                $fld = array();
                $fld[] = "`$field[name]`";
                $fld[] = $field['type'] . ($field['length'] > 0 ? "($field[length])" : "");
                $fld[] = $field['allow_null'];
                $fld[] = $field['default'] != '' ? "DEFAULT $field[default]" : "";
                $fld[] = $field['extra'];
                $fld[] = $field['comment'] != '' ? "COMMENT $field[comment]" : "";

                if ($is_exists == 1) {
                    // $is_column_exists
                    if ($this->column_exists($name, $field['name']) != 1) {
                        array_push($field_list, (" ADD " . implode(" ", $fld)));
                    }
                } else {
                    $metadata = implode(" ", $fld);
                    array_push($field_list, $metadata);
                }
            }
            $is_exists == 1 ? "" : array_push($field_list, "PRIMARY KEY (`{$table['primary']}`)");
            if (count($field_list) > 0) {
                if ($is_exists == 1) {
                    $query = "ALTER TABLE `$name`";
                } else {
                    $query = "CREATE TABLE `$name` (";
                }
                $query .= implode(",", $field_list);
                $query .= $is_exists == 1 ? "" : ') ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;';
                $status = $this->mysqli->query($query);
                $create[] = ['table' => $name, 'status' => $status, 'query' => $query, 'error' => $this->mysqli->error];
            }
        }
        return $create;
    }

    public function triggerCreator($triggers)
    {
        $create = [];
        foreach ($triggers as $trigger) {
            $trigger_name   = $trigger['name'];
            $table          = $trigger['table'];
            $action_time    = $trigger['action_time'];
            $event          = $trigger['event'];
            $statement      = $trigger['statement'];

            $query = "";

            if (is_array($statement) == 1) {
                // $query .= "DELIMITER $$";
                $statements = "\n\t" . implode("\n\t", $statement) . "\n";
                $begin = "BEGIN";
                $end = "END;";
            } else {
                $statements = $statement;
                $begin = "";
                $end = "";
            }

            $query .= "CREATE TRIGGER $trigger_name $action_time $event ON $table FOR EACH ROW $begin $statements $end";
            $status = $this->mysqli->query($query);
            $create[] = ['trigger_name' => $trigger_name, 'status' => $status, 'query' => $query, 'error' => $this->mysqli->error];
        }

        return $create;
    }

    function table_exists($table)
    {
        $result = $this->mysqli->query("SHOW TABLES LIKE '{$table}'");
        if ($result->num_rows == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
        $result->free();
    }

    function column_exists($table_name, $column_name)
    {
        $db_name = DBNAME;
        $result = $this->mysqli->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$db_name' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = '$column_name'");
        if ($result->num_rows == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
        $result->free();
    }

    public $join = array();
    public $tablename = '';
    public $select2 = array();
    public $where = array();
    public $orderBy = [];
    public $groupBy = '';


    public function reset_qury_inputs()
    {
        $this->join = array();
        $this->tablename = '';
        $this->select2 = array();
        $this->where = array();
        $this->orderBy = [];
        $this->groupBy = '';
    }

    public function table($table)
    {

        $this->tablename = $table;
        return $this;
    }

    public function selectRaw(...$select2)
    {
        foreach ($select2 as $query) {
            $this->select2[] = $query;
        }
        return $this;
    }

    public function join($table, $from, $identifier, $to)
    {
        $this->join[] = "INNER JOIN $table ON $from $identifier $to";
        return $this;
    }

    public function where($column, $equal, $to = '')
    {
        $where = ($to != '') ? "$equal '$to'" : "= '$equal'";
        $this->where[] = "$column $where";
        return $this;
    }

    public function whereIn($column, $in)
    {
        $this->where[] = "$column IN($in)";
        return $this;
    }

    public function whereNotInQuery($column, $field_name, $table_name, $where = '')
    {
        $inject = $where != null ? " WHERE $where" : "";
        $this->where[] = "$column NOT IN(SELECT $field_name FROM $table_name $inject)";
        return $this;
    }

    public function orderBy($column, $direction = 'asc')
    {
        $this->orderBy[] = compact('column', 'direction');
        return $this;
    }

    public function groupBy($column)
    {
        $this->groupBy = "GROUP BY $column";
        return $this;
    }

    public function get()
    {
        $select = (count($this->select2) > 0 ? implode(",", $this->select2) : '*');
        $where = count($this->where) > 0 ? "WHERE " . implode(' AND ', $this->where) : '';

        $query = "SELECT {$select} FROM {$this->tablename} ";
        $query .= implode(' ', $this->join);
        $query .= " $where ";
        $query .= " $this->groupBy ";

        if (!empty($this->orderBy)) {
            $orderClauses = [];
            foreach ($this->orderBy as $order) {
                $orderClauses[] = "{$order['column']} {$order['direction']}";
            }
            $query .= " ORDER BY " . implode(', ', $orderClauses);
        }
        $this->reset_qury_inputs();
        $this->queries = $query;
        return $this->mysqli->query($query);
    }

    public function __destruct()
    {
        $this->mysqli->close();
    }
}
