<?php

namespace Masterkey\Repository\Traits;

use Illuminate\Database\Eloquent\Model;
use Masterkey\Repository\Contracts\ValidatorInterface;
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
     * @throws  RepositoryException
     */
    public function resetModel()
    {
        $this->makeModel($this->model());
    }
}