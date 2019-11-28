<?php

namespace Masterkey\Repository\Criteria;

use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * OrderBy
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.0
 * @package Masterkey\Repository\Criteria
 */
class OrderBy extends AbstractCriteria
{
    /**
     * @var string
     */
    protected $column;

    /**
     * @var string
     */
    protected $direction;

    /**
     * @param   string  $column
     * @param   string  $direction
     */
    public function __construct(string $column, string $direction = 'asc')
    {
        $this->column = $column;
        $this->direction = $direction;
    }

    /**
     * @param   \Illuminate\Database\Query\Builder $model
     * @param   Repository $repository
     * @return  \Illuminate\Database\Query\Builder|mixed
     */
    public function apply($model, Repository $repository)
    {
        return $model->orderBy($this->column, $this->direction);
    }
}