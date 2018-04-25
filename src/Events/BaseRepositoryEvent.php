<?php

namespace Masterkey\Repository\Events;

use Illuminate\Database\Eloquent\Model;
use Masterkey\Repository\Contracts\RepositoryInterface;

/**
 * BaseRepositoryEvent
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   13/03/2018
 * @package Masterkey\Repository\Events
 */
class BaseRepositoryEvent
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var
     */
    protected $action;

    /**
     * @param   RepositoryInterface  $repository
     * @param   Model  $model
     */
    public function __construct(RepositoryInterface $repository, Model $model)
    {
        $this->repository   = $repository;
        $this->model        = $model;
    }

    /**
     * @return  Model
     */
    public function getModel() : Model
    {
        return $this->model;
    }

    /**
     * @return  RepositoryInterface
     */
    public function getRepository() : RepositoryInterface
    {
        return $this->repository;
    }

    /**
     * @return  string
     */
    public function getAction() : string
    {
        return $this->action;
    }
}