<?php
/**
 * Wepesi ORM
 * DB_Insert
 * Ibrahim Mussa
 * https://github.com/bim-g
 */
namespace Wepesi\App;

use Wepesi\App\Traits\BuildQuery;

class DB_Insert
{
    private string $table;
    private \PDO $pdo;
    private array $_fields;
    private string $_error;
    private array $_results;
    private int $lastID;
    use BuildQuery;

    function __construct(\PDO $pdo, string $table)
    {
        $this->table = $table;
        $this->pdo = $pdo;
        $this->_fields = [];
        $this->_results = [];
        $this->lastID = 0;
        $this->_error = '';
    }

    /**
     * @param array $fields
     * @return $this
     */
    function field(array $fields): DB_Insert
    {
        if (count($fields) && !$this->_fields) {
            $field_key_position = 0;
            $keys = array_keys($fields);
            $values = null;
            $trim_key = [];
            foreach ($fields as $field) {
                $values .= '? ';
                if (count($fields) > ($field_key_position + 1)) {
                    $values .= ', ';
                }
                //remove white space around the field name
                $trim_key[] = trim($keys[$field_key_position]);
                $field_key_position++;
            }

            $implode_keys = '`' . implode('`,`', $trim_key) . '`';

            $this->_fields = [
                'keys' => $implode_keys,
                'values' => $values,
                'params' => $fields
            ];
        }
        return $this;
    }

    /**
     * @param string $sql
     * @param array $params
     * @return void
     * this module is use to execute sql request
     */
    private function query(string $sql, array $params = [])
    {
        $q = $this->executeQuery($this->pdo, $sql, $params);
        $this->_results = $q['result'];
        $this->_error = $q['error']??"";
        $this->lastID = $q['lastID']??0;
    }

    /**
     *
     */
    private function insert()
    {
        $fields = $this->_fields['keys'];
        $values = $this->_fields['values'];
        $params = $this->_fields['params'];
        $sql = "INSERT INTO $this->table ($fields) VALUES ($values)";
        $this->query($sql, $params);
    }

    /**
     * @return array
     * return result after a request select
     */
    function result(): array
    {
        $this->insert();
        return $this->_results;
    }

    /**
     * @return string
     */
    function error(): string
    {
        return $this->_error;
    }

    /**
     * @return int
     */
    function lastId(): int
    {
        return $this->lastID;
    }
}