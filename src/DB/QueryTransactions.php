<?php

namespace Wepesi\App;
class QueryTransactions
{
    private string $table, $action,$_error;
    private \PDO $_pdo;
    private array $_where, $_fields,$_results;
    private int $_count = 0, $_lastid;
    use DBWhere;
    function __construct(\PDO $pdo, string $table, string $action)
    {
        $this->table = $table;
        $this->action = $action;
        $this->_pdo = $pdo;
    }

    function where(array $where = [])
    {
        // select where <> update where
        $this->_where=$this->fnWhere($where)??[];
        return $this;

    }
    //
    function field(array $fields = [])
    {
        if (count($fields) && !$this->_fields && ($this->action != "insert" || $this->action != "update")) {
            $keys = $fields;
//                $values = null;
            $params = $keys;
            $x = 1;
            $keys = array_keys($fields);
            $values = null;
            $_trim_key=[];
            foreach ($fields as $field) {
                $values .= "? ";
                if ($x < count($fields)) {
                    $values .= ', ';
                }
                //remove white space around the collum name
                array_push($_trim_key,trim($keys[($x-1)]));
                $x++;
            }
            $keys=$_trim_key;
            $implode_keys= "`" . implode('`,`', $keys) . "`";
            if($this->action=="update"){
                $implode_keys= "`" . implode('`= ?,`', $keys) . "`";
                $implode_keys.="=?";
            }
            $this->_fields = [
                "keys" => $implode_keys,
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
        $q = new DBQeury($this->_pdo, $sql, $params);
        $this->_results = $q->result();
        $this->_count = $q->rowCount();
        $this->_error = $q->getError();
    }

    /**
     * @return bool
     * use this module to detele and existing row record
     */
    private function delete()
    {
        $where = $this->_where['field'] ?? null;
        $params = $this->_where['params'] ?? [];
        $sql = "DELETE FROM $this->table $where";
        if (!$this->query($sql, $params)->error()) {
            return true;
        }
        return false;
    }
    // update module
    private function update(){
        $where = $this->_where['field'] ?? null;
        $where_params = $this->_where['params'] ?? [];
        $fields = $this->_fields['keys'];
        $field_params= $this->_fields['params'] ?? [];
        $params=array_merge($field_params, $where_params);
        //generate the sql query to be execute
        $sql = "UPDATE $this->table SET $fields  $where";
        if (!$this->query($sql, $params)->error()) {
            return true;
        }
        return false;
    }

    /**
     *
     */
    private function build():void
    {
        switch ($this->action) {
            case 'update':
                $this->update();
                break;
            case 'delete':
                $this->delete();
                break;
            case 'count':
                $this->count();
                break;
        }
    }

    /**
     * @return bool
     * return result after a request select
     */
    function result()
    {
        $this->build();
        return $this->_results;
    }
    // return an error status when an error occure while doing an querry
    function error()
    {
        return $this->_error;
    }

    /**
     * @return int
     * return counted rows of a select query
     */
    function count()
    {
        return $this->_count;
    }
}