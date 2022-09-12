<?php
/**
 * Wepesi ORM
 * BuildQuery
 * Ibrahim Mussa
 * https://github.com/bim-g
 */
namespace Wepesi\App\Traits;

trait BuildQuery
{
    protected function executeQuery(\PDO $pdo, string $sql, array $params = []): array
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

            if($query_result){
                $data_result['result'] = ['query_result' => true];
                if (strchr(strtolower($sql), 'update') || strchr(strtolower($sql), 'select')) {
                    $data_result['result'] = $query->fetchAll(\PDO::FETCH_OBJ);
                    $data_result['count'] = $query->rowCount();
                } else if (strchr(strtolower($sql), 'insert into')) {
                    $data_result['lastID'] = $pdo->lastInsertId();
                } else if (strchr(strtolower($sql), 'delete')) {
                    $data_result['result'] = ['delete' => true];
                }
            }
            return $data_result;
        } catch (\PDOException $ex) {
            $data_result['error'] = $ex->getmessage();
            return $data_result;
        }
    }
}