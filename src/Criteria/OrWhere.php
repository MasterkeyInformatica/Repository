<?php

namespace Masterkey\Repository\Criteria;

/**
 * OrWhere
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.0
 * @package Masterkey\Repository\Criteria
 */
class OrWhere extends Where
{
    public function __construct($column, $operator = null, $value = null)
    {
        parent::__construct($column, $operator, $value, 'or');
    }
}