<?php

namespace Wepesi\App\Provider;

use PDO;
use Wepesi\App\Provider\Contract\DbContract;
use Wepesi\App\Traits\BuildQuery;
use Wepesi\App\Traits\DBWhereCondition;

/**
 *
 */
abstract class DbProvider implements DbContract
{
    /**
     * @var string
     */
    protected string $_error = '';
    /**
     * @var PDO
     */
    protected PDO $pdo;
    /**
     * @var array
     */
    protected array $result = [];
    /**
     * @var int
     */
    protected int $lastID = 0;
    /**
     * @var int
     */
    protected int $_count;

    use BuildQuery;
    use DBWhereCondition;

    /**
     * @return string
     */
    public function error(): string
    {
        return $this->_error;
    }

    /**
     * @param string $sql
     * @param array $values
     * @return void
     * this module is use to execute sql request
     */
    protected function query(string $sql, array $values)
    {
        $q = $this->executeQuery($this->pdo, $sql, $values);
        $this->result = $q['result'];
        $this->_error = $q['error'] ?? '';
        $this->lastID = $q['lastID'] ?? 0;
        $this->_count = $q['count'] ?? 0;
    }
}