<?php
$db=$db??[];
$field = [
    "userid" => 1,
    "message" => "hello from wepesi",
    "datecreated" => Date('Y-m-d H:i:s')
];
try {
    $db->insert("message")->field($field)->result();
    var_dump($db->lastId());
} catch (Exception $e) {
    var_dump($e->getMessage());
}

