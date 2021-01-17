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
    protected Model $model;

    protected RepositoryInterface $repository;

    protected string $action;

    public function __construct(RepositoryInterface $repository, Model $model)
    {
        $this->repository   = $repository;
        $this->model        = $model;
    }

    public function getModel() : Model
    {
        return $this->model;
    }

    public function getRepository() : RepositoryInterface
    {
        return $this->repository;
    }

    public function getAction() : string
    {
        return $this->action;
    }
}