<?php


namespace Wepesi\App;


trait DBField
{
    function field_params(array $fields = [],string $action)
    {
        if (count($fields) && !$this->_fields && (strtolower($action) != "insert" || strtolower($action) != "update")) {
            $keys = $fields;
            $params = $keys;
            $x = 1;
            $keys = array_keys($fields);
            $values = null;
            $_trim_key=[];
            foreach ($fields as $field) {
                $values .= "? ";
                if ($x < count($fields)) {
                    $values .= ', ';
                }
                //remove white space around the collum name
                array_push($_trim_key,trim($keys[($x-1)]));
                $x++;
            }
            $keys=$_trim_key;
            $implode_keys= "`" . implode('`,`', $keys) . "`";
            if($action=="update"){
                $implode_keys= "`" . implode('`= ?,`', $keys) . "`";
                $implode_keys.="=?";
            }
            return [
                "fields" => $implode_keys,
                "values" => $values,
                "params" => $params
            ];

        }else{
            return ("This method try to access undefined method");
        }
    }
}