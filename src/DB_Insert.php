<?php
/**
 * Wepesi ORM
 * DB_Insert
 * Ibrahim Mussa
 * https://github.com/bim-g
 */


namespace Wepesi\App;

use PDO;
use Wepesi\App\Provider\DbProvider;

/**
 *
 */
class DB_Insert extends DbProvider
{
    /**
     * @var array
     */
    private array $_fields;
    /**
     * @var bool
     */
    private bool $is_multiple;

    /**
     * @param PDO $pdo
     * @param string $table
     */
    public function __construct(PDO $pdo, string $table, bool $multiple = false)
    {
        $this->table = $table;
        $this->pdo = $pdo;
        $this->_fields = [];
        $this->is_multiple = $multiple;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function field(array $fields): DB_Insert
    {
        if (count($fields) > 0) {
            $keys = array_keys($fields);
            $count_row = 1;
            if (isset($fields[0]) && is_array($fields[0]) && $this->is_multiple){
                $keys = array_keys($fields[0]);
                $count_row = count($fields);
                //remove white space around the field name
            }
            $trim_key = array_map(function($item){
                return trim($item);
            },$keys);
            $field_params = [];

            $values = null;
            $index_row = 1;
            $count_fields = count($fields);
            for ($i=0; $i < $count_row; $i++) {
                $row_fields = $fields;
                $index_key = 1;
                if (isset($fields[0]) && is_array($fields[0]) && $this->is_multiple){
                    $row_fields = $fields[$i];
                }
                $values .= '(';
                foreach ($row_fields as $field_value) {
                    $values .= '? ';
                    if ($count_fields > $index_key) {
                        $values .= ', ';
                    }
                    $field_params[] = $field_value;
                    $index_key++;
                }
                $values .= ')';
                 if ($index_row < $count_row) {
                     $values .=',';
                 }
                $index_row++;
            }

            $implode_keys = '`' . implode('`,`', $trim_key) . '`';

            $this->_fields = [
                'keys' => $implode_keys,
                'values' => $values,
                'params' => $field_params
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
        $sql = "INSERT INTO $this->table ($fields) VALUES $values";
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