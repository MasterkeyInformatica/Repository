<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Eloquent\Builder;
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
    protected array $columns;

    public function __construct(...$columns)
    {
        $columns = Arr::flatten($columns);

        if ( empty($columns) ) {
            $columns = ['*'];
        }

        $this->columns = $columns;
    }

    public function apply(Builder $model, Repository $repository): Builder
    {
        return $model->select($this->columns);
    }
}
