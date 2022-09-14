<?php
/**
 * Wepesi ORM
 * DB
 * Ibrahim Mussa
 * https://github.com/bim-g
 */
namespace Wepesi\App;
use Exception;
use Wepesi\App\Traits\BuildQuery;

class DB
{
    private static ?DB $_instance = null;
    private $_query,
        $query_transaction;
    private ?string $_error;
    private array $_results;
    private  int $_lastID;
    private \PDO $pdo;
    private string $_action="";
    private int $_count;
    private string $db_name;
    use BuildQuery;
    private function __construct(string $host="",string $db_name="",string $user_name="",string $password="")
    {
        try {
            $this->_results = [];
            $this->_error = null;
            $this->_lastID = -1;
            $this->_count = 0;
            $this->db_name = $db_name;
            //
            $this->pdo = new \PDO("mysql:host=" . $host . ";dbname=" . $db_name.";charset=utf8mb4", $user_name,$password);
            $this->pdo->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND,'SET NAMES utf8');
            $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES,false);
            $this->pdo->setAttribute(\PDO::ATTR_PERSISTENT,true);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $ex) {
            echo $ex->getMessage();
            die();
        }
    }

    /**
     * @param array $config this config will store the database configuration, the host, database name, username and password.
     * @return DB|null
     */
    static function getInstance(array $config): ?DB
    {
        try{
            if(!isset($config['host']) || !$config['host']) throw new \Exception("host config params is not defined");
            if(!isset($config['db_name']) || !$config['db_name']) throw new \Exception("db_name config params does not exist or is not set");
            if(!isset($config['username']) || !$config['username']) throw new \Exception("database username config params does not exist or is not set");
            if(!isset($config['password']) ) throw new \Exception("database password config params does not exist or is not set");

            $hot=$config["host"];
            $db_name=$config["db_name"];
            $user_name=$config["username"];
            $password=$config["password"];

            if(!isset(self::$_instance)){
                self::$_instance=new DB($hot,$db_name,$user_name,$password);
            }
            return self::$_instance;

        }catch (\Exception $ex){
            print_r(["exception"=>$ex->getMessage()]);
            die();
        }
    }

    /**
     * @param string $table_name
     * @return DB_Select
     * @throws Exception
     */
    function get(string $table_name): DB_Select
    {
        return $this->select_option($table_name);
    }

    /**
     * @param string $table_name
     * @return DB_Select
     * @throws Exception
     */
    function count(string $table_name): DB_Select
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
        $this->query_transaction = new DB_Select($this->pdo, $table_name, $action);
        return $this->query_transaction;
    }

    /**
     * @param string $table : this is the name of the table where to get information
     * @return DB_Insert
     *
     * this method will help create new row data
     */
    function insert(string $table): DB_Insert
    {
        $this->query_transaction = new DB_Insert($this->pdo, $table);
        return $this->query_transaction;
    }

    /**
     * @param string $table :  this is the name of the table where to get information
     * @return DB_Delete
     */
    function delete(string $table): DB_Delete
    {
        $this->query_transaction= new DB_Delete();
        return $this->query_transaction;
    }
    //

    /**
     * @param string $table : this is the name of the table where to get information
     * @return DB_Update
     */
    function update(string $table): DB_Update
    {
        $this->query_transaction = new DB_Update();
        return $this->query_transaction;
    }
    //
    function query($sql, array $params = []): DB
    {
        $q = $this->executeQuery($this->pdo,$sql,$params);
        $this->_results = $q['result'];
        $this->_count = $q['count']??0;
        $this->_error = $q['error'];
        $this->_lastID = $q['lastID']??-1;

        return $this;
    }

    /**
     * @return int
     */
    function lastId(): int
    {
        if (isset($this->query_transaction) && method_exists($this->query_transaction, 'lastId')) {
            $this->_lastID = $this->query_transaction->lastId();
        }
        return $this->_lastID;
    }
    /**
     * return an error status when an error occur while doing an query
     */
    function error()
    {
        if(isset($this->query_transaction) ){
            $this->_error = $this->query_transaction->error();
        }
        return $this->_error;
    }

    /**
     * @return array
     */
    function result(): array
    {
        return $this->_results;
    }
    function rowCount(){
        if(isset($this->query_transaction) && method_exists($this->query_transaction,"count") ){
            $this->_count = $this->query_transaction->count();
        }
        return $this->_count;
    }

    function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }
    function commit(){
        return $this->pdo->commit();
    }
    function rollBack(){
        return $this->pdo->rollBack();
    }

    /**
     * @throws Exception
     */
    function transaction(\Closure $callable){
        try{
            $this->pdo->beginTransaction();
            $callable($this);
            $this->pdo->commit();
        }catch (\Exception $ex){
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $ex;
        }
    }

    /**
     * @throws Exception
     */
    function convertToInnoDB(){
        try {
            $params =[$this->db_name,"MyISAM"];
            $sql = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = ? AND `ENGINE` = ?";
            $result  = self::query($sql,$params)->result();
            foreach ($result as $table){
                $sql = "ALTER TABLE $table->TABLE_NAME ENGINE=InnoDB";
                $this->query($sql);
            }
        }catch (\Exception $ex){
            throw $ex;
        }
    }
}