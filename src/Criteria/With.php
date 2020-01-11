<?php

namespace Masterkey\Repository\Criteria;

use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;
use Masterkey\Repository\MultiArray;

/**
 * With
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.0
 * @package Masterkey\Repository\Criteria
 */
class With extends AbstractCriteria
{
    use MultiArray;

    /**
     * @var array
     */
    protected $with;

    /**
     * @param array ...$with
     */
    public function __construct(...$with)
    {
        $this->with = $this->variadicToArray($with);
    }

    /**
     * @param   \Illuminate\Database\Query\Builder $model
     * @param   Repository $repository
     * @return  \Illuminate\Database\Query\Builder
     */
    public function apply($model, Repository $repository)
    {
        return $model->with($this->with);
    }
}
