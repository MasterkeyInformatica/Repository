<?php

namespace Masterkey\Repository\Exceptions;

use Exception;

/**
 * ModelNotDeletedException
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   02/09/2017
 * @package Masterkey\Repository\Exceptions
 */
class ModelNotDeletedException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'O registro não foi apagado. Tente Novamente';
}