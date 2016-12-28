<?php

    namespace Masterkey\Repository\Contracts;

    /**
     * RepositoryContract
     *
     * Interface that rules repository classes
     *
     * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
     * @version  1.0.0
     * @since    28/12/2016
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
         * @param   int  $perPage
         * @param   array $columns
         * @return  mixed
         */
        public function paginate($perPage = 15, $columns = ['*']);

        /**
         * @param   array  $data
         * @return  mixed
         */
        public function save(array $data);

        /**
         * @param   array  $data
         * @return  mixed
         */
        public function create(array $data);

        /**
         * @param   int  $id
         * @param   array  $data
         * @return  mixed
         */
        public function update($id, array $data);

        /**
         * @param   int  $id
         * @return  mixed
         */
        public function delete($id);

        /**
         * @param   int  $id
         * @param   array  $columns
         * @return  mixed
         */
        public function find($id, $columns = ['*']);

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
    }
