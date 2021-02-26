<?php
class DB{
    private static $_instance = null;
    private $_pdo,$_results,$_error,$_lastid;    
    private $_query,$sqlQUery; 
    private $option=[
        PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8",
        PDO::ATTR_EMULATE_PREPARES=>false,
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
    ]  ;  
    private function __construct()
    {
        try {
            $this->_pdo = new PDO("mysql:host=" . Config::get('mysql/host') . ";dbname=" . Config::get('mysql/db'), Config::get('mysql/username'), Config::get('mysql/password'), $this->option);
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new DB();
        }
        return self::$_instance;
    }
    private function queryOperation($table, $actions)
    {
        if (strlen($table) < 1) {
            throw new Exception("table name should be a string");
        }
        $_get = new DB_Query($this->_pdo, $table, $actions);
        $this->_query = $_get;
        return $_get;
    }
    // select module
    function get(string $table)
    {
        if (strlen($table) < 1) {
            throw new Exception("table name should be a string");
        }
        $_get = new DB_Select($this->_pdo, $table);
        $this->sqlQUery = $_get;
        return $_get;
    }
    function count(string $table)
    {
        if (strlen($table) < 1) {
            throw new Exception("table name should be a string");
        }        
        return  new DB_Select($this->_pdo, $table,"count");
    }
    // insert module
    function insert(string $table)
    {
        return $this->queryOperation($table, "insert");
    }
    // delete module
    function delete(string $table)
    {
        return $this->queryOperation($table, "delete");
    }
    // udate module
    function update(string $table)
    {
        return $this->queryOperation($table, "update");
    }
    //
    function query($sql, array $params = []){
        $q = new DB_Exec_Qeury($this->_pdo, $sql, $params);
        $this->_results = $q->result();
        $this->_count = $q->rowCount();
        $this->_error = $q->getError();
        $this->_lastid = $q->lastId();
        return $this;
    }
    // return the last id
    function lastId()
    {
        return isset($this->_query)?$this->_query->lastId():$this->_lastid;
    }
    // return an error status when an error occure while doing an querry
    function error(){
        $_error= isset($this->sqlQUery) ? $this->sqlQUery->error():$this->_error;
        $_error= isset($this->_query) ? $this->_query->error():$this->_error;
        return $_error;
    }
    function result(){
        return $this->_results;
    }
    // count result after delete or update
    // function count(){
    //     return $this->_query->count();
    // }
}