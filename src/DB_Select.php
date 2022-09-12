<?php
/**
 * Wepesi ORM
 * DB_Update
 * Ibrahim Mussa
 * https://github.com/bim-g
 */
namespace Wepesi\App;

use Wepesi\App\Traits\BuildQuery;

class DB_Select
{
    private  ?string $table, $action,$_error,$_dsc,$_asc,$orderBy,$groupBY;
    private  array $_where,  $_fields;
    private ?int $_limit,  $_offset;
    private array $_join_comparison_sign;
    use BuildQuery;

    /**
     * DBSelect constructor.
     * @param \PDO $pdo
     * @param string $table
     * @param string|null $action
     */
    function __construct(\PDO $pdo, string $table, string $action = null)
    {
        $this->table = $table;
        $this->_pdo = $pdo;
        $this->action = $action;
        $this->_dsc = $this->_asc=null;
        $this->_where = $this->_fields =[];
        $this->_error = null;
        $this->orderBy = $this->groupBY = null;
        $this->_limit = $this->_offset = null;
        $this->_join_comparison_sign = ['=', '>', '<', '!=', '<>'];
    }

    /**
     * @param array $params
     * @return $this
     * @throws \Exception
     */
    function where(array $params = []): DB_Select
    {
        if (count($params)) {
            // $params = [];
            //
            /**
             * defined comparison operator to avoid error while Passing operation witch does not exist
             */
            $comparisonOperator = ['<', '<=', '>', '>=', '<>', '!=', 'like'];
            // defined logical operator
            $logicalOperator = ['or', 'not'];
            // chech if the array is multidimensional array
            $key = array_keys($params);
            $key_exist = is_string($key[0]);
            if ($key_exist) {
                throw new \Exception('bad format, for where data');
            }
            $where = is_array($params[0]) ? $params : [$params];
            $whereLen = count($where);
            //
            $jointuresWhereCondition = '';
            $defaultComparison = '=';
            $lastIndexWhere = 1;
            $fieldValue = [];
            //
            foreach ($where as $WhereField) {
                $defaultLogical = ' AND ';
                $notComparison = null;
                // check if there is a logical operator `or`||`and`
                if (isset($WhereField[3])) {
                    // check id the defined operation exist in our defined tables
                    $defaultLogical = in_array(strtolower($WhereField[3]), $logicalOperator) ? $WhereField[3] : ' and ';
                    if ($defaultLogical === 'not') {
                        $notComparison = ' not ';
                    }
                }
                // check the field exist and defined by default one
                $_WhereField = strlen($WhereField[0]) > 0 ? $WhereField[0] : 'id';
                // check if comparison  exist on the array
                $defaultComparison = in_array($WhereField[1], $comparisonOperator) ? $WhereField[1] : '=';
                $jointuresWhereCondition .= " {$notComparison} {$_WhereField} {$defaultComparison}  ? ";
                $valueTopush = isset($WhereField[2]) ? $WhereField[2] : null;
                array_push($fieldValue, $valueTopush);
                if ($lastIndexWhere < $whereLen) {
                    if ($defaultLogical != 'not') {
                        $jointuresWhereCondition .= $defaultLogical;
                    }
                }
                $lastIndexWhere++;
            }
            $this->_where = [
                'field' => " WHERE {$jointuresWhereCondition} ",
                'value' => $fieldValue
            ];
        }
        return $this;

    }

    /**
     *
     * @param array $fields
     * @return DB_Select
     */
    function field(array $fields = []): DB_Select
    {
        if (count($fields)) {
            $keys = $fields;
            $values = null;
            $this->_fields = [
                'keys' => '' . implode(',', $keys) . '',
                'values' => $values
            ];
        } else {
            $this->_fields = '*';
        }
        return $this;
    }

    /**
     * @param array $group
     * @return $this
     */
    function groupBY(string $field): DB_Select
    {
        $this->groupBY = "group by $field";
        return $this;
    }

    /**
     *
     * @param string $order
     * @return $this
     */
    function orderBy(string $order = null): DB_Select
    {
        if ($order) $this->orderBy = " order by ($order)";
        return $this;
    }

    function random(): DB_Select
    {
        $this->orderBy = ' order by RAND()';
        return $this;
    }

    /**
     *
     * @return $this
     */
    function ASC(): DB_Select
    {
        if ($this->orderBy) {
            $this->_asc = ' ASC';
            $this->_dsc = null;
        }
        return $this;
    }

    /**
     *
     * @return $this
     */
    function DESC(): DB_Select
    {
        if ($this->orderBy) {
            $this->_asc = null;
            $this->_dsc = ' DESC';
        }
        return $this;
    }

    /**
     *
     * @param int $limit
     * @return $this
     */
    function limit(int $limit): DB_Select
    {
        $this->_limit = " LIMIT {$limit}";
        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    function offset(int $offset): DB_Select
    {
        $this->_offset = " OFFSET {$offset}";
        return $this;
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
        $sortedASC_DESC = ($this->_dsc || $this->_asc) ? ($this->_dsc ?? $this->_asc) : null;
        //
        $sql = "SELECT " . $fields . " FROM " . " $this->table " . $WHERE . $this->groupBY . $this->orderBy . $sortedASC_DESC . $this->_limit . $this->_offset;
        $this->query($sql, $params);
        return [
            'sql' => $sql,
            'params' => $params
        ];
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
     *
     * @param string $sql
     * @param array $params
     */
    private function query(string $sql, array $params = [])
    {
        $q = $this->executeQuery($this->_pdo, $sql, $params);
        $this->_results = $q['result'];
        $this->_count = $q['count']??0;
        $this->_error = $q['error'];
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
     * @return bool|int
     * execute query to get result
     */
    function result()
    {
        $this->build();
        return $this->action == 'count' ? count($this->_results) : $this->_results;
    }

    /**
     * @return mixed
     */
    function error()
    {
        return $this->_error;
    }
}