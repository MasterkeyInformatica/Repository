<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Support\Arr;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * With
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.1
 * @package Masterkey\Repository\Criteria
 */
class With extends AbstractCriteria
{
    /**
     * @var array
     */
    protected $with;

    /**
     * @param array ...$with
     */
    public function __construct(...$with)
    {
        $this->with = Arr::collapse($with);
    }

    /**
     * @param   \Illuminate\Database\Query\Builder $model
     * @param   Repository $repository
     * @return  \Illuminate\Database\Query\Builder
     */
    public function apply($model, Repository $repository)
    {
        return $model->with($this->with);
    }
}
