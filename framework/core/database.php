<?php
class Database {
    
    private $server = "127.0.0.1";
    private $username = "root";
    private $password = "";
    private $database = "databasename";

    private $conn;
    public $insert_id;

    function __construct() {
        global $app;

        if(!$this->server || !$this->username || !$this->database) {
            return;
        }

        $this->conn = new mysqli(
            $this->server, 
            $this->username, 
            $this->password, 
            $this->database
        );

        if (!$this->conn) {
            die("Debugging error: " . mysqli_connect_error());
        }
    }

    public function query($query) {
        global $app;
        // AND || OR || NOT || IN() || BETWEEN 1 AND 2 || ORDER BY field ASC || GROUP BY field

        if(empty($query)) {
            return false;
        }

        return $this->conn->query($query);
    }

    public function save($table, $key, $array)
    {
        global $app;

        // Check if all parameters are set
        if(empty($table || $key || $array)) {
            return;
        }

        // If array value is a string then add quotes
        foreach($array as $key => $value) {
            if(is_string($value)) {
                $array[$key] = "'" . $value . "'";
            }
        }

        // Comma separated list of all keys
        $insertKeys = implode(",", array_keys($array));
        // Comma sepaprated list of all values
        $insertValues = implode(",", $array);

        // If the primary key is defined in the array: replace, else: insert
        if(array_key_exists($key, $array)) {
            $qResult = $this->conn->query("REPLACE INTO {$table} VALUES ({$insertValues})");
        } else {
            $qResult = $this->conn->query("INSERT INTO {$table} ({$insertKeys}) VALUES ({$insertValues})");
        }

        // Save new (or existing) primary key in class property
        if($qResult) {
            $this->insert_id = $this->conn->insert_id;
            return $this->insert_id;
        }

        return $qResult;
    }

    public function update($table, $change, $condition)
    {
        global $app;

        // Check if all parameters are set
        if(empty($table || $change || $condition)) {
            return;
        }

        // Execute query
        $qResult = $this->conn->query("UPDATE `" . $table . "` SET " . $change . " WHERE " . $condition);

        // Save updated primary key in class property
        if($qResult) {
            $this->insert_id = $this->conn->insert_id;
        }

        return $qResult;
    }

    public function remove($table, $key, $id)
    {
        global $app;

        // Check if all parameters are set
        if(empty($table || $key || $id)) {
            return;
        }

        // Execute query
        $qResult = $this->conn->query("DELETE FROM {$table} WHERE {$key} = {$id}");

        return $qResult;
    }

    public function get($args = array())
    {
        global $app;

        // Check if table is defined
        $table = $args['table'];
        if(empty($table)) {
            return;
        }

        // Prepare order and direction
        if(!empty($args['order'])) {
            $order_query = '`' . $args['order'] . '` ' . (!empty($args['direction']) ? strtoupper($args['direction']) : 'ASC');
        }

        // Prepare where statement
        $where_query = !empty($args['where']) ? $args['where'] : '';

        // Prepare join statement
        $join_query = !empty($args['join']) ? $args['join'] : '';

        // Execute select query
        $qResult = $this->conn->query("SELECT * FROM `{$table}`"
             . (!empty($join_query) ? " LEFT JOIN " . $join_query : "")
             . (!empty($where_query) ? " WHERE " . $where_query : "") 
             . (!empty($order_query) ? " ORDER BY {$order_query}" : "")
        );

        // if query has no result return empty
        if (empty($qResult) || mysqli_num_rows($qResult) == 0) {
            return array();
        }

        // Find out what the primary key is (for output array key)
        $primary_key = '';
        while ($meta = $qResult->fetch_field()) {
            if ($meta->flags & MYSQLI_PRI_KEY_FLAG) {
                $primary_key = $meta->name;
            }
        }

        // Add found rows to output array
        $get = array();
        while ($row = $qResult->fetch_assoc()) {
            if(!empty($primary_key)) {
                // Row key is primary key
                $get[$row[$primary_key]] = $row;
            }
            else {
                // Row key is default
                $get[] = $row;
            }
        }

        return $get;
    }
}
?>