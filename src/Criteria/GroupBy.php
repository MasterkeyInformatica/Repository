<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Support\Arr;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * GroupBy
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.1
 * @package Masterkey\Repository\Criteria
 */
class GroupBy extends AbstractCriteria
{
    /**
     * @var array
     */
    protected $groups;

    /**
     * @param   mixed ...$groups
     */
    public function __construct(...$groups)
    {
        $this->groups = Arr::collapse($groups);
    }

    /**
     * @param   \Illuminate\Database\Query\Builder $model
     * @param   Repository $repository
     * @return  \Illuminate\Database\Query\Builder|mixed
     */
    public function apply($model, Repository $repository)
    {
        return $model->groupBy($this->groups);
    }
}