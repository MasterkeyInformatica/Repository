<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Query\Builder;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * WhereRaw
 *
 * @author Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class WhereRaw extends AbstractCriteria
{
    /**
     * @var string
     */
    protected $query;

    /**
     * @var array
     */
    protected $bindings;

    /**
     * @var string
     */
    protected $boolean;

    /**
     * @param string $query
     * @param array  $bindings
     * @param string $boolean
     */
    public function __construct(string $query, array $bindings = [], string $boolean = 'and')
    {
        $this->query = $query;
        $this->bindings = $bindings;
        $this->boolean = $boolean;
    }

    /**
     * @param Builder    $model
     * @param Repository $repository
     * @return Builder
     */
    public function apply($model, Repository $repository)
    {
        return $model->whereRaw($this->query, $this->bindings, $this->boolean);
    }
}
