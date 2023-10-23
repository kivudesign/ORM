<?php
/*
 * wepesi_ORM
 * WhereBuilderInterface.php
 * https://github.com/bim-g/Wepesi-ORM
 * Copyright (c) 2023.
 */

namespace Wepesi\App\Provider\Contract;

use Wepesi\App\WhereQueryBuilder\WhereBuilder;

interface WhereBuilderInterface
{
    public function orOption(WhereConditionsInterface $where_condition);
    public function andOption(WhereConditionsInterface $where_condition);
}