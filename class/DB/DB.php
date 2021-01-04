<?php
class DB{
    private static $_instance = null;
    private $_pdo;         
    private function __construct()
    {
        try {
            $this->_pdo = new PDO("mysql:host=localhost;dbname=wepesi_db", "root", "");
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
        $_get = new QueryParams($this->_pdo, $table, $actions);
        $this->_query = $_get;
        return $_get;
    }
    function get(string $table)
    {
        if (strlen($table) < 1) {
            throw new Exception("table name should be a string");
        }
        return $this->queryOperation($table, "select");
    }
    
}