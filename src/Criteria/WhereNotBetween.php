<?php

namespace Masterkey\Repository\Criteria;

/**
 * WhereNotBetween
 *
 * @author Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.0
 * @package Masterkey\Repository\Criteria
 */
class WhereNotBetween extends WhereBetween
{
    public function __construct(string $column, $values = [], $boolean = 'and')
    {
        parent::__construct($column, $values, $boolean, true);
    }
}