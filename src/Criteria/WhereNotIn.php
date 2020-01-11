<?php

namespace Masterkey\Repository\Criteria;

/**
 * WhereNotIn
 *
 * @author Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class WhereNotIn extends WhereIn
{
    /**
     * @param string $column
     * @param        $values
     * @param string $boolean
     */
    public function __construct(string $column, $values, string $boolean = 'and')
    {
        parent::__construct($column, $values, $boolean, true);
    }
}