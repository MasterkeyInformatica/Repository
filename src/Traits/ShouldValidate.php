<?php

namespace Masterkey\Repository\Traits;

use Masterkey\Repository\Contracts\ValidatorContract;
use ValidationException;

/**
 * ShouldValidate
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   06/03/2018
 * @package Masterkey\Repository\Traits
 */
trait ShouldValidate
{
    /**
     * @param   array  $data
     * @return  bool
     * @throws  ValidationException
     */
    public function validateBeforeInsert(array $data)
    {
        if ( ! is_null($this->validator) ) {
            return $this->validator->validate($data, ValidatorContract::INSERT_RULES);
        }

        return true;
    }

    /**
     * @param   array  $data
     * @return  bool
     * @throws  ValidationException
     */
    public function validateBeforeUpdate(array $data)
    {
        if ( ! is_null($this->validator) ) {
            return $this->validator->validate($data, ValidatorContract::UPDATE_RULES);
        }

        return true;
    }
}