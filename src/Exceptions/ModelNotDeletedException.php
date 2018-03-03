<?php

/**
 * ModelNotDeletedException
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.0
 * @since   03/03/2018
 */
class ModelNotDeletedException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'O registro não foi apagado. Tente Novamente';
}