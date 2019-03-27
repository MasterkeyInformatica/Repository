<?php

namespace Masterkey\Repository\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Masterkey\Repository\Events\EntityCreated;
use Masterkey\Repository\Events\EntityDeleted;
use Masterkey\Repository\Events\EntityUpdated;
use RepositoryException;

/**
 * Trait NeedsBeCreatable
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.0
 * @since   27/03/2019
 * @package Masterkey\Repository\Traits
 */
trait NeedsBeCreatable
{
    /**
     * @param   array  $data
     * @return  mixed
     * @throws  RepositoryException
     */
    public function create(array $data)
    {
        $model = $this->model->create($data);

        if ( $model ) {
            Event::fire(new EntityCreated($this, $model));

            return $model;
        }

        throw new RepositoryException('Nāo foi possível salvar os dados. Tente novamente');
    }

    /**
     * @param   array  $data
     * @return  Model
     * @throws  RepositoryException
     */
    public function firstOrCreate(array $data)
    {
        $model = $this->model->firstOrCreate($data);

        if( $model ) {
            Event::fire(new EntityCreated($this, $model));

            return $model;
        }

        throw new RepositoryException('Não foi possível salvar os dados. Tente novamente');
    }

    /**
     * @param   array $data
     * @return  Model
     */
    public function firstOrNew(array $data)
    {
        return $this->model->firstOrNew($data);
    }

    /**
     * @param   array  $data
     * @return  Model
     * @throws  RepositoryException
     */
    public function save(array $data)
    {
        foreach ( $data as $k => $v ) {
            $this->model->$k = $v;
        }

        if ( $this->model->save() ) {
            Event::fire(new EntityCreated($this, $this->model->getModel()));

            return $this->model;
        }

        throw new RepositoryException('Nao foi possível salvar os dados, Tente Novamente');
    }

    /**
     * @param   array  $data
     * @return  bool
     */
    public function insert(array $data) : bool
    {
        return DB::transaction(function () use ($data) {
            if ( $this->model->insert($data) ) {
                Event::fire(new EntityCreated($this, $this->model->getModel()));

                return true;
            }

            throw new RepositoryException('Não foi possível salvar alguns registros. Tente novamente');
        });
    }

    /**
     * @param   int  $id
     * @param   array  $data
     * @return  Model
     * @throws  RepositoryException
     */
    public function update(int $id, array $data)
    {
        $model      = $this->find($id);
        $original   = clone $model;

        if ( $model->update($data) ) {
            Event::fire(new EntityUpdated($this, $original));

            return $model;
        }

        throw new RepositoryException('Não foi possível atualizar o registro. Tente novamente');
    }

    /**
     * @param   array  $data
     * @return  bool
     */
    public function massUpdate(array $data)
    {
        $this->applyCriteria();

        $updated = $this->model->update($data);

        if ( $updated ) {
            Event::fire(new EntityUpdated($this, $this->model->getModel()));
        }

        return $updated;
    }

    /**
     * @param   int  $id
     * @return  bool
     * @throws  RepositoryException
     */
    public function delete(int $id) : bool
    {
        $model      = $this->find($id);
        $original   = clone $model;

        if ( $model->delete() ) {
            Event::fire(new EntityDeleted($this, $original));

            return true;
        }

        throw new RepositoryException('Não foi possível apagar o registro. Tente Novamente');
    }

    /**
     * @param   array  $records
     * @return  bool
     * @throws  RepositoryException
     */
    public function destroy(array $records) : bool
    {
        $this->applyCriteria();

        if ( $this->model->destroy($records) ) {
            Event::fire(new EntityDeleted($this, $this->model->getModel()));

            return true;
        }

        throw new RepositoryException('Os registros não foram apagados. Tente novamente');
    }

    /**
     * @param   int  $id
     * @param   string  $relation
     * @param   array  $attributes
     * @param   bool  $detach
     * @return  mixed
     */
    public function sync($id, $relation, $attributes, $detach = true)
    {
        return $this->find($id)->{$relation}()->sync($attributes, $detach);
    }
}