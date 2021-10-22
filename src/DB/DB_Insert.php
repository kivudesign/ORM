<?php
namespace Wepesi\App;

class DB_Insert{
    private $table, $_pdo;
    private $_fields;
    private $_error;
    private $_results = false;
    private $_lastid;
    function __construct(\PDO $pdo, string $table)
    {
        $this->table = $table;
        $this->_pdo = $pdo;
    }

    //
    function field(array $fields = [])
    {
        if (count($fields) && !$this->_fields) {
            $keys = $fields;
            $params = $keys;

            $keys = array_keys($fields);
            $values = null;
            $_trim_key=[];
            $count_key=count($fields);
            for($x = 0;$x=$count_key;$x++) {
                $values .= "? ";
                if ($x < count($fields)) {
                    $values .= ', ';
                }
                array_push($_trim_key,trim($keys[($x)]));
            }
            $keys=$_trim_key;
            $implode_keys= "`" . implode('`,`', $keys) . "`";

            $this->_fields = [
                "fields" => $implode_keys,
                "values" => $values,
                "params" => $params
            ];
            return $this;
        }else{
            throw new \Exception("This method try to access undefined method");
        }
    }

    /**
     * @param $sql
     * @param array $params
     * @return $this
     * this module is use to execute sql request
     */
    private function query($sql, array $params = [])
    {
        $q = new DB_Qeury($this->_pdo, $sql, $params);
        $this->_results = $q->result();
        $this->_error = $q->getError();
        $this->_lastid = $q->lastId();
    }

    /**
     * @return bool
     * use this module to create new record
     */
    private function insert()
    {
        if (isset($this->_fields['fields']) && isset($this->_fields['values']) && isset($this->_fields['params'])){
            $fields = $this->_fields['fields'];
            $values =  $this->_fields['values'];
            $params = $this->_fields['params'];
            $sql = "INSERT INTO $this->table ($fields) VALUES ($values)";
            if (!$this->query($sql, $params)->error()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     * return result after a request select
     */
    function result()
    {
        $this->insert();
        return $this->_results;
    }
    // return an error status when an error occure while doing an querry
    function error()
    {
        return $this->_error;
    }

    /**
     * @return mixed
     * access the last id record after creating a new record
     */
    function lastId()
    {
        return $this->_lastid;
    }
}