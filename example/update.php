<?php
$db = $db ?? (object)[];
try {
    $field = ["message" => "Get update to new test update message"];
    $where = (new \Wepesi\App\WhereQueryBuilder\WhereBuilder())->orOption((new \Wepesi\App\WhereQueryBuilder\WhereConditions('id'))->isEqualTo(22));
    $res = $db->update("message")->field($field)->where($where)->result();
    if ($db->error()) {
        throw new \Exception($db->error());
    }
    var_dump($db->rowCount());
} catch (Exception $e) {
    var_dump($e->getMessage());
}