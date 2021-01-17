<?php

namespace Masterkey\Repository\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;
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
    public function all(array $columns = ['*']): Collection;

    public function with(array $relations);

    public function pluck(string $value, ?string $key = null): array;

    public function paginate(int $perPage = 15, array $columns = ['*'], string $method = 'paginate'): AbstractPaginator;

    public function simplePaginate(int $perPage = 15, array $columns = ['*']): AbstractPaginator;

    public function find(int $id, $columns = ['*']): ?Model;

    public function findOrFail(int $id, array $columns = ['*']): Model;

    public function first(array $columns = ['*']): ?Model;

    public function last(array $columns = ['*']): ?Model;

    public function findBy(string $field, $value, array $columns = ['*']): ?Model;

    public function findAllBy($field, $value, array $columns = ['*']): Collection;

    public function exists(): bool;

    public function doesntExists(): bool;

    public function increment(string $column, $amount = 1, array $extra = []);

    public function decrement(string $column, $amount = 1, array $extra = []);
}