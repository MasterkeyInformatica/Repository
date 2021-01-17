<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * OrderBy
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 3.0.0
 * @package Masterkey\Repository\Criteria
 */
class OrderBy extends AbstractCriteria
{
    protected string $column;

    protected string $direction;

    public function __construct(string $column, string $direction = 'asc')
    {
        $this->column    = $column;
        $this->direction = $direction;
    }

    /**
     * @param Builder $model
     * @param Repository    $repository
     * @return Builder
     */
    public function apply($model, Repository $repository): Builder
    {
        return $model->orderBy($this->column, $this->direction);
    }
}