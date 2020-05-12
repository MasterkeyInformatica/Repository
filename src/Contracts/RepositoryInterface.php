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
    /**
     * @param string $model
     */
    public function makeModel($model);

    /**
     * @param string|null $presenter
     * @return mixed
     */
    public function makePresenter(string $presenter = null);

    /**
     * @return Builder
     */
    public function getBuilder() : Builder;

    /**
     * @return mixed
     */
    public function getFieldsSearchable();

    /**
     * @return  void
     */
    public function bootTraits();

    /**
     * @return Connection
     */
    public function connection() : Connection;

    /**
     * @return PDO
     */
    public function getPDO() : PDO;

    /**
     * @return bool
     */
    public function enableAutoCommit() : bool;

    /**
     * @return bool
     */
    public function disableAutoCommit() : bool;

    /**
     * @param Closure $closure
     * @return mixed
     */
    public function transaction(Closure $closure);

    /**
     * @return void
     */
    public function enableQueryLog();

    /**
     * @return void
     */
    public function disableQueryLog();

    /**
     * @return array
     */
    public function getQueryLog() : array;

    /**
     * @return string|null
     */
    public function getLastQuery() : ? string;

    /**
     * @param string $query
     * @param array  $bindings
     * @param bool   $useReadPdo
     * @return Collection
     */
    public function select(string $query, array $bindings = [], bool $useReadPdo = true) : Collection;

    /**
     * @param string $query
     * @param array  $bindings
     * @param bool   $useReadPdo
     * @return Model|null
     */
    public function selectOne(string $query, array $bindings = [], bool $useReadPdo = true) : ? Model;

    /**
     * @param string $query
     * @param array  $bindings
     * @return bool
     */
    public function statement(string $query, array $bindings = []) : bool;

    /**
     * @param string $value
     * @return Expression
     */
    public function raw(string $value) : Expression;

    public function chunk(int $count, callable $callback);

    public function chunkById(int $count, callable $callback, string $column = null, string $alias = null);
}
