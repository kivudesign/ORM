<?php


namespace Wepesi\App;

abstract class DB_Q
{
    private \PDO $pdo;
    private int $rowCount;
    private int $lastInsertId=0;
    private array $result=[];
    private $error;
    function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     *
     * @param string $sql : sql string
     * @param array $params
     */
    function executeQuery(string $sql, array $params = [])
    {
        $this->error = false;
        try {
            $query = $this->pdo->prepare($sql);
            $x = 1;
            if (count($params)) {
                foreach ($params as $param) {
                    $query->bindValue($x, $param);
                    $x++;
                }
            }
            if($query->execute()){
                if (strchr(strtolower($sql), "update") || strchr(strtolower($sql), "select")) {
                    $this->result = $query->fetchAll(\PDO::FETCH_OBJ);
                    $this->rowCount = $query->rowCount();
                } else if (strchr(strtolower($sql), "insert into")) {
                    $this->lastInsertId = $this->pdo->lastInsertId();
                }else{
                    $this->result=true;
                }
            }
            return;
        } catch (\Exception $ex) {
            $this->error = $ex->getmessage();
        }
    }

    /**
     * @return int
     */
    function get_rowCount(): int
    {
        return $this->rowCount;
    }

    /**
     * @return int
     */
    function get_lastid(): int
    {
        return $this->lastInsertId;
    }

    /**
     * @return array
     */
    function get_result(): array
    {
        return $this->result;
    }

    /**
     * @return mixed
     */
    function get_Error()
    {
        return $this->error;
    }
}