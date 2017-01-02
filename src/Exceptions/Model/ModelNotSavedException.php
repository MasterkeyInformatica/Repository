<?php

    namespace Masterkey\Repository\Exceptions\Model;

    use Exception;

    /**
     * ModelNotSavedException
     *
     * Throwed when the model is not saved
     *
     * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
     * @version 1.0.0
     * @since   02/01/2017
     * @package Masterkey\Repository\Exceptions\Model
     */
    class ModelNotSavedException extends Exception
    {
        protected $message = 'Não foi possível salvar os dados. Tente novamente';
    }