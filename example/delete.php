<?php
$db = $db ?? [];
$where = ['id', "=", 12];
try {
    $res = $db->delete("message")->where([]);
    if ($db->error()) {
        throw new \Exception($db->error());
    }
    var_dump($res->result());
} catch (\Exception $e) {
    var_dump($e->getMessage());
}

