<?php

namespace Masterkey\Repository\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * RepositoryContract
 *
 * Interface that rules repository classes
 *
 * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version  3.0.0
 * @since    02/09/2017
 * @package  Masterkey\Repository\Contracts
 */
interface RepositoryContract
{
    /**
     * @param   array  $columns
     * @return  mixed
     */
    public function all(array $columns = ['*']);

    /**
     * @param   array  $relations
     * @return  mixed
     */
    public function with(array $relations);

    /**
     * @param   string  $value
     * @param   string|null  $key
     * @return  array
     */
    public function pluck(string $value, $key = null);

    /**
     * @param   int  $perPage
     * @param   array $columns
     * @return  mixed
     */
    public function paginate(int $perPage = 15, array $columns = ['*']);

    /**
     * @param   array  $data
     * @return  mixed
     */
    public function create(array $data);

    /**
     * @param   array  $data
     * @return  mixed
     */
    public function firstOrCreate(array $data);

    /**
     * @param   array $data
     * @return  mixed
     */
    public function firstOrNew(array $data);

    /**
     * @param   array  $data
     * @return  mixed
     */
    public function save(array $data);

    /**
     * @param   array  $data
     * @return  bool
     */
    public function massInsert(array $data) : bool;

    /**
     * @param   int  $id
     * @param   array  $data
     * @return  mixed
     */
    public function update(int $id, array $data);

    /**
     * @param   array  $data
     * @return  mixed
     */
    public function massUpdate(array $data);

    /**
     * @param   int  $id
     * @return  bool
     */
    public function delete(int $id) : bool;

    /**
     * @param   array $records
     * @return  bool
     */
    public function destroy(array $records) : bool;

    /**
     * @param   int  $id
     * @param   array  $columns
     * @return  mixed
     */
    public function find(int $id, $columns = ['*']);

    /**
     * @param   array  $columns
     * @return  mixed
     */
    public function first(array $columns = ['*']);

    /**
     * @param   array  $columns
     * @return  mixed
     */
    public function last(array $columns = ['*']);

    /**
     * @param   string  $field
     * @param   mixed  $value
     * @param   array  $columns
     * @return  mixed
     */
    public function findBy($field, $value, array $columns = ['*']);

    /**
     * @param   string  $field
     * @param   mixed  $value
     * @param   array  $columns
     * @return  mixed
     */
    public function findAllBy($field, $value, array $columns = ['*']);

    /**
     * @return  integer
     */
    public function count() : int ;

    /**
     * @param   string  $column
     * @return  int|float
     */
    public function max(string $column);

    /**
     * @param   string  $column
     * @return  int|float
     */
    public function min(string $column);

    /**
     * @param   string  $column
     * @return  int|float
     */
    public function avg(string $column);

    /**
     * @param   string  $column
     * @return  int|float
     */
    public function sum(string $column);

    /**
     * @return  \Illuminate\Database\Eloquent\Builder
     */
    public function getBuilder() : Builder;
}
