<?php

namespace Masterkey\Tests\Models;

use Masterkey\Repository\Contracts\RepositoryInterface as Repository;
use Masterkey\Repository\Criteria;

class OrderFileByName extends Criteria
{
    public  function apply($model, Repository $repository)
    {
        return $model->orderBy('file');
    }
}