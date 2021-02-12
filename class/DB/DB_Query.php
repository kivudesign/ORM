<?php
class DB_Query{
    private $table, $action, $_pdo;
    private $_where, $_fields;
    private  $_error,$_results = false,$_count = 0, $_lastid;
    function __construct($pdo, string $table, string $action)
    {
        $this->table = $table;
        $this->action = $action;
        $this->_pdo = $pdo;
        $this->_asc = null;
        $this->_dsc = null;
        $this->_limit = null;
        $this->_offset = null;
    }

    function where(array $where = [])
    {
        // select where <> update where
        /**
         * select WHERE format
         * [
         *  [field,comparisonOperator,value,logicOperator]
         * ]
         * eg:[
         *  ["name","=","john","and"]
         * ]
         */
        try{
            if ($this->action == "insert") {
                throw new Exception("This method try to access undefined method");
            }
            if (count($where)) {
                $keys = $where;
                $params = [];
                $values = null;

                $x = 1;
                if ($this->action === "insert") {
                    $getKeys = array_keys($keys);
                    $checkKey = is_string($getKeys[0]);
                    $params = $where;
                    if (!$checkKey) {
                        throw new Exception("where data format error");
                    }
                    $keys = array_keys($where);
                    foreach ($where as $elem) {
                        $values .= "? ";
                        if ($x < count($where)) {
                            $values .= ', ';
                        }
                        $x++;
                    }
                } else {
                    // defined comparion operator to avoid error while assing operation witch does not exist
                    $comparisonOperator = ["<", "<=", ">", ">=", "<>", "!="];
                    $logicalOperator = ["or", "not"];
                    // chech if the array is multidimensional array
                    $where = is_array($where[0]) ? $where : [$where];
                    $whereLen = count($where);
                    // 
                    $jointureWhereCondition = "";
                    $defaultComparison = "=";
                    $lastIndexWhere = 1;
                    $fieldValue = [];
                    // 
                    foreach ($where as $WhereField) {
                        $defaultLogical = " and ";
                        $notComparison = "";
                        // check if there is a logical operatior `or`||`and`
                        if (isset($WhereField[3])) {
                            // check id the defined operation exist in our defined tables
                            $defaultLogical = in_array(strtolower($WhereField[3]), $logicalOperator) ? $WhereField[3] : " and ";
                            if ($defaultLogical === "not") {
                                $notComparison = " not ";
                            }
                        }
                        // check the field exist and defined by default one
                        $_WhereField = strlen($WhereField[0]) > 0 ? $WhereField[0] : "id";                    
                        $jointureWhereCondition .= " {$notComparison}{$_WhereField}{$defaultComparison} ? ";
                        $valueTopush = isset($WhereField[2]) ? $WhereField[2] : "";
                        array_push($fieldValue, $valueTopush);
                        array_push($params, [$_WhereField => $valueTopush]);

                        if ($lastIndexWhere < $whereLen) {
                            if ($defaultLogical != "not") {
                                $jointureWhereCondition .= $defaultLogical;
                            }
                        }
                        $lastIndexWhere++;
                    }
                    $params = $params[0];
                }
                $this->_where = [
                    "field" => "WHERE {$jointureWhereCondition}",
                    "value" => $fieldValue,
                    "params" => $params
                ];
            } else {
                $this->_where = [];
            }
            return $this;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }
    function fields(array $fields = [])
    {
        if (count($fields) && !$this->_fields && ($this->action == "insert" || $this->action == "update")) {
            $keys = $fields;
            $values = null;
            $params = $keys;
            $x = 1;
            if ($this->action == "insert" || $this->action == "update") {
                $keys = array_keys($fields);
                $values = "";
                foreach ($fields as $field) {
                    $values .= "? ";
                    if ($x < count($fields)) {
                        $values .= ', ';
                    }
                    $x++;
                }
            }
            $implode = "`" . implode('`,`', $keys) . "`";
            if ($this->action == "update") {
                $implode = "`" . implode('`= ?,`', $keys) . "`";
                $implode .= "=?";
            }
            $this->_fields = [
                "keys" => $implode,
                "values" => $values,
                "params" => $params
            ];
        }
        return $this;
    }
    private function query($sql, array $params = [])
    {
        $q = new DB_Exec_Qeury($this->_pdo, $sql, $params);
        $this->_results = $q->result();
        $this->_count = $q->rowCount();
        $this->_error = $q->getError();
        $this->_lastid=$q->lastId();
        return $this;
    }
    // 
    private function insert()
    {
        if (isset($this->_fields['keys']) && isset($this->_fields['values']) && isset($this->_fields['params'])) {
            $fields = $this->_fields['keys'];
            $values =  $this->_fields['values'];
            $params = $this->_fields['params'];
            $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$values})";           
            if (!$this->query($sql, $params)->error()) {
                return true;
            }
        }
        return false;
    }
    // delete module
    private function delete()
    {
        $where = isset($this->_where['field']) ? $this->_where['field'] : "";
        $params = isset($this->_where['params']) ? $this->_where['params'] : [];
        $sql = "DELETE FROM {$this->table} {$where}";
        if (!$this->query($sql, $params)->error()) {
            return true;
        }
        return false;
    }
    // update module
    private function update()
    {
        $where = isset($this->_where['field']) ? $this->_where['field'] : "";
        $where_params = isset($this->_where['params']) ? $this->_where['params'] : [];
        $fields = $this->_fields['keys'];
        $field_params = isset($this->_fields['params']) ? $this->_fields['params'] : [];
        $params = array_merge($field_params, $where_params);
        $sql = "UPDATE {$this->table} SET {$fields}  {$where}";
        if (!$this->query($sql, $params)->error()) {
            return true;
        }
        return false;
    }
    // build request siurce
    private function build()
    {
        switch ($this->action) {
            case 'insert':
                $this->insert();
                break;
            case 'update':
                $this->update();
            break;
            case 'delete':
                $this->delete();
            break;
        }
    }
    // return result after a request select
    function result(){
        $this->build();
        return $this->_results;
    }
    // return an error status when an error occure while doing an querry
    function error()
    {
        return $this->_error;
    }
    // retourn counted rows of a select querry
    function count()
    {
        return $this->_count;
    }
    
    function lastId()
    {
        return $this->_lastid;
    }
}