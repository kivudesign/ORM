<?php

namespace Wepesi\App\WhereQueryBuilder;

use Wepesi\App\Provider\Contract\WhereConditionsInterface;

/**
 * Apply clean condition to minimised less error with typo
 * to create conditions to be applied on where clause
 * The WHERE clause is used to filter records.
 *
 * It is used to extract only those records that fulfill a specified condition.
 * Note: The WHERE clause is not only used in SELECT statements, it is also used in UPDATE and DELETE.
 *
 */
final class WhereConditions implements WhereConditionsInterface
{
    /**
     * @var object
     */
    private object $field_condition;

    /**
     * @param string $field_name name of the field to be compared
     */
    public function __construct(string $field_name)
    {
        $this->field_condition = (object)[
            'field_name' => $field_name,
            'comparison' => null,
            'field_value' => null
        ];;
    }

    /**
     * @param string|int $field_value value to be used to apply the filter, and can be for different data type such as int, string, or date,...
     * @return $this
     */
    public function isGreaterThan($field_value): WhereConditions
    {
        $this->field_condition->comparison = '>';
        $this->conditionIsString($field_value);
        return $this;
    }

    /**
     * @param string|int $field_value value to be used to apply the filter, and can be for different data type such as int, string, or date,...
     * @return $this
     */
    public function isGreaterEqualThan($field_value): WhereConditions
    {
        $this->field_condition->comparison = '>=';
        $this->conditionIsString($field_value);
        return $this;
    }

    /**
     * @param string|int $field_value value to be used to apply the filter, and can be for different data type such as int, string, or date,...
     * @return $this
     */
    public function isLessThan($field_value): WhereConditions
    {
        $this->field_condition->comparison = '<';
        $this->conditionIsString($field_value);
        return $this;
    }

    /**
     * @param string|int $field_value value to be used to apply the filter, and can be for different data type such as int, string, or date,...
     * @return $this
     */
    public function isLessEqualThan($field_value): WhereConditions
    {
        $this->field_condition->comparison = '<=';
        $this->conditionIsString($field_value);
        return $this;
    }

    /**
     *
     * @param string|array|int|bool $field_value This value will
     * @return $this
     */
    public function isEqualTo($field_value): WhereConditions
    {
        $this->field_condition->comparison = '=';
        $this->conditionIsString($field_value);
        return $this;
    }

    /**
     * @param string|int $field_value value to be used to apply the filter, and can be for different data type such as int, string, or date,...
     * @return $this
     */
    public function isDifferentTo($field_value): WhereConditions
    {
        $this->field_condition->comparison = '<>';
        $this->conditionIsString($field_value);
        return $this;
    }

    /**
     * @param string|int $field_value value to be used to apply the filter, and can be for different data type such as int, string, or date,...
     * @return $this
     */
    public function isNotEqualTo($field_value): WhereConditions
    {
        $this->field_condition->comparison = '!=';
        $this->conditionIsString($field_value);
        return $this;
    }

    /**
     * @param string|int $field_value value to be used to apply the filter, and can be for different data type such as int, string, or date,...
     * @return $this
     */
    public function isLike($field_value): WhereConditions
    {
        $this->field_condition->comparison = 'like';
        $this->conditionIsString($field_value);
        return $this;
    }

    /**
     * @return object
     */
    private function getCondition(): object
    {
        return $this->field_condition;
    }

    /**
     * @param string|int $field_value value to be used to apply the filter, and can be for different data type such as int, string, or date,...
     * @return void
     */
    private function conditionIsString($field_value)
    {
        $this->field_condition->field_value = is_numeric($field_value) ? $field_value : "'" . $field_value . "'";
    }
    /**
     * @param $name
     * @param $arguments
     * @return mixed|void
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $arguments);
        }
    }
}
