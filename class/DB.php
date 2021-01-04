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
}