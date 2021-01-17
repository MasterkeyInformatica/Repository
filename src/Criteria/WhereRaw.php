<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * WhereRaw
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class WhereRaw extends AbstractCriteria
{
    protected string $query;

    protected array $bindings;

    protected string $boolean;

    public function __construct(string $query, array $bindings = [], string $boolean = 'and')
    {
        $this->query    = $query;
        $this->bindings = $bindings;
        $this->boolean  = $boolean;
    }

    public function apply(Builder $model, Repository $repository): Builder
    {
        return $model->whereRaw($this->query, $this->bindings, $this->boolean);
    }
}
