<?php

namespace Masterkey\Tests\Models;

use Illuminate\Database\Eloquent\Builder;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;
use Masterkey\Repository\AbstractCriteria;

class OrderFileByName extends AbstractCriteria
{
    public function apply(Builder $model, Repository $repository): Builder
    {
        return $model->orderBy('file');
    }
}