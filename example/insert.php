<?php
$db = $db ?? [];
$field = [
    "userid" => 1,
    "message" => "hello from wepesi",
    "datecreated" => date('Y-m-d H:i:s', strtotime("now"))
];
$multiple_field = [
    ["userid" => 1,
    "message" => "Mote na wepesi",
    "datecreated" => date('Y-m-d H:i:s', strtotime("now"))],
    ["userid" => 1,
    "message" => "Bonjour Dimanche",
    "datecreated" => date('Y-m-d H:i:s', strtotime("now"))],
    ["userid" => 1,
    "message" => "Jambo kwa jumapili",
    "datecreated" => date('Y-m-d H:i:s', strtotime("now"))],
];
try {
    $db->insert("message")->field($field)->result();
    if ($db->error()) {
        throw new Exception($db->error());
    }
    $field['id'] = $db->lastId();
    $db->multipleInsert("message")->field($multiple_field)->result();
    if ($db->error()) {
        throw new Exception($db->error());
    }
    $multiple_field[0]['id'] = $db->lastId();
    var_dump([
        'single' => $field,
        'multiple' => $multiple_field
    ]);
} catch (Exception $e) {
    var_dump($e->getMessage());
}

