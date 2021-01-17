<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * Between
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class Between extends AbstractCriteria
{
    protected string $column;

    protected array $values;

    protected bool $boolean;

    protected bool $not;

    public function __construct(string $column, array $values, string $boolean = 'and', bool $not = false)
    {
        $this->column  = $column;
        $this->values  = $values;
        $this->boolean = $boolean;
        $this->not     = $not;
    }

    public function apply(Builder $model, Repository $repository): Builder
    {
        return $model->whereBetween(
            $this->column,
            $this->values,
            $this->boolean,
            $this->not
        );
    }
}