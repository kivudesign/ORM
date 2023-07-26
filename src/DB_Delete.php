<?php
/**
 * Wepesi ORM
 * DB_Delete
 * Ibrahim Mussa
 * https://github.com/bim-g
 */

namespace Wepesi\App;

use PDO;
use Wepesi\App\Provider\DbProvider;

/**
 *
 */
class DB_Delete extends DbProvider
{
    /**
     * @var string
     */
    private string $table;
    /**
     * @var array
     */
    private array $where;


    /**
     * @param PDO $pdo
     * @param string $table
     */
    public function __construct(PDO $pdo, string $table)
    {
        $this->table = $table;
        $this->pdo = $pdo;
        $this->where = [];
        $this->result = [];
    }

    /**
     * @param array $where
     * @return $this
     */
    public function where(array $where = []): DB_Delete
    {
        if (count($where) > 0) {
            $params = [];
            /**
             * defined comparion operator to avoid error while assing operation witch does not exist
             */
            $logicalOperator = ['or', 'not'];
            $default_logical_operator = ' and ';
            // check if the array is multidimensional array
            $where = is_array($where[0]) ? $where : [$where];
            $whereLen = count($where);
            //
            $joiner_Where_Condition = null;
            $defaultComparison = '=';
            $lastIndexWhere = 1;
            $fieldValue = [];
            //
            foreach ($where as $WhereField) {
                $notComparison = null;
                // check if there is a logical operator `or`||`and`
                if (isset($WhereField[3])) {
                    // check id the defined operation exist in our defined tables
                    $default_logical_operator = in_array(strtolower($WhereField[3]), $logicalOperator) ? $WhereField[3] : ' and ';
                    if ($default_logical_operator === 'not') {
                        $notComparison = ' not ';
                    }
                }
                // check the field exist and defined by default one
                $where_field_name = strlen(trim($WhereField[0])) > 0 ? trim($WhereField[0]) : 'id';
                $joiner_Where_Condition .= $notComparison . $where_field_name . $defaultComparison . ' ? ';
                $where_field_value = $WhereField[2] ?? null;
                $fieldValue[] = $where_field_value;
//
                $params[$where_field_name] = $where_field_value;
                if ($lastIndexWhere < $whereLen) {
                    if ($default_logical_operator != 'not') {
                        $joiner_Where_Condition .= $default_logical_operator;
                    }
                }
                $lastIndexWhere++;
            }
            $this->where = [
                'field' => 'WHERE ' . $joiner_Where_Condition,
                'value' => $fieldValue,
                'params' => $params
            ];
        }
        return $this;
    }

    /**
     * @return array return result after a request select
     * return result after a request select
     */
    public function result(): array
    {
        $this->delete();
        return $this->result;
    }

    /**
     * @return void use this module to delete and existing row record
     * use this module to delete and existing row record
     */
    private function delete(): void
    {
        $where = $this->where['field'] ?? '';
        $params = $this->where['params'] ?? [];
        $sql = "DELETE FROM $this->table $where";
        $this->query($sql, $params);
    }

}