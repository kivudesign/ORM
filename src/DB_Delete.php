<?php
/*
 * wepesi_ORM
 * DB_Delete.php
 * https://github.com/bim-g/Wepesi-ORM
 * Copyright (c) 2023.
 */

namespace Wepesi\App;

use PDO;
use Wepesi\App\Provider\Contract\WhereBuilderInterface;
use Wepesi\App\Provider\DbProvider;
use Wepesi\App\Traits\DBWhereCondition;

/**
 *
 */
class DB_Delete extends DbProvider
{
    /**
     * @var string
     */
    private string $table;
    /**
     * @var array
     */
    private array $where;
    use DBWhereCondition;

    /**
     * @param PDO $pdo
     * @param string $table
     */
    public function __construct(PDO $pdo, string $table)
    {
        $this->table = $table;
        $this->pdo = $pdo;
        $this->where = [];
        $this->result = [];
    }

    /**
     * @param WhereBuilderInterface $where_builder
     * @return $this
     */
    public function where(WhereBuilderInterface $where_builder): DB_Delete
    {
        $this->where = $this->condition($where_builder);
        return $this;
    }

    /**
     * @return array return result after a request select
     * return result after a request select
     */
    public function result(): array
    {
        $this->delete();
        return $this->result;
    }

    /**
     * @return void use this module to delete and existing row record
     * use this module to delete and existing row record
     */
    private function delete(): void
    {
        $where = $this->where['field'] ?? '';
        $params = $this->where['params'] ?? [];
        $sql = "DELETE FROM $this->table $where";
        $this->query($sql, $params);
    }
}
