<?php

namespace Masterkey\Repository\Criteria;

use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * Having
 *
 * @author Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class Having extends AbstractCriteria
{
    /**
     * @var string
     */
    protected $column;

    /**
     * @var null
     */
    protected $operator;

    /**
     * @var null
     */
    protected $value;

    /**
     * @var string
     */
    protected $boolean;

    /**
     * @param string $column
     * @param null   $operator
     * @param null   $value
     * @param string $boolean
     */
    public function __construct(string $column, $operator = null, $value = null, $boolean = 'and')
    {
        $this->column = $column;
        $this->operator = $operator;
        $this->value = $value;
        $this->boolean = $boolean;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $model
     * @param Repository                         $repository
     * @return \Illuminate\Database\Query\Builder|mixed
     */
    public function apply($model, Repository $repository)
    {
        return $model->having(
            $this->column,
            $this->operator,
            $this->value,
            $this->boolean
        );
    }
}