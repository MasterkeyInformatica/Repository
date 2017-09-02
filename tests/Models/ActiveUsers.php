<?php

namespace Masterkey\Tests\Models;

use Masterkey\Repository\Criteria;
use Masterkey\Repository\Contracts\RepositoryContract as Repository;

/**
 * ActiveUsers
 *
 * @package Masterkey\Tests\Models
 */
class ActiveUsers extends Criteria
{
    /**
     * @param   Builder  $model
     * @param   Repository $repository
     * @return  Builder
     */
    public function apply($model, Repository $repository)
    {
        return $model->where('active', true);
    }
}
