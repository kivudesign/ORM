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

    /**
     * @string :$table =>this is the name of the table where to get information
     * this method allow to do a select field from  a $table with all the conditions defined ;
     */
    function get(string $table)
    {
        return $this->select_option($table);
    }
    /**
     * @string :$table =>this is the name of the table where to get information
     * this method allow to do a count the number of field on a $table with all the possible condition
     */
    function count(string $table)
    {
        return $this->select_option($table,"count");
    }
    /**
     * @string : $table=> this is the name of the table where to get information
     * @string : @action=> this is the type of action tu do while want to do a request
     */
    private function select_option(string $table,string $action=null){
        if (strlen($table) < 1) {
            throw new Exception("table name should be a string");
        }
        return $this->sqlQUery = new DB_Select($this->_pdo, $table, $action);
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
    function rowCount(){
        return $this->_query->rowCount();
    }
}