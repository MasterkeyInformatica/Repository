<?php

namespace Masterkey\Repository\Traits;

use Illuminate\Database\Eloquent\Model;
use Prettus\Validator\Contracts\ValidatorInterface;
use RepositoryException;

/**
 * ClassMaker
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @package Masterkey\Repository\Traits
 */
trait ClassBuilder
{
    /**
     * @param   $eloquentModel
     * @return  Model
     * @throws  RepositoryException
     */
    public function makeModel($eloquentModel)
    {
        $model = $this->app->make($eloquentModel);

        if ( ! $model instanceof Model ) {
            throw new RepositoryException("Class {$model} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * @param   null  $validator
     * @return  null|ValidatorInterface
     * @throws  RepositoryException
     */
    public function makeValidator($validator = null)
    {
        if ( ! is_null($validator) ) {
            $validator = $this->app->make($validator);

            if ( ! $validator instanceof ValidatorInterface ) {
                throw new RepositoryException("Class {$validator} must be a implementation of Prettus\\Validator\\Contracts\\ValidatorInterface");
            }
        }

        return $this->validator = $validator;
    }

    /**
     * @throws  RepositoryException
     */
    public function resetModel()
    {
        $this->makeModel($this->model());
    }
}