<?php

namespace Masterkey\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * AbstractCriteria
 *
 * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version  4.0.0
 * @package  Masterkey\Repository
 */
abstract class AbstractCriteria
{
    /**
     * @param Builder $model
     * @param Repository    $repository
     * @return Builder
     */
    public abstract function apply($model, Repository $repository): Builder;
}
