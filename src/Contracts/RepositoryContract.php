<?php

namespace Masterkey\Repository\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * RepositoryContract
 *
 * Interface that rules repository classes
 *
 * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version  3.1.0
 * @since    05/03/2018
 * @package  Masterkey\Repository\Contracts
 */
interface RepositoryContract
{
    public function all(array $columns = ['*']);

    public function with(array $relations);

    public function pluck(string $value, $key = null);

    public function paginate(int $perPage = 15, array $columns = ['*'], $method = 'paginate');

    public function simplePaginate(int $perPage = 15, array $columns = ['*']);

    public function create(array $data);

    public function firstOrCreate(array $data);

    public function firstOrNew(array $data);

    public function save(array $data);

    public function insert(array $data) : bool;

    public function update(int $id, array $data);

    public function massUpdate(array $data);

    public function delete(int $id) : bool;

    public function destroy(array $records) : bool;

    public function find(int $id, $columns = ['*']);

    public function first(array $columns = ['*']);

    public function last(array $columns = ['*']);

    public function findBy($field, $value, array $columns = ['*']);

    public function findAllBy($field, $value, array $columns = ['*']);

    public function count() : int ;

    public function max(string $column);

    public function min(string $column);

    public function avg(string $column);

    public function sum(string $column);

    public function getBuilder() : Builder;

    public function getFieldsSearchable();

    public function sync($id, $relation, $attributes, $detach = true);

    public function limit(int $limit);
}
