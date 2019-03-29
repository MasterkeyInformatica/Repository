<?php

namespace Masterkey\Repository\Criteria;

use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * GroupByClause
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   29/03/2019
 * @package Masterkey\Repository\Criteria
 */
class GroupByClause extends AbstractCriteria
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
        $this->groups = $groups;
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