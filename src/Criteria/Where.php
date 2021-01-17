<?php

namespace Masterkey\Repository\Criteria;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * Where
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class Where extends AbstractCriteria
{
    /** @var string|Closure */
    protected $column;

    /** @var mixed */
    protected $operator;

    /** @var mixed */
    protected $value;

    protected string $boolean;

    public function __construct($column, $operator = null, $value = null, string $boolean = 'and')
    {
        $this->column   = $column;
        $this->operator = $operator;
        $this->value    = $value;
        $this->boolean  = $boolean;
    }

    /**
     * @param Builder $model
     * @param Repository    $repository
     * @return Builder
     */
    public function apply($model, Repository $repository): Builder
    {
        return $model->where(
            $this->column,
            $this->operator,
            $this->value,
            $this->boolean
        );
    }
}