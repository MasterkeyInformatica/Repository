<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Query\Expression;

/**
 * SelectRaw
 *
 * @author Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class SelectRaw extends Expression
{
    /**
     * @param string $query
     */
    public function __construct(string $query)
    {
        parent::__construct($query);
    }
}