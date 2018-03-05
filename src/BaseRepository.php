<?php

namespace Masterkey\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Masterkey\Repository\Contracts\CriteriaContract;
use Masterkey\Repository\Contracts\RepositoryContract;
use ModelNotSavedException;
use ModelNotDeletedException;
use Prettus\Validator\Contracts\ValidatorInterface;
use RepositoryException;

/**
 * BaseRepository
 *
 * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version  3.0.0
 * @since    02/09/2017
 * @package  Masterkey\Repository
 */
abstract class BaseRepository implements CriteriaContract, RepositoryContract
{
    use Traits\ClassBuilder;

    /**
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $criteria;

    /**
     * @var boolean
     */
    protected $skipCriteria = false;

    /**
     * @var boolean
     */
    protected $preventCriteriaOverwriting = true;

    /**
     * @var null|ValidatorInterface
     */
    protected $validator = null;

    /**
     * @var array
     */
    protected $fieldsSearchable = [];

    /**
     * @param   Container  $container
     * @throws  RepositoryException
     */
    public function __construct(Container $container)
    {
        $this->app      = $container;
        $this->criteria = new Collection();

        $this->resetScope();

        $this->makeModel($this->model());
        $this->makeValidator($this->validator());

        $this->boot();
    }

    /**
     * @return  mixed
     */
    public abstract function model();

    /**
     * @return  void
     */
    public function boot() {}

    /**
     * @return  null|string
     */
    public function validator()
    {
        return null;
    }

    /**
     * @param   array  $columns
     * @return  \Illuminate\Support\Collection
     */
    public function all(array $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->get($columns);
    }

    /**
     * @param   array  $relations
     * @return  $this
     */
    public function with(array $relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * @param   string  $value
     * @param   string|null $key
     * @return  mixed
     */
    public function pluck(string $value, $key = null)
    {
        $this->applyCriteria();

        return $this->model->pluck($value, $key)->toArray();
    }

    /**
     * @param   integer $perPage
     * @param   array $columns
     * @param   string  $method
     * @return  mixed
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], $method = 'paginate')
    {
        $this->applyCriteria();

        $results = $this->model->{$method}($perPage, $columns);
        $results->appends($this->app->make('request')->query());

        return $results;
    }

    /**
     * @param   int  $perPage
     * @param   array  $columns
     * @return  mixed
     */
    public function simplePaginate(int $perPage = 15, array $columns = ['*'])
    {
        return $this->paginate($perPage, $columns, 'simplePaginate');
    }

    /**
     * @param   array  $data
     * @return  mixed|bool
     * @throws  ModelNotSavedException
     * @throws  \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $data)
    {
        if ( ! is_null($this->validator) ) {
            $this->validator->with($data)->passesOrFail(ValidatorInterface::RULE_CREATE);
        }

        $model = $this->model->create($data);

        if ( $model ) {
            return $model;
        }

        throw new ModelNotSavedException;
    }

    /***
     * @param   array  $data
     * @return  Model
     * @throws  ModelNotSavedException
     * @throws  \Prettus\Validator\Exceptions\ValidatorException
     */
    public function firstOrCreate(array $data)
    {
        if ( ! is_null($this->validator) ) {
            $this->validator->with($data)->passesOrFail(ValidatorInterface::RULE_CREATE);
        }

        $model = $this->model->firstOrCreate($data);

        if( $model ) {
            return $model;
        }

        throw new ModelNotSavedException;
    }

    /**
     * @param   array  $data
     * @return  Model
     * @throws  \Prettus\Validator\Exceptions\ValidatorException
     */
    public function firstOrNew(array $data)
    {
        if ( ! is_null($this->validator) ) {
            $this->validator->with($data)->passesOrFail(ValidatorInterface::RULE_CREATE);
        }

        return $this->model->firstOrNew($data);
    }

    /**
     * @param   array  $data
     * @return  Model
     * @throws  ModelNotSavedException
     * @throws  \Prettus\Validator\Exceptions\ValidatorException
     */
    public function save(array $data)
    {
        if ( ! is_null($this->validator) ) {
            $this->validator->with($data)->passesOrFail(ValidatorInterface::RULE_CREATE);
        }

        foreach ( $data as $k => $v ) {
            $this->model->$k = $v;
        }

        if ( $this->model->save() ) {
            return $this->model;
        }

        throw new ModelNotSavedException;
    }

    /**
     * Perform bulk insertions on the model
     *
     * @param   array  $data
     * @return  mixed
     * @throws  ModelNotSavedException
     */
    public function insert(array $data) : bool
    {
        if ( $this->model->insert($data) ) {
            return true;
        }

        throw new ModelNotSavedException('Não foi possível salvar alguns registros. Tente novamente');
    }

    /**
     * @param   int $id
     * @param   array $data
     * @return  mixed
     * @throws  ModelNotSavedException
     * @throws  \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(int $id, array $data)
    {
        if ( ! is_null($this->validator) ) {
            $this->validator->with($data)->passesOrFail(ValidatorInterface::RULE_UPDATE);
        }

        $model = $this->find($id);

        if ( $model->update($data) ) {
            return $model;
        }

        throw new ModelNotSavedException('Não foi possível atualizar o registro. Tente novamente');
    }

    /**
     * @param   array  $data
     * @return  bool
     */
    public function massUpdate(array $data)
    {
        $this->applyCriteria();

        return $this->model->update($data);
    }

    /**
     * @param   int  $id
     * @return  bool
     * @throws  ModelNotDeletedException
     */
    public function delete(int $id) : bool
    {
        if ( $this->model->destroy($id) ) {
            return true;
        }

        throw new ModelNotDeletedException;
    }

    /**
     * Make a massive deleting on DB
     *
     * @param   array  $records
     * @return  bool
     * @throws  ModelNotDeletedException
     */
    public function destroy(array $records) : bool
    {
        $this->applyCriteria();

        if ( $this->model->destroy($records) ) {
            return true;
        }

        throw new ModelNotDeletedException('Os registros não foram apagados. Tente novamente');
    }

    /**
     * @param   int   $id
     * @param   array  $columns
     * @return  mixed
     */
    public function find(int $id, $columns = array('*'))
    {
        $this->applyCriteria();

        return $this->model->findOrFail($id, $columns);
    }

    /**
     * @param   array  $columns
     * @return  mixed
     */
    public function first(array $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->first($columns);
    }

    /**
     * @param   array  $columns
     * @return  mixed
     */
    public function last(array $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model
                    ->orderBy($this->getKeyName(), 'desc')
                    ->first();
    }

    /**
     * Search for a model by a column
     *
     * @param   string  $attribute
     * @param   mixed  $value
     * @param   array  $columns
     * @return  mixed
     */
    public function findBy($attribute, $value, array $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    /**
     * Search for records by a column
     *
     * @param   string  $attribute
     * @param   mixed  $value
     * @param   array  $columns
     * @return  mixed
     */
    public function findAllBy($attribute, $value, array $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->where($attribute, '=', $value)->get($columns);
    }

    /**
     * @return   $this
     */
    public function resetScope()
    {
        $this->skipCriteria(false);

        return $this;
    }

    /**
     * @param   boolean  $status
     * @return  $this
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;

        return $this;
    }

    /**
     * @return   \Illuminate\Support\Collection
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param   Criteria  $criteria
     * @return  Collection
     */
    public function getByCriteria(Criteria $criteria) : Collection
    {
        $this->pushCriteria($criteria);

        return $this->all();
    }

    /**
     * @param   Criteria  $criteria
     * @return  $this
     */
    public function pushCriteria(Criteria $criteria)
    {
        if ( $this->preventCriteriaOverwriting ) {
            // Find existing criteria
            $key = $this->criteria->search(function ($item) use ($criteria) {
                return ( is_object($item) && (get_class($item) == get_class($criteria)) );
            });

            // Remove old criteria
            if ( is_int($key) ) {
                $this->criteria->offsetUnset($key);
            }
        }

        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * @return   $this
     */
    public function applyCriteria()
    {
        if ( $this->skipCriteria === true ) {
            return $this;
        }

        foreach ( $this->getCriteria() as $criteria ) {
            if ( $criteria instanceof Criteria ) {
                $this->model = $criteria->apply($this->model, $this);
            }
        }

        return $this;
    }

    /**
     * @return  integer
     */
    public function count() : int
    {
        $this->applyCriteria();

        return $this->model->count();
    }

    /**
     * @param   string  $column
     * @return  int|float
     */
    public function max(string $column)
    {
        $this->applyCriteria();

        return $this->model->max($column);
    }

    /**
     * @param   string  $column
     * @return  int|float
     */
    public function min(string $column)
    {
        $this->applyCriteria();

        return $this->model->min($column);
    }

    /**
     * @param   string  $column
     * @return  int|float
     */
    public function avg(string $column)
    {
        $this->applyCriteria();

        return $this->model->avg($column);
    }

    /**
     * @param   string  $column
     * @return  int|float
     */
    public function sum(string $column)
    {
        $this->applyCriteria();

        return $this->model->sum($column);
    }

    /**
     * @return  \Illuminate\Database\Eloquent\Builder
     */
    public function getBuilder() : Builder
    {
        return $this->model->query();
    }

    /**
     * @return string
     */
    private function getKeyName() : string
    {
        if ( $this->model instanceof Builder ) {
            $model = $this->model->getModel();

            return $model->getKeyName();
        }

        return $this->model->getKeyName();
    }

    /**
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldsSearchable;
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
