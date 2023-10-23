<?php
/*
 * wepesi_ORM
 * DB_Select.php
 * https://github.com/bim-g/Wepesi-ORM
 * Copyright (c) 2023.
 */

namespace Wepesi\App;

use Wepesi\App\Provider\Contract\WhereBuilderInterface;
use Wepesi\App\Provider\DbProvider;
use Wepesi\App\Traits\DBWhereCondition;

/**
 *
 */
class DB_Select extends DbProvider
{

    /**
     * @var string
     */
    private string $table, $_dsc, $_asc;

    /**
     * @var string|null
     */
    private ?string $orderBy, $action, $groupBY;

    /**
     * @var array
     */
    private array $_where, $_fields;

    /**
     * @var int|null
     */
    private ?int $_limit, $_offset;
    /**
     * @var string[]
     */
    private array $_join_comparison_sign;

    /**
     * DBSelect constructor.
     * @param \PDO $pdo
     * @param string $table
     * @param string|null $action
     */
    public function __construct(\PDO $pdo, string $table, string $action = null)
    {
        $this->table = $table;
        $this->pdo = $pdo;
        $this->action = $action;
        $this->_dsc = $this->_asc = "";
        $this->_where = $this->_fields = [];
        $this->orderBy = $this->groupBY = null;
        $this->_limit = $this->_offset = null;
        $this->_join_comparison_sign = ['=', '>', '<', '!=', '<>'];
    }

    /**
     * @param WhereBuilderInterface $where_builder
     * @return $this
     */
    public function where(WhereBuilderInterface $where_builder): DB_Select
    {
        $this->_where = $this->condition($where_builder);
        return $this;
    }

    /**
     *
     * @param array $fields
     * @return DB_Select
     */
    public function field(array $fields = []): DB_Select
    {
        if (count($fields)) {
            $keys = $fields;
            $values = null;
            $this->_fields = [
                'keys' => '' . implode(',', $keys) . '',
                'values' => $values
            ];
        }
        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function groupBY(string $field): DB_Select
    {
        if ($field) $this->groupBY = "group by $field";
        return $this;
    }

    /**
     *
     * @param string|null $order
     * @return $this
     */
    public function orderBy(string $order = null): DB_Select
    {
        if ($order) $this->orderBy = " order by ($order)";
        return $this;
    }

    /**
     * @return $this
     */
    public function random(): DB_Select
    {
        $this->orderBy = ' order by RAND()';
        return $this;
    }

    /**
     *
     * @return $this
     */
    public function ASC(): DB_Select
    {
        if ($this->orderBy) {
            $this->_asc = ' ASC';
            $this->_dsc = "";
        }
        return $this;
    }

    /**
     *
     * @return $this
     */
    public function DESC(): DB_Select
    {
        if ($this->orderBy) {
            $this->_asc = "";
            $this->_dsc = ' DESC';
        }
        return $this;
    }

    /**
     *
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit): DB_Select
    {
        $this->_limit = " LIMIT {$limit}";
        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset): DB_Select
    {
        $this->_offset = " OFFSET {$offset}";
        return $this;
    }

    /**
     *
     * @return array|int
     * execute query to get result
     */
    public function result()
    {
        $this->build();
        return $this->action == 'count' ? $this->result[0] : $this->result;
    }

    /**
     *
     */
    private function build()
    {
        $query = [];
        if ($this->action == 'count') {
            $query = $this->count_total();
        } else {
            $query = $this->select();
        }
        $this->query($query['sql'], $query['params']);
    }

    /**
     *
     */
    private function count_total(): array
    {
        $WHERE = $this->_where['field'] ?? '';
        $params = $this->_where['value'] ?? [];
        $sql = "SELECT COUNT(*) as count FROM {$this->table} " . $WHERE;
        return [
            'sql' => $sql,
            'params' => $params
        ];
    }

    /**
     * @return array
     */
    private function select(): array
    {
        $fields = $this->_fields['keys'] ?? '*';
        $WHERE = $this->_where['field'] ?? '';
        $params = $this->_where['value'] ?? [];
        //
        $sql = "SELECT " . $fields . " FROM " . " $this->table " . $WHERE . $this->groupBY . $this->orderBy . $this->_dsc . $this->_asc . $this->_limit . $this->_offset;
        $this->query($sql, $params);
        return [
            'sql' => $sql,
            'params' => $params
        ];
    }
}
