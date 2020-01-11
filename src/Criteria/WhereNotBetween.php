<?php

namespace Masterkey\Repository\Criteria;

/**
 * WhereNotBetween
 *
 * @author Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class WhereNotBetween extends WhereBetween
{
    /**
     * @param string $column
     * @param array  $values
     * @param string $boolean
     */
    public function __construct(string $column, $values = [], $boolean = 'and')
    {
        parent::__construct($column, $values, $boolean, true);
    }
}