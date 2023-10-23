<?php

namespace Wepesi\App\Traits;

use Wepesi\App\Provider\Contract\WhereBuilderInterface;

/**
 *
 */
trait DBWhereCondition
{
    /**
     * @param WhereBuilderInterface $whereBuilder
     * @return array|void
     */
    public function condition(WhereBuilderInterface $whereBuilder)
    {
        $where = $whereBuilder->generate();
        if (count($where) == 0) return;
        $params = [];

        /**
         * defined comparison operator to avoid error while passing operation witch does not exist
         */
        $logicalOperator = ['or', 'not'];
        // check if the array is multidimensional array
        $len = count($where);
        $where_condition_string = '';
        $index = 1;
        $field_value = [];
        //
        foreach ($where as $object) {
            $notComparison = null;
            // check the field exist and defined by default one
            $where_condition_string .= $object->field_name . $object->comparison . ' ? ';
            $field_value[] = $object->field_value;

            $params[$object->field_name] = $object->field_value;
            if ($index < $len) {
                $where_condition_string .= $object->operator;
            }
            $index++;
        }
        return [
            'field' => 'WHERE ' . $where_condition_string,
            'value' => $field_value,
            'params' => $params
        ];
    }
}