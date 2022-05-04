<?php

namespace Wepesi\App;
class QueryTransactions extends DB_Q
{
    private string $table, $action,$_error;
    private \PDO $pdo;
    private array $_where, $_fields,$_results;
    private int $_count = 0, $_lastid;
    use DBWhere,DBField;
    function __construct(\PDO $pdo, string $table, string $action)
    {
        $this->table = $table;
        $this->action = $action;
        parent::__construct($pdo);
    }

    /**
     * @param array $where
     * @return $this
     */
    function where(array $where = []): QueryTransactions
    {
        $this->_where=$this->condition($where)??[];
        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    function field(array $fields = []): QueryTransactions
    {
        $result=$this->field_params($fields,$this->action);
        if(is_array($result)){
            $this->_fields = $result;
        }else{
            echo ("This method try to access undefined method");
        }
        return $this;
    }

    /**
     * @param $sql
     * @param array $params
     * @return void
     * this module is use to execute sql request
     */
    private function query($sql, array $params = [])
    {
        $this->executeQuery($sql,$params);
    }

    /**
     * @return void
     * use this module to delete and existing row record
     */
    private function delete()
    {
        $where = $this->_where['field'] ?? null;
        $params = $this->_where['params'] ?? [];
        $sql = "DELETE FROM $this->table $where";
        var_dump($sql,$params);
        $this->query($sql, $params);
    }

    /**
     *
     */
    private function update(){
        $where = $this->_where['field'] ?? null;
        $where_params = $this->_where['params'] ?? [];
        $fields = $this->_fields['fields'];
        $field_params= $this->_fields['params'] ?? [];

        $params=array_merge($field_params, $where_params);

        $sql = "UPDATE $this->table SET $fields  $where";

        $this->query($sql, $params);
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
     * @return array|null
     * return result after a request select
     */
    function result()
    {
        $this->build();
        return $this->get_result();
    }
    // return an error status when an error occure while doing an querry
    function error()
    {
        return $this->get_Error();
    }

    /**
     * @return int
     * return counted rows of a select query
     */
    function count()
    {
        return $this->get_rowCount();
    }
}