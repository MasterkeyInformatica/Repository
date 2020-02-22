<?php

namespace Masterkey\Repository\Criteria;

use Closure;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * Where
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class Where extends AbstractCriteria
{
    /**
     * @var string|Closure
     */
    protected $column;

    /**
     * @var string|null
     */
    protected $operator;

    /**
     * @var string|null
     */
    protected $value;

    /**
     * @var string
     */
    protected $boolean;

    /**
     * @param string|Closure $column
     * @param string|null    $operator
     * @param string|null    $value
     * @param string         $boolean
     */
    public function __construct($column, $operator = null, $value = null, $boolean = 'and')
    {
        $this->column = $column;
        $this->operator = $operator;
        $this->value = $value;
        $this->boolean = $boolean;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $model
     * @param Repository                         $repository
     * @return \Illuminate\Database\Query\Builder
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