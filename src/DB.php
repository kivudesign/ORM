<?php

namespace Wepesi\App;
use Exception;
use PDO;
class DB extends DB_Q
{
    private static ?DB $_instance = null;
    private $_query,
        $select_db_query;
    private bool $_error=false, $_results = false;
    private  int $_lastid;
    private PDO $pdo;
    private string $_action="";
    private array $option=[
        PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8",
        PDO::ATTR_EMULATE_PREPARES=>false,
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
    ]  ;
    private int $_count;

    private function __construct(string $host="",string $db_name="",string $user_name="",string $password="")
    {
        try {
            $this->pdo = new PDO("mysql:host=" . $host . ";dbname=" . $db_name.";charset=utf8mb4", $user_name,$password,$this->option);
            parent::__construct($this->pdo);
        } catch (\PDOException $ex) {
            die($ex->getMessage());
        }
    }
    static function getInstance(string $hot,string $db_name,string $user_name,string $password): ?DB
    {
        if(!isset(self::$_instance)){
            self::$_instance=new DB($hot,$db_name,$user_name,$password);
        }
        return self::$_instance;
    }

    /**
     * @throws Exception
     */
    private function queryOperation(string $table_name, string  $actions): ?QueryTransactions
    {
        if (strlen($table_name) < 1) {
            throw new \Exception("table name should be a string");
        }
        $this->_action=$actions;
        return new QueryTransactions($this->pdo, $table_name, $actions);
    }

    /**
     * @string :$table =>this is the name of the table where to get information
     * this method allow to do a select field from  a $table with all the conditions defined ;
     * @throws Exception
     */
    function get(string $table_name): DB_Select
    {
        return $this->select_option($table_name);
    }

    /**
     * @string :$table =>this is the name of the table where to get information
     * this method allow to do a count the number of field on a $table with all the possible condition
     * @throws Exception
     */
    function count(string $table_name): ?DB_Select
    {
        return $this->select_option($table_name, "count");
    }

    /**
     * @string : $table=> this is the name of the table where to get information
     * @string : @action=> this is the type of action tu do while want to do a request
     * @throws Exception
     */
    private function select_option(string $table_name, string $action = "select"): ?DB_Select
    {
        if (strlen($table_name) < 1) {
            throw new \Exception("table name should be a string");
        }
        return $this->select_db_query = new DB_Select($this->pdo, $table_name, $action);
    }

    /**
     * @param string $table : this is the name of the table where to get information
     * @return DB_Insert
     * @throws Exception
     *
     * this method will help create new row data
     */
    function insert(string $table): ?DB_Insert
    {
        return $this->_query=new DB_Insert($this->pdo, $table);
    }

    /**
     * @param string $table_name
     * @return DBCreateMultiple
     */
    // function insertMultiple(string  $table_name)
    // {
    //     return $this->_query =new DBCreateMultiple($this->_pdo, $table_name);
    // }

    /**
     * @param string $table :  this is the name of the table where to get information
     * @return QueryTransactions
     * @throws Exception
     * this method will help delete row data information
     */
    function delete(string $table): ?QueryTransactions
    {
        return $this->queryOperation($table, "delete");
    }
    //

    /**
     * @param string $table : this is the name of the table where to get information
     * @return QueryTransactions
     * @throws Exception
     * this methode will help update row informations of a selected tables
     */
    function update(string $table): ?QueryTransactions
    {
        return $this->_query = $this->queryOperation($table, "update");
    }
    //
    function query($sql, array $params = []): DB
    {
        $this->executeQuery($sql,$params);
        return $this;
    }
    // return the last id after an insert operation query
    function lastId()
    {
        return isset($this->_query) ? $this->_query->lastId() : $this->get_lastid();
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
            return $this->get_Error();
        }
    }
    function result()
    {
        return $this->get_result();
    }
    function rowCount(){
        return isset($this->_query) ? $this->_query->count() : $this->get_rowCount();
    }
}