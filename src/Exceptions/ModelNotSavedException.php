<?php

/**
 * ModelNotSavedException
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 4.0.0
 * @since   03/03/2018
 */
class ModelNotSavedException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'Não foi possível salvar os dados. Tente novamente';
}