<?php
class QueryParams{
    private $table, $action, $_pdo;
    private $_where, $_fields, $orderBy, $groupBY;
    private  $_error,
        $_results = false,
        $_count = 0,
        $_limit,
        $_offset,
        $_dsc,
        $_asc, $_lastid;
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
    private function query($sql, array $params = [])
    {
        $this->_error = false;
        if ($_query = $this->_pdo->prepare($sql)) {
            $x = 1;
            if (count($params)) {
                foreach ($params as $param) {
                    $_query->bindValue($x, $param);
                    $x++;
                }
            }
            if ($_query->execute()) {
                if (strchr($sql, "UDPATE")) {
                    $this->_count = $_query->rowCount();
                } else if (strchr($sql, "INSERT INTO")) {
                    $this->_lastid = $this->_pdo->lastInsertId();
                }
            } else {
                $this->_error = true;
            }
        }
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
        return  $this->query($sql, $params);
    }
    // build request siurce
    private function build()
    {
        switch ($this->action) {
            case 'insert':
                $this->insert();
                break;
            case 'update':
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