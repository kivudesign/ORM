<?php

namespace Wepesi\App\WhereQueryBuilder;

use Wepesi\App\Provider\Contract\WhereBuilderInterface;
use Wepesi\App\Provider\Contract\WhereConditionsInterface;

/**
 * Create clean where condition to minimised less error with typo
 * to create a `WHERE` clause condition.
 * The WHERE clause is used to filter records.
 * It is used to extract only those records that fulfill a specified condition.
 * Note: The WHERE clause is not only used in SELECT statements, it is also used in UPDATE and DELETE.
 *
 */
final class WhereBuilder implements WhereBuilderInterface
{
    /**
     * @var array
     */
    private array $operator;

    /**
     *
     */
    public function __construct()
    {
        $this->operator = [];
    }

    /**
     * @param WhereConditionsInterface $where_condition
     * @return $this
     */
    public function orOption(WhereConditionsInterface $where_condition): WhereBuilder
    {
        $condition = $where_condition->getCondition();
        $condition->operator = 'OR';
        $this->operator[] = $condition;
        return $this;
    }

    /**
     * @param WhereConditionsInterface $where_condition
     * @return $this
     */
    public function andOption(WhereConditionsInterface $where_condition): WhereBuilder
    {
        $condition = $where_condition->getCondition();
        $condition->operator = 'AND';
        $this->operator[] = $condition;
        return $this;
    }

    /**
     * @param WhereBuilder $builder
     * @return void
     */
    protected function groupOption(WhereBuilder $builder)
    {
        // TODO implement group condition
    }

    /**
     * @return array
     */
    private function generate(): array
    {
        return $this->operator;
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