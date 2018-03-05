<?php

namespace Masterkey\Repository;

use Illuminate\Database\Query\Builder;
use Masterkey\Repository\Contracts\RepositoryContract as Repository;

/**
 * Criteria
 *
 * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version  3.0.0
 * @since    02/09/2017
 * @package  Masterkey\Repository
 */
abstract class Criteria
{
    /**
     * Apply a criteria on a model
     *
     * @param   Builder  $model
     * @param   Repository  $repository
     * @return  mixed
     */
    public abstract function apply($model, Repository $repository);
}
