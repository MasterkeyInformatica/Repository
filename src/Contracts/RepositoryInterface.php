<?php

namespace Masterkey\Repository\Contracts;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
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
}
