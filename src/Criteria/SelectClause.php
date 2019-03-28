<?php

namespace Masterkey\Repository\Criteria;

use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * SelectClause
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   28/03/2019
 * @package Masterkey\Repository\Criteria
 */
class SelectClause extends AbstractCriteria
{
    /**
     * @var array
     */
    protected $columns;

    /**
     * @param   mixed ...$columns
     */
    public function __construct(...$columns)
    {
        $this->columns = $columns;
    }

    /**
     * @param   \Illuminate\Database\Query\Builder  $model
     * @param   Repository  $repository
     * @return  \Illuminate\Database\Query\Builder|mixed
     */
    public function apply($model, Repository $repository)
    {
        return $model->select($this->columns);
    }
}