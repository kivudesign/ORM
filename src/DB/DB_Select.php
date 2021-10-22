<?php

namespace Wepesi\App;
class DB_Select
{
    private  $table, $action,$_leftJoin,$_rightJoin,$_error,$_dsc,$_asc,$_join,$orderBy,$groupBY;
    private \PDO $_pdo;
    private  $_where=[],
        $_fields=[],$_results=[];
    private  $_count = 0,
        $_limit=null,
        $_offset=null;
    private $_join_comparison_sign=["=",">","<","!=","<>"];
    /**
     *
     * @param type $pdo
     * @param string $table
     * @param string $action
     */
    function __construct(\PDO $pdo, string $table, string $action="select")
    {
        $this->table = $table;
        $this->_pdo = $pdo;
        $this->action=$action;
//        $this->initInut();
    }
    private function initInut(){
       $this->table="";$this->action="";$this->_leftJoin="";$this->_rightJoin="";$this->_error="";$this->_dsc="";$this->_asc="";$this->_join="";
       $this->orderBy="";$this->groupBY="";
    }
    /**
     *
     * @param array $params
     * @return $this
     * @throws Exception
     */
    function where(array $params = []){
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
        if (count($params)) {
            // $params = [];
            //
            /**
             * defined comparison operator to avoid error while Passing operation witch does not exist
             */
            $comparisonOperator = ["<", "<=", ">", ">=", "<>", "!=","like"];
            // defined logical operator
            $logicalOperator = ["or", "not"];
            // chech if the array is multidimensional array
            $key = array_keys($params);
            $key_exist = is_string($key[0]);
            if ($key_exist) {
                throw new \Exception("bad format, for where data");
            }
            $where = is_array($params[0]) ? $params : [$params];
            $whereLen = count($where);
            //
            $jointuresWhereCondition = "";
            $defaultComparison = "=";
            $lastIndexWhere = 1;
            $fieldValue = [];
            //
            foreach ($where as $WhereField) {
                $defaultLogical = " AND ";
                $notComparison = null;
                // check if there is a logical operator `or`||`and`
                if (isset($WhereField[3])) {
                    // check id the defined operation exist in our defined tables
                    $defaultLogical = in_array(strtolower($WhereField[3]), $logicalOperator) ? $WhereField[3] : " and ";
                    if ($defaultLogical === "not") {
                        $notComparison = " not ";
                    }
                }
                // check the field exist and defined by default one
                $_WhereField = strlen($WhereField[0]) > 0 ? $WhereField[0] : "id";
                // check if comparison  exist on the array
                $defaultComparison = in_array($WhereField[1], $comparisonOperator) ? $WhereField[1] : "=";
                $jointuresWhereCondition .= " {$notComparison} {$_WhereField} {$defaultComparison}  ? ";
                $valueTopush = isset($WhereField[2]) ? $WhereField[2] : null;
                array_push($fieldValue, $valueTopush);
                if ($lastIndexWhere < $whereLen) {
                    if ($defaultLogical != "not") {
                        $jointuresWhereCondition .= $defaultLogical;
                    }
                }
                $lastIndexWhere++;
            }
            $this->_where = [
                "field" => " WHERE {$jointuresWhereCondition} ",
                "value" => $fieldValue
            ];
        }
        return $this;

    }
    /**
     *
     * @param array $fields
     * @return $this
     */
    function field(array $fields = [])
    {
        if (count($fields)) {
            $keys = $fields;
            $values = null;
            $this->_fields = [
                "keys" => implode(',', $keys),
                "values" => $values
            ];
        } else {
            $this->_fields = "*";
        }
        return $this;
    }
    /**
     *
     * @param array $group
     * @return $this
     */
    function groupBY(array $group = [])
    {
//        if (count($group))  $this->groupBY = "group by field";
//        return $this;
    }
    /**
     *
     * @param string $order
     * @return $this
     */
    function orderBy(string $order = null)
    {
        if ($order) $this->orderBy = " ORDER BY ($order)";
        return $this;
    }
    /**
     *
     * @return $this
     */
    function ASC()
    {
        if ($this->orderBy) {
            $this->_asc = " ASC";
            $this->_dsc = null;
        }
        return $this;
    }
    /**
     *
     * @return $this
     */
    function DESC()
    {
        if ($this->orderBy) {
            $this->_dsc = " DESC";
            $this->_asc = null;
        }
        return $this;
    }
    /**
     *
     * @param int $limit
     * @return $this
     */
    function limit(int $limit)
    {
        $this->_limit = " LIMIT {$limit}";
        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    function offset(int $offset=0)
    {
        $this->_offset = " OFFSET {$offset}";
        return $this;
    }

    /**
     * @param string $table_name
     * @param array $onParameters : this represent the field
     * @return $this|false
     * this module allow to do a single left join, in case of multiple join, it's recommend use to use the `query` method with is better
     */
    function leftJoin(string $table_name,array $onParameters=[]){
        $on=null;
        if(count($onParameters)==3) {
            $_field1=$onParameters[0];
            $_operator=$onParameters[1];
            $_field2=$onParameters[2];
            if(in_array($_operator,$this->_join_comparison_sign))$on=" ON {$_field1}{$_operator}{$_field2} ";
        }
        if(!$table_name)return  false;
        $this->_leftJoin=" LEFT {$table_name} {$on} ";
//        return $this;
    }

    /**
     * @param string $table_name
     * @param array $onParameters
     * @return $this|false
     */
    function rightJoin(string $table_name,array $onParameters=[]){
        try{
            $on=null;
            if(count($onParameters)==3) {
                $_field1=$onParameters[0];
                $_operator=$onParameters[1];
                $_field2=$onParameters[2];
                if(in_array($_operator,$this->_join_comparison_sign))$on=" ON {$_field1}{$_operator}{$_field2} ";
            }
            if(!$table_name)return  false;
            $this->_leftJoin=" RIGHT {$table_name} {$on} ";
//            return $this;
        }catch (\Exception $ex){
            $this->_error = true;
        }
    }
    /**
     * @param string $table_name
     * @param array $onParameters
     * @return $this|false
     * this module wil help only join single(one) table
     * for multiple join better to use query method for better perfomances
     */
    function join(string $table_name,array $onParameters=[]){
        try{
            $on=null;
            if(count($onParameters)==3) {
                $_field1=$onParameters[0];
                $_operator=$onParameters[1];
                $_field2=$onParameters[2];
                if(in_array($_operator,$this->_join_comparison_sign))$on=" ON {$_field1}{$_operator}{$_field2} ";
            }
            if(!$table_name)return  false;
            $this->_leftJoin=" JOIN {$table_name} {$on} ";
//            return $this;
        }catch (\Exception $ex){
            $this->_error = true;
        }
    }
    /**
     *
     * @return type
     */
    private function select()
    {
        $fields = isset($this->_fields['keys']) ? $this->_fields['keys'] : "*";
        //
        $WHERE = isset($this->_where['field']) ? $this->_where['field'] : "";
        $params = isset($this->_where['value']) ? $this->_where['value'] : [];
        //
        $sortedASC_DESC = $this->_asc ? $this->_asc : ($this->_dsc ? $this->_dsc : null);
        $_jointure="";
        //
        $sql = "SELECT {$fields} FROM {$this->table} {$_jointure} " . $WHERE . $this->groupBY . $this->orderBy . $sortedASC_DESC . $this->_limit . $this->_offset;
        var_dump($sql);
        return $this->query($sql, $params);
    }
    /**
     *
     * @return type
     */
    private function Totalcount(){
        $WHERE = isset($this->_where['field']) ? $this->_where['field'] : "";
        $params = isset($this->_where['value']) ? $this->_where['value'] : [];
        $sql ="SELECT COUNT(*) as count FROM {$this->table} ". $WHERE;
        return $this->query($sql, $params);
    }
    /**
     *
     * @param string $sql
     * @param array $params
     */
    private function query(string $sql, array $params = [])
    {
        $q = new DB_Qeury($this->_pdo, $sql, $params);
        $this->_results = $q->result();
        $this->_count = $q->rowCount();
        $this->_error = $q->getError();
    }
    /**
     *
     */
    private function build(){
        if($this->action && $this->action=="count"){
            $this->Totalcount();
        }else{
            $this->select();
        }
    }
    /**
     *
     * @return object
     * execute query to get result
     */
    function result()
    {
        $this->build();
        return $this->action=="count"?$this->_results[0]->count:$this->_results;
    }
    /**
     *
     * @return type
     * return an error status when an error occur while doing an query
     */
    function error()
    {
        return $this->_error;
    }
}