<?php

namespace Masterkey\Repository\Contracts;

/**
 * SortableInterface
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   24/04/2018
 * @package Masterkey\Repository\Contracts
 */
interface SortableInterface
{
    /**
     * @param   int  $limit
     */
    public function limit(int $limit);

    /**
     * @param   int  $offset
     */
    public function offset(int $offset);

    /**
     * @param   string  $column
     * @param   string  $operator
     * @param   mixed  $value
     */
    public function having(string $column, string $operator, $value);

    /**
     * @param   string  $column
     * @param   string  $order
     */
    public function orderBy(string $column, $order = 'asc');

    /**
     * @param   mixed ...$columns
     */
    public function groupBy(...$columns);
}