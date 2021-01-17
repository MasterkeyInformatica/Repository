<?php

namespace Masterkey\Repository\Criteria;

/**
 * WhereNotIn
 *
 * @author Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.0
 * @package Masterkey\Repository\Criteria
 */
class WhereNotIn extends WhereIn
{
    public function __construct(string $column, $values, string $boolean = 'and')
    {
        parent::__construct($column, $values, $boolean, true);
    }
}