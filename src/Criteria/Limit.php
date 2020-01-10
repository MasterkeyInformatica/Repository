<?php

namespace Masterkey\Repository\Criteria;

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
    /**
     * @var int
     */
    protected $limit;

    /**
     * @param   int  $limit
     */
    public function __construct(int $limit = 15)
    {
        $this->limit = $limit;
    }

    /**
     * @param   \Illuminate\Database\Query\Builder  $model
     * @param   Repository  $repository
     * @return  \Illuminate\Database\Query\Builder
     */
    public function apply($model, Repository $repository)
    {
        return $model->take($this->limit);
    }
}
