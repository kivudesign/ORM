<?php
class QueryParams{
    private $_pdo,$_query,$table,$action;
    private $_where,$_fields;
    private $_results,$_count,$_lastid,$_error;
    // 
    function __construct($pdo,string $table,string $action)
    {
        $this->_pdo=$pdo;
        $this->table=$table;
        $this->action=$action;
    }
    // 
    private function select(){        
        // 
        $sql = "SELECT * FROM {$this->table}";
        return $this->query($sql);
    }
    private function query($sql, array $params = [])
    {
        $this->_error = false;
        if ($_query = $this->_pdo->prepare($sql)) {
            $x = 1;
            if (count($params)) {
                foreach ($params as $param) {
                    $_query->bindValue($x, $param);
                    $x++;
                }
            }
            if ($_query->execute()) {
                if (strchr($sql, "SELECT")) {
                    $this->_results = $_query->fetchAll(PDO::FETCH_OBJ);
                    $this->_count = $_query->rowCount();
                } else if (strchr($sql, "INSERT INTO")) {
                    $this->_lastid = $this->_pdo->lastInsertId();
                }
            } else {
                $this->_error = true;
            }
        }
        return $this;
    }

    function result(){
        $this->select();
        return $this->_results;
    }

    function lastId(){
        return $this->_lastid;
    }
}