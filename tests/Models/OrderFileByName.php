<?php

namespace Masterkey\Tests\Models;

use Masterkey\Repository\Contracts\RepositoryInterface as Repository;
use Masterkey\Repository\AbstractCriteria;

class OrderFileByName extends AbstractCriteria
{
    public function apply($model, Repository $repository)
    {
        return $model->orderBy('file');
    }
}