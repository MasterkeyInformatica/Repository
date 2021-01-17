<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * WhereNull
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.0
 * @package Masterkey\Repository\Criteria
 */
class WhereNull extends AbstractCriteria
{
    protected string $column;

    protected string $boolean;

    protected bool $not;

    public function __construct(string $column, string $boolean = 'and', bool $not = false)
    {
        $this->column  = $column;
        $this->boolean = $boolean;
        $this->not     = $not;
    }

    /**
     * @param Builder $model
     * @param Repository    $repository
     * @return Builder
     */
    public function apply($model, Repository $repository): Builder
    {
        return $model->whereNull($this->column, $this->boolean, $this->not);
    }
}