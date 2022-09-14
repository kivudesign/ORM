<?php
//use
use Wepesi\App\DB;
$config=[
    "host"=>"localhost",
    "db_name"=>"wepesi_db",
    "username"=>"root",
    "password"=>""
];
$db = DB::getInstance($config);
// include("./test/insert.php");
//include("./test/select.php");
// include("./test/delete.php");
// include("./test/query.php");
// include("./test/update.php");
 include("./test/transaction.php");