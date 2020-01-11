<?php

namespace Masterkey\Repository\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * SearchableInterface
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   24/04/2018
 * @package Masterkey\Repository\Contracts
 */
interface SearchableInterface
{
    /**
     * @param   array  $columns
     * @return  Collection
     */
    public function all(array $columns = ['*']) : Collection;

    /**
     * @param   array  $relations
     */
    public function with(array $relations);

    /**
     * @param   string  $value
     * @param   null  $key
     * @return  array
     */
    public function pluck(string $value, $key = null) : array;

    /**
     * @param   int  $perPage
     * @param   array  $columns
     * @param   string  $method
     * @return  Paginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], $method = 'paginate');

    /**
     * @param   int  $perPage
     * @param   array  $columns
     * @return  LengthAwarePaginator
     */
    public function simplePaginate(int $perPage = 15, array $columns = ['*']);

    /**
     * @param   int  $id
     * @param   array  $columns
     * @return  Model
     */
    public function find(int $id, $columns = ['*']) : ? Model;

    /**
     * @param int   $id
     * @param array $columns
     * @return Model
     */
    public function findOrFail(int $id, $columns = ['*']) : Model;

    /**
     * @param   array  $columns
     * @return  Model
     */
    public function first(array $columns = ['*']) : ?Model;

    /**
     * @param   array  $columns
     * @return  Model
     */
    public function last(array $columns = ['*']) : ?Model;

    /**
     * @param   string  $field
     * @param   mixed  $value
     * @param   array  $columns
     * @return  Model
     */
    public function findBy($field, $value, array $columns = ['*']) : ?Model;

    /**
     * @param   string  $field
     * @param   mixed  $value
     * @param   array  $columns
     * @return  Collection
     */
    public function findAllBy($field, $value, array $columns = ['*']) : Collection;
}