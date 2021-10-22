<?php

namespace Wepesi\App;
use PDO;
class DB
{
    private static $_instance = null;
    private $_pdo,
        $_query,
        $select_db_query,
        $_error=false,
        $_results = false,
        $_lastid;
    private $_action=null;
    private $option=[
        PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8",
        PDO::ATTR_EMULATE_PREPARES=>false,
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
    ]  ;
    private $_count;

    private function __construct()
    {
        try {
            $this->_pdo = new PDO("mysql:host=" . Config::get('mysql/host') . ";dbname=" . Config::get('mysql/db').";charset=utf8mb4", Config::get('mysql/username'), Config::get('mysql/password'),$this->option);
        } catch (\PDOException $ex) {
            die($ex->getMessage());
        }
    }
    static function getInstance(){
        if(!isset(self::$_instance)){
            self::$_instance=new DB();
        }
        return self::$_instance;
    }
    private function queryOperation(string $table_name,string  $actions)
    {
        if (strlen($table_name) < 1) {
            throw new \Exception("table name should be a string");
        }
        $this->_action=$actions;
        return new QueryTransactions($this->_pdo, $table_name, $actions);
    }
    /**
     * @string :$table =>this is the name of the table where to get information
     * this method allow to do a select field from  a $table with all the conditions defined ;
     */
    function get(string $table_name)
    {
        return $this->select_option($table_name);
    }
    /**
     * @string :$table =>this is the name of the table where to get information
     * this method allow to do a count the number of field on a $table with all the possible condition
     */
    function count(string $table_name)
    {
        return $this->select_option($table_name, "count");
    }
    /**
     * @string : $table=> this is the name of the table where to get information
     * @string : @action=> this is the type of action tu do while want to do a request
     */
    private function select_option(string $table_name, string $action = "select")
    {
        if (strlen($table_name) < 1) {
            throw new \Exception("table name should be a string");
        }
        return $this->select_db_query = new DB_Select($this->_pdo, $table_name, $action);
    }

    /**
     * @param string $table : this is the name of the table where to get information
     * @return QueryTransactions
     * @throws \Exception
     *
     * this method will help create new row data
     */
    function insert(string $table)
    {
        return $this->_query=new DB_Insert($this->_pdo, $table);
    }

    /**
     * @param string $table_name
     * @return DBCreateMultiple
     */
    function insertMultiple(string  $table_name)
    {
        return $this->_query =new DBCreateMultiple($this->_pdo, $table_name);
    }

    /**
     * @param string $table :  this is the name of the table where to get information
     * @return QueryTransactions
     * @throws \Exception
     * this method will help delete row data information
     */
    function delete(string $table)
    {
        return $this->queryOperation($table, "delete");
    }
    //

    /**
     * @param string $table : this is the name of the table where to get information
     * @return QueryTransactions
     * @throws \Exception
     * this methode will help update row informations of a selected tables
     */
    function update(string $table)
    {
        return $this->_query = $this->queryOperation($table, "update");
    }
    //
    function query($sql, array $params = []): DB
    {
        $q = new DB_Qeury($this->_pdo, $sql, $params);
        $this->_results = $q->result();
        $this->_count = $q->rowCount();
        $this->_error = $q->getError();
        $this->_lastid = $q->lastId();
        return $this;
    }
    // return the last id after an insert operation query
    function lastId()
    {
        return isset($this->_query) ? $this->_query->lastId() : $this->_lastid;
    }
    /**
     * return an error status when an error occur while doing an query
     */
    function error()
    {
        /** check if it was a select operation and if an error occur then return it
         */
        if(isset($this->select_db_query) ){
            return $this->select_db_query->error();
        }
        /** check if if was an (insert, update, select) method was called, and return if an error occur
         */
        else if(isset($this->_query)){
            return $this->_query->error();
        }else{
            /** if it was written query, it will return the error, if it exist.
            // otherwise, it will return false
             */
            return $this->_error;
        }
    }
    function result()
    {
        return $this->_results;
    }
    function rowCount(){
        return isset($this->_query) ? $this->_query->count() : $this->_count;
    }
}