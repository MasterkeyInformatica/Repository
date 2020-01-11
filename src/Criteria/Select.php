<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Support\Arr;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * Select
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.1
 * @package Masterkey\Repository\Criteria
 */
class Select extends AbstractCriteria
{
    /**
     * @var array
     */
    protected $columns;

    /**
     * @param array ...$columns
     */
    public function __construct(...$columns)
    {
        $columns = Arr::collapse($columns);

        if ( empty($columns) ) {
            $columns = ['*'];
        }

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
