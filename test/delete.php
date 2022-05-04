<?php
$db=$db??[];
$where= ['id', "=", "3"];
try {
    $res=$db->delete("message")->where($where);
        if($db->error()){
            throw new \Exception($db->error());
        }
    var_dump($res->result());
} catch (Exception $e) {
    var_dump($e->getMessage());
}

