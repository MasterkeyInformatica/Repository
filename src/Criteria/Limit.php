<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * Limit
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class Limit extends AbstractCriteria
{
    protected int $limit;

    public function __construct(int $limit = 15)
    {
        $this->limit = $limit;
    }

    /**
     * @param Builder $model
     * @param Repository    $repository
     * @return Builder
     */
    public function apply($model, Repository $repository): Builder
    {
        return $model->take($this->limit);
    }
}
