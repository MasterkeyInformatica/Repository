<?php

    namespace Masterkey\Repository;

    use Masterkey\Repository\Contracts\RepositoryContract as Repository

    /**
     * Criteria
     *
     * Classe abstrata que define a diretriz para o funcionamento das classes
     * que extenderão à esta
     *
     * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
     * @version  1.0.0
     * @since    26/12/2016
     * @package  Masterkey\Repository
     */
    abstract class Criteria
    {
        /**
         * Apply a criteria on a model
         * 
         * @param   mixed  $model
         * @param   Repository  $repository
         * @return  mixed
         */
        public abstract function apply($model, Repository $repository)
    }
