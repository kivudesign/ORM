<?php
class DB{
    private static $_instance = null;
    private $_pdo;    
    private $_query,$sqlQUery; 
    private $option=[
        PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8",
        PDO::ATTR_EMULATE_PREPARES=>false,
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
    ]  ;  
    private function __construct()
    {
        try {
            $this->_pdo = new PDO("mysql:host=localhost;dbname=wepesi_db", "root", "",$this->option);
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
        $_get = new QueryParams($this->_pdo, $table, $actions);
        $this->_query = $_get;
        return $_get;
    }
    // select module
    function get(string $table)
    {
        if (strlen($table) < 1) {
            throw new Exception("table name should be a string");
        }
        $_get = new DBSelect($this->_pdo, $table);
        $this->sqlQUery = $_get;
        return $_get;
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
    // return the last id
    function lastId()
    {
        return $this->_query->lastId();
    }
    // return an error status when an error occure while doing an querry
    function error(){
        return isset($this->sqlQUery)?$this->sqlQUery->error():$this->_query->error();
    }    
}