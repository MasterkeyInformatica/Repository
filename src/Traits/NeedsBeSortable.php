<?php

namespace Masterkey\Repository\Traits;

/**
 * NeedsBeSortable
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   24/04/2018
 * @package Masterkey\Repository\Traits
 */
trait NeedsBeSortable
{
    /**
     * @param   int  $limit
     * @return  $this
     */
    public function limit(int $limit)
    {
        $this->model = $this->model->limit($limit);

        return $this;
    }

    /**
     * @param   int  $offset
     * @return  $this
     */
    public function offset(int $offset)
    {
        $this->model = $this->model->offset($offset);

        return $this;
    }

    /**
     * @param   string  $column
     * @param   string  $operator
     * @param   mixed  $value
     * @return  $this
     */
    public function having(string $column, string $operator, $value)
    {
        $this->groupBy($column);

        $this->model = $this->model->having($column, $operator, $value);

        return $this;
    }

    /**
     * @param   string  $column
     * @param   string  $order
     * @return  $this
     */
    public function orderBy(string $column, $order = 'asc')
    {
        $this->model = $this->model->orderBy($column, $order);

        return $this;
    }

    /**
     * @param   mixed ...$columns
     * @return  $this
     */
    public function groupBy(...$columns)
    {
        $this->model = $this->model->groupBy($columns);

        return $this;
    }
}