<?php

namespace Wepesi\App\Traits;

trait DBWhere
{
    function condition(array $where = []){
        if(count($where)==0) return;
        $params = [];
        /**
         * defined comparion operator to avoid error while assing operation witch does not exist
         */
        $logicalOperator = ["or", "not"];
        // chech if the array is multidimensional array
        $where = is_array($where[0]) ? $where : [$where];
        $whereLen = count($where);
        //
        $jointure_Where_Condition = null;
        $defaultComparison = "=";
        $lastIndexWhere = 1;
        $fieldValue = [];
        //
        foreach ($where as $WhereField) {
            $default_logical_operator = " and ";
            $notComparison = null;
            // check if there is a logical operator `or`||`and`
            if (isset($WhereField[3])) {
                // check id the defined operation exist in our defined tables
                $default_logical_operator = in_array(strtolower($WhereField[3]), $logicalOperator) ? $WhereField[3] : " and ";
                if ($default_logical_operator === "not") {
                    $notComparison = " not ";
                }
            }
            // check the field exist and defined by default one
            $where_field_name = strlen(trim($WhereField[0])) > 0 ? trim($WhereField[0]) : "id";
            $jointure_Where_Condition .=  $notComparison.$where_field_name.$defaultComparison." ? ";
            $where_field_value = $WhereField[2] ?? null;
            array_push($fieldValue, $where_field_value);
//
            $params[$where_field_name]=$where_field_value;
            if ($lastIndexWhere < $whereLen) {
                if ($default_logical_operator != "not") {
                    $jointure_Where_Condition .= $default_logical_operator;
                }
            }
            $lastIndexWhere++;
        }
        return [
            "field" => "WHERE ".$jointure_Where_Condition,
            "value" => $fieldValue,
            "params" => $params
        ];
    }
}