<?php
try {
    // Simple request
    $result = $db->get('users')->result();
    var_dump($result);

//    Request with Where condition.

    $where = (new \Wepesi\App\WhereQueryBuilder\WhereBuilder())
        ->orOption((new \Wepesi\App\WhereQueryBuilder\WhereConditions('id'))
            ->isEqualTo(6));

    $db = $db ?? (object)[];
    $result = $db->get("users")->where($where)->result();
    var_dump($result);
} catch (Exception $e) {
    var_dump($e->getMessage());
}