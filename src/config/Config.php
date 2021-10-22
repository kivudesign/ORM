<?php
namespace Wepesi\App;

class Config
{
    private static array $setup=[
            "config"=>["mysql"=>[
                    'host'=> "localhost",
                    'db'=> "wepesi_db",
                    'username'=> "root",
                    'password'=> "",
                ],
            "remender"=>[]
            ]
    ];

    static function get($path = null)
    {
        if ($path) {
            $config = self::$setup['config'];
            $path = explode('/', $path);

            foreach ($path as $bit) {
                if (isset($config[$bit])) {
                    $config = $config[$bit];
                }
            }
            return $config;
        }
        return false;
    }
}
