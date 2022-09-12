<?php
$db = $db??[];
$user = [
    "fullname" => "Celestin Doe",
    "username" => "John Doe",
    "password" => md5("12345678"),
    "datecreated" => Date("Y-m-d H:i:s",time())
];

$message = [
    "message" => "Hello Celestin",
    'datecreated' => Date('Y-m-d H:i:s', time())
];
try {
    $db->transaction(function($db) use ($user,$message){
        $db->insert('users')->field($user)->result();
        if ($db->error()) {
            throw new \Exception($db->error());
        }
        $user_id = $db->lastId();
        $user['id'] = $user_id;
        $message['user_id'] = $user_id;
        $db->insert('message')->field($message)->result();
        if ($db->error()) {
            throw new \Exception($db->error());
        }
        $message['id'] = $db->lastId();
        $user['messages'] = $message;

        print_r($user);
    });
} catch (\Exception $ex) {
    var_dump($ex);
}
