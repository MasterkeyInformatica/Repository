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
    /**
     * @param Builder    $model
     * @param Repository $repository
     * @return Builder
     */
    public function apply($model, Repository $repository): Builder
    {
        return $model->where('active', true);
    }
}
