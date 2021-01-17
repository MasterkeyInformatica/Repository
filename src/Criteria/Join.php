<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * Join
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.0
 * @package Masterkey\Repository\Criteria
 */
class Join extends AbstractCriteria
{
    protected string $table;

    /** @var string|\Closure */
    protected $first;

    protected ?string $operator;

    protected ?string $second;

    protected string $type;

    protected bool $where;

    public function __construct(string $table, $first, $operator = null, $second = null, string $type = 'inner', bool $where = false)
    {
        $this->table    = $table;
        $this->first    = $first;
        $this->operator = $operator;
        $this->second   = $second;
        $this->type     = $type;
        $this->where    = $where;
    }

    /**
     * @param Builder $model
     * @param Repository    $repository
     * @return Builder
     */
    public function apply($model, Repository $repository): Builder
    {
        return $model->join(
            $this->table,
            $this->first,
            $this->operator,
            $this->second,
            $this->type,
            $this->where
        );
    }
}