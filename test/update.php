<?php
$db=$db??[];
try{
    $field=["message" => "new test update message"];
    $where= ['id', "=", "4"];
    $res=$db->update("message")->field(field)->where($where)->result();
    var_dump($res);
} catch (Exception $e) {
    var_dump($e->getMessage());
}