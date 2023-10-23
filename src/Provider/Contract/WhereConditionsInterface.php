<?php
/*
 * wepesi_ORM
 * WhereConditionsInterface.php
 * https://github.com/bim-g/Wepesi-ORM
 * Copyright (c) 2023.
 */

namespace Wepesi\App\Provider\Contract;

interface WhereConditionsInterface
{
    public function isGreaterThan($field_value);
    public function isGreaterEqualThan($field_value);
    public function isLessThan($field_value);
    public function isLessEqualThan($field_value);
    public function isEqualTo($field_value);
    public function isDifferentTo($field_value);
    public function isNotEqualTo($field_value);
    public function isLike($field_value);
}