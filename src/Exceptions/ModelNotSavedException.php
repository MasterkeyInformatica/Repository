<?php

    namespace Masterkey\Repository\Exceptions;

    use Exception;

    /**
     * ModelNotSavedException
     *
     * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
     * @version 3.0.0
     * @since   02/09/2017
     * @package Masterkey\Repository\Exceptions\Model
     */
    class ModelNotSavedException extends Exception
    {
        /**
         * @var string
         */
        protected $message = 'Não foi possível salvar os dados. Tente novamente';
    }