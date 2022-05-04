<?php
$db=$db??[];
$field = [
    "userid" => 1,
    "message" => "hello from wepesi",
    "datecreated" => date('Y-m-d H:i:s',strtotime("now"))
];
try {
    $db->insert("message")->field($field)->result();
    if($db->error()){
        throw new \Exception($db->error());
    }
    var_dump(["last insert ID : "=>$db->lastId()]);
} catch (Exception $e) {
    var_dump($e->getMessage());
}

