<?php

class DBSelect{
    private $table, $action, $_pdo;
    private $_where, $_fields, $orderBy, $groupBY;
    private  $_error,
        $_results = false,
        $_count = 0,
        $_limit,
        $_offset,
        $_dsc,
        $_asc;

    function __construct($pdo, $table)
    {
        $this->table = $table;
        $this->_pdo = $pdo;
        $this->_asc = null;
        $this->_dsc = null;
        $this->_limit = null;
        $this->_offset = null;
        $this->_error = false;
    }
    function where(array $params = [])
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
            if (count($params)) {            
                // $params = [];
                // defined comparion operator to avoid error while assing operation witch does not exist
                $comparisonOperator = ["<", "<=", ">", ">=", "<>", "!="];
                // defined logical operator
                $logicalOperator = ["or", "not"];
                // chech if the array is multidimensional array
                $key = array_keys($params);
                $key_exist = is_string($key[0]);
                if($key_exist){
                    throw new Exception("bad format, for where data");
                }
                $where = is_array($params[0]) ? $params : [$params];
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
                    // check if comparison  existe on the array
                    $defaultComparison = in_array($WhereField[1], $comparisonOperator) ? $WhereField[1] : "=";                
                    $jointureWhereCondition .= " {$notComparison}{$_WhereField}{$defaultComparison} ? ";
                    $valueTopush = isset($WhereField[2]) ? $WhereField[2] : "";
                    array_push($fieldValue, $valueTopush);
                    if ($lastIndexWhere < $whereLen) {
                        if ($defaultLogical != "not") {
                            $jointureWhereCondition .= $defaultLogical;
                        }
                    }
                    $lastIndexWhere++;
                }
                $this->_where = [
                    "field" => " WHERE {$jointureWhereCondition} ",
                    "value" => $fieldValue
                ];
            } 
            return $this;
        }catch(Exception $ex){
            echo $ex->getmessage();
            $this->_error=true;
        }
    }
    function fields(array $fields = [])
    {
        if (count($fields)) {
            $keys = $fields;
            $values = null;
            $params = $keys;
            $x = 1;
            if ($this->action == "insert") {
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
            $this->_fields = [
                "keys" => "`" . implode('`,`', $keys) . "`",
                "values" => $values
            ];
        } else {
            $this->_fields = "*";
        }
        return $this;
    }
    function groupBY(array $group = [])
    {
        if (count($group)) {
            $this->groupBY = "group by field";
        } else {
            $this->groupBY = null;
        }
        return $this;
    }
    function orderBy(string $order = null)
    {
        if ($order) {
            $this->orderBy = "order by ($order)";
        } else {
            $this->orderBy = null;
        }
        return $this;
    }
    function ASC()
    {
        if ($this->orderBy) {
            $this->_asc = " ASC";
            $this->_dsc = null;
        }
        return $this;
    }
    function DESC()
    {
        if ($this->orderBy) {
            $this->_asc = null;
            $this->_dsc = " DESC";
        }
        return $this;
    }
    function LIMIT(int $limit)
    {
        $this->_limit = " LIMIT {$limit}";
        return $this;
    }
    function OFFSET(int $offset)
    {
        $this->_offset = " OFFSET {$offset}";
        return $this;
    }
    private function select()
    {
        $fields = isset($this->_fields['keys']) ? $this->_fields['keys'] : "*";
        // 
        $WHERE = isset($this->_where['field']) ? $this->_where['field'] : "";
        $params = isset($this->_where['value']) ? $this->_where['value'] : [];
        // 
        $sortedASC_DESC = $this->_asc ? $this->_asc : ($this->_dsc ? $this->_dsc : null);
        // 
        $sql = "SELECT {$fields} FROM {$this->table}".$WHERE.$this->groupBY . $this->orderBy . $sortedASC_DESC . $this->_limit . $this->_offset;
        return $this->query($sql, $params);
    }
    private function query($sql, array $params = [])
    {
        if ($_query = $this->_pdo->prepare($sql)) {
            $x = 1;
            if (count($params)) {
                foreach ($params as $param) {
                    $_query->bindValue($x, $param);
                    $x++;
                }
            }
            if ($_query->execute()) {               
                $this->_results = $_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $_query->rowCount();                
            } else {
                $this->_error = true;
            }
        }
        return $this;
    }
    // execute query to get result
    function result()
    {
        $this->select();
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
}