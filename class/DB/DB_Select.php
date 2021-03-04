<?php

class DB_Select{
    private $table, $action, $_pdo;
    private $_where, $_fields, $orderBy, $groupBY;
    private  $_error,
        $_results = false,
        $_count = 0,
        $_limit,
        $_offset,
        $_dsc,
        $_asc;

    function __construct($pdo,string $table,string $action=null)
    {
        $this->table = $table;
        $this->_pdo = $pdo;
        $this->_asc = null;
        $this->_dsc = null;
        $this->_limit = null;
        $this->_offset = null;
        $this->_error = false;
        $this->where=false;
        $this->action=$action;
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
            if (count($params) && !$this->_where) {            
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
    // 
    private function countTotal(){
        $WHERE = isset($this->_where['field']) ? $this->_where['field'] : "";
        $params = isset($this->_where['value']) ? $this->_where['value'] : [];
        $sql="SELECT COUNT(*) as count FROM {$this->table}" . $WHERE;
        return $this->query($sql,$params);
    }
    // 
    private function query($sql, array $params = [])
    {
        $q= new DB_Exec_Qeury($this->_pdo,$sql,$params);
        $this->_results=$q->result();
        $this->_count=$q->rowCount();
        $this->_error=$q->getError();
        return $this;
    }
    // build request
    private function build(){
        if($this->action && $this->action=="count"){
            $this->countTotal();
        }else{
            $this->select();
        }
    }
    // execute query to get result
    function result()
    {
        $this->build();
        if($this->action && $this->action=="count"){
            return $this->_results[0]->count;
        }else{
            return $this->_results;
        }
    }
    // return an error status when an error occure while doing an querry
    function error()
    {
        return $this->_error;
    }
    // retourn counted rows of a select querry
    function rowCount()
    {
        return $this->_count;
    }
}