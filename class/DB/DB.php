<?php
class DB{
    private static $_instance = null;
    private $_pdo;  
    private $_lastid,$_error;       
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
    function get(string $table)
    {
        $this->_error = false;
        $sql = "SELECT * FROM {$table}";
        if ($_query = $this->_pdo->prepare($sql)) {
            if ($_query->execute()) {
                $this->_results = $_query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $this->_error = true;
            }
        }
        return $this;
    }
    
    function result()
    {
        return $this->_results;
    }
    function isfailed(){
        return $this->_error;
    }
    function lastId()
    {
        return $this->_lastid;
    }
}