<?php

namespace Masterkey\Repository\Criteria;

/**
 * WhereNotNull
 *
 * @author Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class WhereNotNull extends WhereNull
{
    /**
     * @param string $column
     * @param string $boolean
     */
    public function __construct(string $column, string $boolean = 'and')
    {
        parent::__construct($column, $boolean, true);
    }
}