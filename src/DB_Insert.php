<?php
/**
 * Wepesi ORM
 * DB_Insert
 * Ibrahim Mussa
 * https://github.com/bim-g
 */

namespace Wepesi\App;

use Wepesi\App\Provider\DbProvider;

/**
 *
 */
class DB_Insert extends DbProvider
{
    /**
     * @var string
     */
    private string $table;

    /**
     * @var array
     */
    private array $_fields;

    /**
     * @param \PDO $pdo
     * @param string $table
     */
    public function __construct(\PDO $pdo, string $table)
    {
        $this->table = $table;
        $this->pdo = $pdo;
        $this->_fields = [];
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function field(array $fields): DB_Insert
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
     * @return array
     * return result after a request select
     */
    public function result(): array
    {
        $this->insert();
        return $this->result;
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
     * @return int
     */
    public function lastId(): int
    {
        return $this->lastID;
    }
}