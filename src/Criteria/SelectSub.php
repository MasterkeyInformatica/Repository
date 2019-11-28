<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Query\Builder;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * SelectSub
 *
 * @author Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class SelectSub extends AbstractCriteria
{
    /**
     * @var string
     */
    protected $query;

    /**
     * @var string
     */
    protected $as;

    /**
     * @param string $query
     * @param string $as
     */
    public function __construct(string $query, string $as)
    {
        $this->query = $query;
        $this->as = $as;
    }

    /**
     * @param Builder    $model
     * @param Repository $repository
     * @return Builder
     */
    public function apply($model, Repository $repository)
    {
        return $model->selectSub($this->query, $this->as);
    }
}