<?php

namespace Masterkey\Repository\Criteria;

use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * WhereBetween
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class WhereBetween extends AbstractCriteria
{
    /**
     * @var string
     */
    protected $column;

    /**
     * @var array
     */
    protected $values;

    /**
     * @var string
     */
    protected $boolean;

    /**
     * @var bool
     */
    protected $not;

    /**
     * @param   string  $column
     * @param   array  $values
     * @param   string  $boolean
     * @param   bool  $not
     */
    public function __construct(string $column, $values = [], $boolean = 'and', $not = false)
    {
        $this->column = $column;
        $this->values = $values;
        $this->boolean = $boolean;
        $this->not = $not;
    }

    /**
     * @param \Illuminate\Database\Query\Builder  $model
     * @param Repository $repository
     * @return \Illuminate\Database\Query\Builder
     */
    public function apply($model, Repository $repository)
    {
        return $model->whereBetween($this->column, $this->values, $this->boolean, $this->not);
    }
}
