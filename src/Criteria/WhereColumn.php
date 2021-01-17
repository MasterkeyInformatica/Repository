<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * WhereColumn
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class WhereColumn extends AbstractCriteria
{
    protected string $first;

    /** @var string|\Closure|null */
    protected $operator;

    protected ?string $second;

    protected string $boolean;

    public function __construct(string $first, $operator = null, ?string $second = null, string $boolean = 'and')
    {
        $this->first    = $first;
        $this->operator = $operator;
        $this->second   = $second;
        $this->boolean  = $boolean;
    }

    /**
     * @param Builder $model
     * @param Repository    $repository
     * @return Builder
     */
    public function apply($model, Repository $repository): Builder
    {
        return $model->whereColumn(
            $this->first,
            $this->operator,
            $this->second,
            $this->boolean
        );
    }
}