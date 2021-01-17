<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * GroupBy
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.2
 * @package Masterkey\Repository\Criteria
 */
class GroupBy extends AbstractCriteria
{
    protected array $groups;

    public function __construct(...$groups)
    {
        $this->groups = Arr::flatten($groups);
    }

    public function apply(Builder $model, Repository $repository): Builder
    {
        return $model->groupBy($this->groups);
    }
}