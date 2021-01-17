<?php

namespace Masterkey\Repository\Contracts;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use PDO;

/**
 * RepositoryContract
 *
 * Interface that rules repository classes
 *
 * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version  3.1.1
 * @package  Masterkey\Repository\Contracts
 */
interface RepositoryInterface
{
    public function makeModel(string $model);

    public function getBuilder(): Builder;

    public function getFieldsSearchable(): array;

    public function bootTraits(): void;

    public function connection(): Connection;

    public function getPDO(): PDO;

    public function enableAutoCommit(): bool;

    public function disableAutoCommit(): bool;

    public function transaction(Closure $closure, int $attempts = 1);

    public function enableQueryLog(): void;

    public function disableQueryLog(): void;

    public function getQueryLog(): array;

    public function getLastQuery(): ?string;

    public function select(string $query, array $bindings = [], bool $useReadPdo = true): Collection;

    public function selectOne(string $query, array $bindings = [], bool $useReadPdo = true): ?Model;

    public function statement(string $query, array $bindings = []): bool;

    public function raw(string $value): Expression;

    public function chunk(int $count, callable $callback): bool;

    public function chunkById(int $count, callable $callback, string $column = null, string $alias = null): bool;

    public function count(string $column = '*'): int;

    public function max(string $column);

    public function min(string $column);

    public function avg(string $column);

    public function sum(string $column);

    public function create(array $data);

    public function firstOrCreate(array $data);

    public function firstOrNew(array $data);

    public function save(array $data);

    public function insert(array $data) : bool;

    public function update(array $data, int $id);

    public function delete(int $id) : bool;

    public function destroy(array $records) : bool;

    public function sync(int $id, string $relation, string $attributes, bool $detach = true);

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

    public function findAllBy(string $field, $value, array $columns = ['*']): Collection;

    public function exists(): bool;

    public function doesntExists(): bool;

    public function increment(string $column, $amount = 1, array $extra = []): int;

    public function decrement(string $column, $amount = 1, array $extra = []): int;

    public function limit(int $limit);

    public function offset(int $offset);

    public function having(string $column, string $operator, $value);

    public function orderBy(string $column, $order = 'asc');

    public function groupBy(...$columns);
}
