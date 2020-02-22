<?php

namespace Masterkey\Repository\Criteria;

use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * WhereColumn
 *
 * @author Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class WhereColumn extends AbstractCriteria
{
    protected $first;

    /**
     * @var null
     */
    protected $operator;

    /**
     * @var null
     */
    protected $second;

    /**
     * @var string
     */
    protected $boolean;

    /**
     * @param string|array $first
     * @param string|null  $operator
     * @param string|null  $second
     * @param string       $boolean
     */
    public function __construct($first, $operator = null, $second = null, $boolean = 'and')
    {
        $this->first = $first;
        $this->operator = $operator;
        $this->second = $second;
        $this->boolean = $boolean;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $model
     * @param Repository                         $repository
     * @return \Illuminate\Database\Query\Builder|mixed
     */
    public function apply($model, Repository $repository)
    {
        return $model->whereColumn(
            $this->first,
            $this->operator,
            $this->second,
            $this->boolean
        );
    }
}