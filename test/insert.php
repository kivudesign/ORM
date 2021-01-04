<?php
$data = [
    "userid" => 2,
    "message" => "hello from wepesi",
    "datecreated" => Date('Y-m-d H:i:s')
];
$res=$message->sendMessage($data);

var_dump($res);