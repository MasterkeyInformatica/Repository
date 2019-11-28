<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Query\Builder;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * WhereNull
 *
 * @author Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class WhereNull extends AbstractCriteria
{
    /**
     * @var string
     */
    protected $column;

    /**
     * @var string
     */
    protected $boolean;

    /**
     * @var bool
     */
    protected $not;

    /**
     * @param string $column
     * @param string $boolean
     * @param bool   $not
     */
    public function __construct(string $column, string $boolean = 'and', bool $not = false)
    {
        $this->column = $column;
        $this->boolean = $boolean;
        $this->not = $not;
    }

    /**
     * @param Builder    $model
     * @param Repository $repository
     * @return Builder
     */
    public function apply($model, Repository $repository)
    {
        return $model->whereNull($this->column, $this->boolean, $this->not);
    }
}