<?php

namespace Masterkey\Repository;

use Illuminate\Database\Eloquent\Builder;
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
    public abstract function apply(Builder $model, Repository $repository): Builder;
}
