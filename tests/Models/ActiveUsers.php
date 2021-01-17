<?php

namespace Masterkey\Tests\Models;

use Illuminate\Database\Eloquent\Builder;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * ActiveUsers
 *
 * @package Masterkey\Tests\Models
 */
class ActiveUsers extends AbstractCriteria
{
    public function apply(Builder $model, Repository $repository): Builder
    {
        return $model->where('active', true);
    }
}
