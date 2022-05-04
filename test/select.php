<?php
try{
    $where=[];
//    $where= ['id', "=", 1];
    $db=$db??(object)[];
    $res=$db->get("users")->where($where)->result();
    var_dump($res);
} catch (Exception $e) {
    var_dump($e->getMessage());
}