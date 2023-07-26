<?php


use PHPUnit\Framework\TestCase;
use Wepesi\App\DB;

class DBTest extends TestCase
{
    function testDBException()
    {
        $stringException = Exception::class;
        $pdoException = PDOException::class;
        try {
            $db = DB::getInstance();
        } catch (Exception $ex) {
            $className = get_class($ex);
            $msg = $ex->getMessage();
            $code = $ex->getCode();
        }

        $expectedMessage = 'ArgumentCountError: Too few arguments to function Wepesi\App\DB::getInstance(), 0 passed';
        $expectedCode = 0;
        $this->assertSame($db, $className);
        $this->assertSame($expectedMessage, strchr($msg));
        $this->assertSame($expectedCode, $code);
    }

    function testInstanceDB()
    {
        $stringException = Exception::class;
        $pdoException = PDOException::class;
        try {
            $config = [
                'host' => 'localhost',
                'db_name' => 'wepesi_db',
                'user_name' => 'roots',
                'password' => ''
            ];
            $db = DB::getInstance($config);
        } catch (Exception $ex) {
            $className = get_class($ex);
            $msg = $ex->getMessage();
            $code = $ex->getCode();
        }

        $this->assertSame($db, $className);
    }
}