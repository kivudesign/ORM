<?php

namespace Wepesi\App\WhereQueryBuilder;

/**
 *
 */
final class WhereConditions
{
    /**
     * @var array
     */
    private array $field_condition;

    /**
     * @param string $field
     */
    public function __construct(string $field)
    {
        $this->field_condition = [
            'field_name' => $field,
            'comparison' => null,
            'field_value' => null
        ];;
    }

    /**
     * @param $field_comparison
     * @return $this
     */
    public function isGreaterThan($field_comparison): WhereConditions
    {
        $this->field_condition['comparison'] = '>';
        $this->conditionIsString($field_comparison);
        return $this;
    }

    /**
     * @param $field_comparison
     * @return $this
     */
    public function isGreaterEqualThan($field_comparison): WhereConditions
    {
        $this->field_condition['comparison'] = '>=';
        $this->conditionIsString($field_comparison);
        return $this;
    }

    /**
     * @param $field_comparison
     * @return $this
     */
    public function isLessThan($field_comparison): WhereConditions
    {
        $this->field_condition['comparison'] = '<';
        $this->conditionIsString($field_comparison);
        return $this;
    }

    /**
     * @param $field_comparison
     * @return $this
     */
    public function isLessEqualThan($field_comparison): WhereConditions
    {
        $this->field_condition['comparison'] = '<=';
        $this->conditionIsString($field_comparison);
        return $this;
    }

    /**
     * @param $field_comparison
     * @return $this
     */
    public function isEqualto($field_comparison): WhereConditions
    {
        $this->field_condition['comparison'] = '=';
        $this->conditionIsString($field_comparison);
        return $this;
    }

    /**
     * @param $field_comparison
     * @return $this
     */
    public function isDifferentTo($field_comparison): WhereConditions
    {
        $this->field_condition['comparison'] = '<>';
        $this->conditionIsString($field_comparison);
        return $this;
    }

    /**
     * @param $field_comparison
     * @return $this
     */
    public function isNotEqualTo($field_comparison)
    {
        $this->field_condition['comparison'] = '!=';
        $this->conditionIsString($field_comparison);
        return $this;
    }

    /**
     * @param $field_comparison
     * @return $this
     */
    public function isLike($field_comparison): WhereConditions
    {
        $this->field_condition['comparison'] = 'like';
        $this->conditionIsString($field_comparison);
        return $this;
    }

    /**
     * @return array
     */
    public function getCondition(): array
    {
        return $this->field_condition;
    }

    /**
     * @param $field_value
     * @return void
     */
    private function conditionIsString($field_value)
    {
        $this->field_condition['field_value'] = is_numeric($field_value) ? $field_value : "'" . $field_value . "'";
    }
}
