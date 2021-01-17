<?php

namespace Masterkey\Repository\Contracts;

/**
 * CountableInterface
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   24/04/2018
 * @package Masterkey\Repository\Contracts
 */
interface CountableInterface
{
    public function count(string $column = '*'): int;

    public function max(string $column);

    public function min(string $column);

    public function avg(string $column);

    public function sum(string $column);
}