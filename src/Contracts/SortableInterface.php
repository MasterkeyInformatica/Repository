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
    public function limit(int $limit);

    public function offset(int $offset);

    public function having(string $column, string $operator, $value);

    public function orderBy(string $column, $order = 'asc');

    public function groupBy(...$columns);
}