<?php
$ini_array = (object) parse_ini_file("./init/config.ini", true);
$db_conf = (object)$ini_array->db_conf;

// database configuration setup
define("HOST", $db_conf->host);
define("DATABASE", $db_conf->database);
define("USER", $db_conf->user);
define("PASSWORD", $db_conf->password);

//web root configaration
define('WEB_ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));
define('ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']));