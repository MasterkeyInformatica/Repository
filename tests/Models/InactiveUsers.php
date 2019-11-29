<?php

namespace Masterkey\Tests\Models;

use Masterkey\Repository\Criteria\Where;

class InactiveUsers extends Where
{
    public function __construct()
    {
        parent::__construct('active', false);
    }
}