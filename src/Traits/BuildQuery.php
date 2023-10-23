<?php
/**
 * Wepesi ORM
 * BuildQuery
 * Ibrahim Mussa
 * https://github.com/bim-g
 */

namespace Wepesi\App\Traits;

use PDO;
use PDOException;

/**
 *
 */
trait BuildQuery
{
    /**
     * @param PDO $pdo
     * @param string $sql
     * @param array $params
     * @return array
     */
    protected function executeQuery(PDO $pdo, string $sql, array $params = []): array
    {
        try {
            $data_result = [
                'result' => [],
                'lastID' => -1,
                'count' => null,
                'error' => "",
            ];
            $query = $pdo->prepare($sql);
            $x = 1;
            if (count($params)) {
                foreach ($params as $param) {
                    $query->bindValue($x, $param);
                    $x++;
                }
            }
            $query_result = $query->execute();

            if ($query_result) {
                $data_result['result'] = ['query_result' => true];
                $string = explode(' ', strtolower($sql));
                switch ($string[0]) {
                    case 'select' :
                        $data_result['result'] = $query->fetchAll(PDO::FETCH_OBJ);
                        $data_result['count'] = $query->rowCount();
                        break;
                    case 'insert' :
                        $data_result['lastID'] = $pdo->lastInsertId();
                        break;
                    case 'update':
                        $data_result['count'] = $query->rowCount();
                        break;
                    case 'delete':
                        $data_result['result'] = ['delete' => true];
                        break;
                }
            }
            return $data_result;
        } catch (PDOException $ex) {
            $data_result['error'] = $ex->getmessage();
            return $data_result;
        }
    }
}