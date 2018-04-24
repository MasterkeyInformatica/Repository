<?php

namespace Masterkey\Repository\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * NeedsBeSearchable
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   24/04/2018
 * @package Masterkey\Repository\Traits
 */
trait NeedsBeSearchable
{
    /**
     * @param   array  $columns
     * @return  Collection
     */
    public function all(array $columns = ['*']) : Collection
    {
        $this->applyCriteria();

        return $this->model->get($columns);
    }

    /**
     * @param   array  $relations
     * @return  $this
     */
    public function with(array $relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * @param   string  $value
     * @param   null|string  $key
     * @return  array
     */
    public function pluck(string $value, $key = null) : array
    {
        $this->applyCriteria();

        return $this->model->pluck($value, $key)->toArray();
    }

    /**
     * @param   int  $perPage
     * @param   array  $columns
     * @param   string  $method
     * @return  Paginator|LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], $method = 'paginate')
    {
        $this->applyCriteria();

        $results = $this->model->{$method}($perPage, $columns);

        $results->appends($this->app->make('request')->query());

        return $results;
    }

    /**
     * @param   int  $perPage
     * @param   array  $columns
     * @return  LengthAwarePaginator
     */
    public function simplePaginate(int $perPage = 15, array $columns = ['*'])
    {
        return $this->paginate($perPage, $columns, 'simplePaginate');
    }

    /**
     * @param   int  $id
     * @param   array  $columns
     * @return  Model
     */
    public function find(int $id, $columns = array('*')) : Model
    {
        $this->applyCriteria();

        return $this->model->findOrFail($id, $columns);
    }

    /**
     * @param   array  $columns
     * @return  Model
     */
    public function first(array $columns = ['*']) : Model
    {
        $this->applyCriteria();

        return $this->model->first($columns);
    }

    /**
     * @param   array  $columns
     * @return  Model
     */
    public function last(array $columns = ['*']) : Model
    {
        $this->applyCriteria();

        return $this->orderBy($this->getKeyName(), 'desc')->first($columns);
    }

    /**
     * @param   string  $attribute
     * @param   mixed  $value
     * @param   array  $columns
     * @return  Model
     */
    public function findBy($attribute, $value, array $columns = ['*']) : Model
    {
        $this->applyCriteria();

        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    /**
     * @param   string  $attribute
     * @param   mixed  $value
     * @param   array  $columns
     * @return  Collection
     */
    public function findAllBy($attribute, $value, array $columns = ['*']) : Collection
    {
        $this->applyCriteria();

        return $this->model->where($attribute, '=', $value)->get($columns);
    }
}