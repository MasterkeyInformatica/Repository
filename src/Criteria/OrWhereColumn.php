<?php

namespace Masterkey\Repository\Criteria;

/**
 * OrWhereColumn
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class OrWhereColumn extends WhereColumn
{
    public function __construct($first, $operator = null, $second = null)
    {
        parent::__construct($first, $operator, $second, 'or');
    }
}