<?php

namespace Masterkey\Repository\Criteria;

use Closure;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * WhereClause
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   29/03/2019
 * @package Masterkey\Repository\Criteria
 */
class WhereClause extends AbstractCriteria
{
    /**
     * @var string|Closure
     */
    protected $column;

    /**
     * @var null
     */
    protected $operator;

    /**
     * @var null
     */
    protected $value;

    /**
     * @var string
     */
    protected $boolean;

    /**
     * @param   string|Closure $column
     * @param   null $operator
     * @param   null $value
     * @param   string $boolean
     */
    public function __construct($column, $operator = null, $value = null, $boolean = 'and')
    {
        $this->column = $column;
        $this->operator = $operator;
        $this->value = $value;
        $this->boolean = $boolean;
    }

    /**
     * @param   \Illuminate\Database\Query\Builder $model
     * @param   Repository $repository
     * @return  \Illuminate\Database\Query\Builder|mixed
     */
    public function apply($model, Repository $repository)
    {
        return $model->where(
            $this->column,
            $this->operator,
            $this->value,
            $this->boolean
        );
    }
}