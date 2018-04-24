<?php

namespace Masterkey\Repository\Traits;

/**
 * NeedsBeCountable
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   24/04/2018
 * @package Masterkey\Repository\Traits
 */
trait NeedsBeCountable
{
    /**
     * @return  integer
     */
    public function count() : int
    {
        $this->applyCriteria();

        return $this->model->count();
    }

    /**
     * @param   string  $column
     * @return  int|float
     */
    public function max(string $column)
    {
        $this->applyCriteria();

        return $this->model->max($column);
    }

    /**
     * @param   string  $column
     * @return  int|float
     */
    public function min(string $column)
    {
        $this->applyCriteria();

        return $this->model->min($column);
    }

    /**
     * @param   string  $column
     * @return  int|float
     */
    public function avg(string $column)
    {
        $this->applyCriteria();

        return $this->model->avg($column);
    }

    /**
     * @param   string  $column
     * @return  int|float
     */
    public function sum(string $column)
    {
        $this->applyCriteria();

        return $this->model->sum($column);
    }
}