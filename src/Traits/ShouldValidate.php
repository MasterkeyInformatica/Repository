<?php

namespace Masterkey\Repository\Traits;

use Masterkey\Repository\Contracts\ValidatorContract;
use RepositoryException;
use ValidationException;

/**
 * ShouldValidate
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.1.0
 * @since   15/03/2018
 * @package Masterkey\Repository\Traits
 */
trait ShouldValidate
{
    /**
     * @return  void
     * @throws  RepositoryException
     */
    public function bootShouldValidate()
    {
        $validator = $this->validator();

        if ( ! is_null($validator) ) {
            $validator = $this->app->make($validator);

            if ( ! $validator instanceof ValidatorContract ) {
                throw new RepositoryException("Class {$validator} must be a implementation of Masterkey\\Repository\\Contracts\\ValidatorContract");
            }
        }

        $this->validator = $validator;
    }

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