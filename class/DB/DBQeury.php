<?php

class DBQeury{
    private $_pdo;
    private $rowCount,$lastInsertId,$result,$error;
    function __construct($pdo,string $sql,array $param=[])
    {
        $this->_pdo=$pdo;
        $this->exceuteQuery($sql,$param);
    }
    private function exceuteQuery($sql, array $params = [])
    {
        $this->error = false;
        try {
            $_query = $this->_pdo->prepare($sql) ;
            $x = 1;
            if (count($params)) {
                foreach ($params as $param) {
                    $_query->bindValue($x, $param);
                    $x++;
                }
            }
            $_query->execute();
            if (strchr($sql, "UDPATE") || strchr($sql, "SELECT")) {
                $this->result = $_query->fetchAll(PDO::FETCH_OBJ);
                $this->rowCount = $_query->rowCount();
            } else if (strchr($sql, "INSERT INTO")) {
                $this->lastInsertId = $this->_pdo->lastInsertId();
            }                
        }catch(Exception $ex){
            $this->error = $ex->getmessage();
        }
        return $this;
    }
    function rowCount(){
        return $this->rowCount;
    }
    function lastid(){
        return $this->lastInsertId;
    }
    function result(){
        return $this->result;
    }
    function getError(){
        return $this->error;
    }
}