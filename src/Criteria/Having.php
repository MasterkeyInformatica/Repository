<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * Having
 *
 * @author Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.0
 * @package Masterkey\Repository\Criteria
 */
class Having extends AbstractCriteria
{
    protected string $column;

    /** @var null */
    protected $operator;

    /** @var null */
    protected $value;

    protected string $boolean;

    public function __construct(string $column, $operator = null, $value = null, string $boolean = 'and')
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
        return $model->having(
            $this->column,
            $this->operator,
            $this->value,
            $this->boolean
        );
    }
}