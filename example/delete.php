<?php
$db = $db ?? [];
$where = (new \Wepesi\App\WhereQueryBuilder\WhereBuilder())->orOption((new \Wepesi\App\WhereQueryBuilder\WhereConditions('id'))->isEqualTo(12));
try {
    $res = $db->delete("message")->where($where);
    if ($db->error()) {
        throw new \Exception($db->error());
    }
    var_dump($res->result());
} catch (\Exception $e) {
    var_dump($e->getMessage());
}

