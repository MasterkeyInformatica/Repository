<?php

namespace Masterkey\Repository\Criteria;

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
    /**
     * @var string
     */
    protected $table;

    /**
     * @var string|\Closure
     */
    protected $first;

    /**
     * @var null
     */
    protected $operator;

    /**
     * @var null
     */
    protected $second;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $where;

    /**
     * @param   string  $table
     * @param   string|\Closure  $first
     * @param   string|null $operator
     * @param   string|null $second
     * @param   string|string $type
     * @param   bool $where
     */
    public function __construct($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        $this->table = $table;
        $this->first = $first;
        $this->operator = $operator;
        $this->second = $second;
        $this->type = $type;
        $this->where = $where;
    }

    /**
     * @param   \Illuminate\Database\Query\Builder $model
     * @param   Repository $repository
     * @return  \Illuminate\Database\Query\Builder|mixed
     */
    public function apply($model, Repository $repository)
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