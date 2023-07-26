<?php
/**
 * Wepesi ORM
 * DB
 * Ibrahim Mussa
 * https://github.com/bim-g
 */

namespace Wepesi\App;

use Wepesi\App\Traits\BuildQuery;

/**
 *
 */
class DB
{
    /**
     * @var DB|null
     */
    private static ?DB $_instance = null;
    /**
     * @var string|null
     */
    private ?string $_query;
    /**
     * @var
     */
    private $query_transaction;
    /**
     * @var string|null
     */
    private ?string $_error;
    /**
     * @var array
     */
    private array $_results;
    /**
     * @var int
     */
    private int $_lastID;
    /**
     * @var \PDO
     */
    private \PDO $pdo;
    /**
     * @var string
     */
    private string $_action = "";
    /**
     * @var int
     */
    private int $_count;
    /**
     * @var string
     */
    private string $db_name;
    use BuildQuery;

    /**
     * @param string $host
     * @param string $db_name
     * @param string $user_name
     * @param string $password
     */
    private function __construct(string $host = "", string $db_name = "", string $user_name = "", string $password = "")
    {
        try {
            $this->_results = [];
            $this->_error = null;
            $this->_lastID = -1;
            $this->_count = 0;
            $this->db_name = $db_name;
            //
            $this->pdo = new \PDO("mysql:host=" . $host . ";dbname=" . $db_name . ";charset=utf8mb4", $user_name, $password);
            $this->pdo->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
            $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->pdo->setAttribute(\PDO::ATTR_PERSISTENT, true);
            $this->pdo->setAttribute(\PDO::MYSQL_ATTR_FOUND_ROWS, true);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $ex) {
            echo $ex->getMessage();
            die();
        }
    }

    /**
     * @param array $config this config will store the database configuration, the host, database name, username and password.
     * @return DB|null
     */
    public static function getInstance(array $config): ?DB
    {
        try {
            if (!isset($config['host']) || !$config['host']) throw new \Exception("host config params is not defined");
            if (!isset($config['db_name']) || !$config['db_name']) throw new \Exception("db_name config params does not exist or is not set");
            if (!isset($config['username']) || !$config['username']) throw new \Exception("database username config params does not exist or is not set");
            if (!isset($config['password'])) throw new \Exception("database password config params does not exist or is not set");

            $hot = $config["host"];
            $db_name = $config["db_name"];
            $user_name = $config["username"];
            $password = $config["password"];

            if (!isset(self::$_instance)) {
                self::$_instance = new DB($hot, $db_name, $user_name, $password);
            }
            return self::$_instance;

        } catch (\Exception $ex) {
            print_r(["exception" => $ex->getMessage()]);
            die();
        }
    }

    /**
     * @param string $table_name
     * @return DB_Select
     * @throws \Exception
     */
    public function get(string $table_name): DB_Select
    {
        return $this->select_option($table_name);
    }

    /**
     * @param string $table_name table name of the table where to get information
     * @param string $action action this is the type of action tu do while want to do a request
     * @throws \Exception
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
     * @param string $table this is the name of the table where to get information
     * @return DB_Insert
     *
     * this method will help create new row data
     */
    public function insert(string $table): DB_Insert
    {
        $this->query_transaction = new DB_Insert($this->pdo, $table);
        return $this->query_transaction;
    }

    /**
     * @param string $table this is the name of the table where to get information
     * @return DB_Delete
     */
    public function delete(string $table): DB_Delete
    {
        $this->query_transaction = new DB_Delete($this->pdo, $table);
        return $this->query_transaction;
    }

    /**
     * @param string $table this is the name of the table where to get information
     * @return DB_Update
     */
    public function update(string $table): DB_Update
    {
        $this->query_transaction = new DB_Update($this->pdo, $table);
        return $this->query_transaction;
    }
    //

    /**
     * @return int
     * get the last id after insert new record
     */
    public function lastId(): int
    {
        if (isset($this->query_transaction) && method_exists($this->query_transaction, 'lastId')) {
            $this->_lastID = $this->query_transaction->lastId();
        }
        return $this->_lastID;
    }

    //

    /**
     * @return string|null
     * return an error status when an error occur while doing a query
     */
    public function error(): ?string
    {
        if (isset($this->query_transaction)) {
            $this->_error = $this->query_transaction->error();
        }
        return $this->_error;
    }

    /**
     * @return int
     * return total of rows selected
     */
    public function rowCount()
    {
//        var_dump($this->_count);
//        if (isset($this->query_transaction) && method_exists($this->query_transaction, "count")) {
//        }
        return $this->query_transaction->count();
//        return $this->_count;
    }

    /**
     * @param string $table_name
     * @return DB_Select
     * @throws \Exception
     */
    public function count(string $table_name): DB_Select
    {
        return $this->select_option($table_name, "count");
    }

    /**
     * @throws \Exception
     * implement transaction with callback function to manage all at once
     */
    public function transaction(\Closure $callable)
    {
        try {
            $this->pdo->beginTransaction();
            $callable($this);
            $this->pdo->commit();
        } catch (\Exception $ex) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $ex;
        }
    }

    /**
     * @return bool
     * start a transaction
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * @return bool
     * validate a transaction when success query
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * @return bool
     * cancel a transaction when query fall
     */
    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    /**
     * @throws \Exception
     * convert your database  ENGINE to MyISAM in case you want your database to support transactions.
     */
    public function convertMyISAMToInnoDB()
    {
        try {
            $params = [$this->db_name, "MyISAM"];
            $sql = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = ? AND `ENGINE` = ?";
            $result = self::query($sql, $params)->result();
            foreach ($result as $table) {
                $params = ["InnoDB"];
                $sql = "ALTER TABLE $table->TABLE_NAME ENGINE = ?";
                $this->query($sql, $params);
            }
        } catch (\Exception $ex) {
            print_r($ex);
        }
    }

    /**
     * @return array
     */
    public function result(): array
    {
        return $this->_results;
    }

    /**
     * @param $sql
     * @param array $params
     * @return $this
     */
    public function query($sql, array $params = []): DB
    {
        $q = $this->executeQuery($this->pdo, $sql, $params);
        $this->_results = $q['result'];
        $this->_count = $q['count'] ?? 0;
        $this->_error = $q['error'];
        $this->_lastID = $q['lastID'] ?? -1;

        return $this;
    }
}