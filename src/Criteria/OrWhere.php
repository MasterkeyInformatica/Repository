<?php

namespace Masterkey\Repository\Criteria;

use Closure;

/**
 * OrWhere
 *
 * @author Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class OrWhere extends Where
{
    /**
     * @param string|Closure $column
     * @param string|null    $operator
     * @param string|null    $value
     */
    public function __construct($column, $operator = null, $value = null)
    {
        parent::__construct($column, $operator, $value, 'or');
    }
}