<?php
try{
    $db=$db??[];
    $sql="select * from user JOIN message On user.id=message.userid";
    $res=$db->query($sql)->result();
    var_dump($res);
} catch (Exception $e) {
    var_dump($e->getMessage());
}
