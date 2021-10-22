<?php
$db=$db??[];
$where= ['id', "=", "4"];
try {
    $res = $db->delete("message")->where($where)->result();
    var_dump($db->lastId());
} catch (Exception $e) {
    var_dump($e->getMessage());
}

