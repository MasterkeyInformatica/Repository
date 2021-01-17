<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * SelectRaw
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class SelectRaw extends AbstractCriteria
{
    protected string $expression;

    protected array $bindings;

    public function __construct(string $expression, array $bindings = [])
    {
        $this->expression = $expression;
        $this->bindings   = $bindings;
    }

    public function apply(Builder $model, Repository $repository): Builder
    {
        return $model->selectRaw($this->expression, $this->bindings);
    }
}
