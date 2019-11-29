<?php

namespace Masterkey\Repository\Contracts;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Masterkey\Repository\AbstractPresenter;
use PDO;

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
    public function getConnection() : Connection;

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
}
