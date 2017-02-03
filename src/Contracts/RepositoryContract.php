<?php

    namespace Masterkey\Repository\Contracts;

    /**
     * RepositoryContract
     *
     * Interface that rules repository classes
     *
     * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
     * @version  1.2.0
     * @since    03/01/2017
     * @package  Masterkey\Repository\Contracts
     */
    interface RepositoryContract
    {
        /**
         * @param   array  $columns
         * @return  mixed
         */
        public function all($columns = ['*']);

        /**
         * @param   array  $relations
         * @return  mixed
         */
        public function with(array $relations);

        /**
         * @param   string  $value
         * @param   string|null  $key
         * @return  array
         */
        public function pluck($value, $key = null);

        /**
         * @param   int  $perPage
         * @param   array $columns
         * @return  mixed
         */
        public function paginate($perPage = 15, $columns = ['*']);

        /**
         * @param   array  $data
         * @return  mixed
         */
        public function create(array $data);

        /**
         * @param   array  $data
         * @return  mixed
         */
        public function firstOrCreate(array $data);

        /**
         * @param   array  $data
         * @return  mixed
         */
        public function save(array $data);

        /**
         * @param   array  $data
         * @return  mixed
         */
        public function massInsert(array $data);

        /**
         * @param   int  $id
         * @param   array  $data
         * @return  mixed
         */
        public function update($id, array $data);

        /**
         * @param   array  $data
         * @return  mixed
         */
        public function massUpdate(array $data);

        /**
         * @param   int  $id
         * @return  bool
         */
        public function delete($id);

        /**
         * @param   int  $id
         * @return  bool
         */
        public function destroy($id);

        /**
         * @param   int  $id
         * @param   array  $columns
         * @return  mixed
         */
        public function find($id, $columns = ['*']);

        /**
         * @param   array  $columns
         * @return  mixed
         */
        public function first($columns = ['*']);

        /**
         * @param   array  $columns
         * @return  mixed
         */
        public function last($columns = ['*']);

        /**
         * @param   string  $field
         * @param   mixed  $value
         * @param   array  $columns
         * @return  mixed
         */
        public function findBy($field, $value, $columns = ['*']);

        /**
         * @param   string  $field
         * @param   mixed  $value
         * @param   array  $columns
         * @return  mixed
         */
        public function findAllBy($field, $value, $columns = ['*']);

        /**
         * @param   string  $where
         * @param   array  $columns
         * @return  mixed
         */
        public function findWhere($where, $columns = ['*']);

        /**
         * @return  integer
         */
        public function count();

        /**
         * @param   string  $column
         * @return  int|float
         */
        public function max($column);

        /**
         * @param   string  $column
         * @return  int|float
         */
        public function min($column);

        /**
         * @param   string  $column
         * @return  int|float
         */
        public function avg($column);

        /**
         * @param   string  $column
         * @return  int|float
         */
        public function sum($column);

        /**
         * @return  \Illuminate\Database\Eloquent\Builder
         */
        public function getBuilder();
    }
