<?php
try {
    $db = $db ?? [];
    $sql = "SELECT * FROM user JOIN message On user.id=message.userid";
    $res = $db->query($sql)->result();
    if ($db->error()) {
        throw new \Exception($db->error());
    }
    var_dump($res);
} catch (Exception $e) {
    var_dump($e->getMessage());
}
