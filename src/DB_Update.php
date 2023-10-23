<?php
/*
 * wepesi_ORM
 * DB_Update.php
 * https://github.com/bim-g/Wepesi-ORM
 * Copyright (c) 2023.
 */

namespace Wepesi\App;

use Wepesi\App\Provider\Contract\WhereBuilderInterface;
use Wepesi\App\Provider\DbProvider;

/**
 *
 */
class DB_Update extends DbProvider
{
    /**
     * @var string
     */
    private string $table;
    /**
     * @var array
     */
    private array $_where;
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
        $this->result = [];
        $this->_where = [];
        $this->_fields = [];
        $this->_count = 0;
    }

    /**
     * @param WhereBuilderInterface $where_builder
     * @return $this
     */
    public function where(WhereBuilderInterface $where_builder): DB_Update
    {
        $this->_where = $this->condition($where_builder);
        return $this;
    }
    /**
     * @param array $fields
     * @return $this
     */
    public function field(array $fields = []): DB_Update
    {
        if (count($fields) && !$this->_fields) {
            $keys = $fields;
//                $values = null;
            $params = $keys;
            $x = 1;
            $keys = array_keys($fields);
            $values = null;
            $_trim_key = [];
            foreach ($fields as $field) {
                $values .= '? ';
                if ($x < count($fields)) {
                    $values .= ', ';
                }
                //remove white space around the collum name
                $_trim_key[] = trim($keys[($x - 1)]);
                $x++;
            }
            $keys = $_trim_key;
            //
            $implode_keys = '`' . implode('`= ?,`', $keys) . '`';
            $implode_keys .= '=?';
            //
            $this->_fields = [
                'keys' => $implode_keys,
                'values' => $values,
                'params' => $params
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
        $this->update();
        return $this->result;
    }

    /**
     *
     */
    private function update()
    {
        $where = $this->_where['field'] ?? null;
        $where_params = $this->_where['params'] ?? [];
        $fields = $this->_fields['keys'];
        $field_params = $this->_fields['params'] ?? [];
        $params = array_merge($field_params, $where_params);
        //generate the sql query to be executed
        $sql = "UPDATE $this->table SET $fields  $where";
        $this->query($sql, $params);
    }


    /**
     * @return int
     * return counted rows of a select query
     */
    public function count(): int
    {
        return $this->_count;
    }
}
