<?php
$data=["message" => "new test update message"];
$where= ['id', "=", "4"];
$res=$message->updateMessage($data,$where);
var_dump($res);