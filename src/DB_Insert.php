<?php
namespace Wepesi\App;

class DB_Insert extends DB_Q {
    private string $table;
    private $_fields;
    use DBField;
    function __construct(\PDO $pdo, string $table)
    {
        $this->table = $table;
        parent::__construct($pdo);
    }

    function field(array $fields): DB_Insert
    {
        $result=$this->field_params($fields,"insert");
        if(is_array($result)){
            $this->_fields = $result;
        }else{
            echo ("This method try to access undefined method");
        }
        return $this;
    }

    //
//    function field(array $fields = [])
//    {
//        if (count($fields) && !$this->_fields) {
//            $keys = $fields;
//            $params = $keys;
//
//            $keys = array_keys($fields);
//            $values = null;
//            $_trim_key=[];
//            $count_key=count($fields);
//            $x =0;
//            for($x = 0;$x<$count_key;$x++) {
//                $values .= "?";
//                if ($x < ($count_key-1)) {
//                    $values .= ',';
//                }
//                array_push($_trim_key,trim($keys[$x]));
//            }
//            $keys=$_trim_key;
//            $implode_keys= "`" . implode('`,`', $keys) . "`";
//
//            $this->_fields = [
//                "fields" => $implode_keys,
//                "values" => $values,
//                "params" => $params
//            ];
//            return $this;
//        }else{
//            throw new \Exception("This method try to access undefined method");
//        }
//    }

    /**
     * @param $sql
     * @param array $params
     * this module is use to execute sql request
     */
    private function query($sql, array $params = [])
    {
        $this->executeQuery($sql, $params);
    }

    /**
     *
     * @return void
     * use this module to create new record
     */
    private function insert()
    {
        if (isset($this->_fields['fields']) && isset($this->_fields['values']) && isset($this->_fields['params'])){
            $fields = $this->_fields['fields'];
            $values =  $this->_fields['values'];
            $params = $this->_fields['params'];
            $sql = "INSERT INTO $this->table ($fields) VALUES ($values)";
            $this->query($sql, $params);
        }
    }

    /**
     * @return array|null
     * return result after a request select
     */
    function result(): ?array
    {
        $this->insert();
        return $this->get_result();
    }

    /**
     * return an error status when an error occur while doing an query
     * @return mixed
     */
    function error()
    {
        return $this->get_Error();
    }

    /**
     * @return int
     * access the last id record after creating a new record
     */
    function lastId(): int
    {
        return $this->get_lastid();
    }
}