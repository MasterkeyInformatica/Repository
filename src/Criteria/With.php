<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * With
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 3.0.0
 * @package Masterkey\Repository\Criteria
 */
class With extends AbstractCriteria
{
    protected array $with;

    public function __construct(...$with)
    {
        $this->with = Arr::collapse($with);
    }

    /**
     * @param Builder $model
     * @param Repository    $repository
     * @return Builder
     */
    public function apply($model, Repository $repository): Builder
    {
        return $model->with($this->with);
    }
}
