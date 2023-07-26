<?php
//use
include __DIR__ . '/../vendor/autoload.php';

use Wepesi\App\DB;

$config = [
    'host' => '127.0.0.1',
    'db_name' => 'wepesi_db',
    'username' => 'root',
    'password' => 'Bobek@09',
    'port' => 3306
];
$db = DB::getInstance($config);
// include("insert.php");
include("select.php");
// include("delete.php");
// include("query.php");
// include("update.php");
// include("transaction.php");