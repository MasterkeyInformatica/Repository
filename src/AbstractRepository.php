<?php

namespace Masterkey\Repository;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Masterkey\Repository\Contracts\{
    CountableInterface,
    CreatableInterface,
    CriteriaInterface,
    RepositoryInterface,
    SearchableInterface,
    SortableInterface};
use Masterkey\Repository\Events\{EntityCreated, EntityDeleted, EntityUpdated};
use RepositoryException;

/**
 * BaseRepository
 *
 * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version  7.0.0
 * @package  Masterkey\Repository
 */
abstract class AbstractRepository implements
    CountableInterface,
    CreatableInterface,
    CriteriaInterface,
    RepositoryInterface,
    SearchableInterface,
    SortableInterface
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * @var Model|Builder
     */
    protected $model;

    /**
     * @var Collection
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

        $this->bootTraits();
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
     * @param string $model
     * @throws RepositoryException
     */
    public function makeModel($model)
    {
        unset($this->model);

        $model = $this->app->make($model);

        if ( ! $model instanceof Model) {
            throw new RepositoryException("Class {$model} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        $this->model = $model;
    }

    /**
     * @throws RepositoryException
     */
    public function resetModel()
    {
        $this->makeModel($this->model());
    }

    public function bootTraits()
    {
        $class = $this;

        foreach ( class_uses_recursive($class) as $trait ) {
            if ( method_exists($class, $method = 'boot' . class_basename($trait)) ) {
                $this->{$method}();
            }
        }
    }

    /**
     * @return Builder
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
     * @return array|mixed
     */
    public function getFieldsSearchable()
    {
        return $this->fieldsSearchable;
    }

    /**
     * @param string $column
     * @return int
     * @throws RepositoryException
     */
    public function count(string $column = '*') : int
    {
        $this->applyCriteria();

        return $this->model->count($column);
    }

    /**
     * @param string $column
     * @return mixed
     * @throws RepositoryException
     */
    public function max(string $column)
    {
        $this->applyCriteria();

        return $this->model->max($column);
    }

    /**
     * @param string $column
     * @return mixed
     * @throws RepositoryException
     */
    public function min(string $column)
    {
        $this->applyCriteria();

        return $this->model->min($column);
    }

    /**
     * @param string $column
     * @return float|int
     * @throws RepositoryException
     */
    public function avg(string $column)
    {
        $this->applyCriteria();

        return $this->model->avg($column);
    }

    /**
     * @param string $column
     * @return int|float
     * @throws RepositoryException
     */
    public function sum(string $column)
    {
        $this->applyCriteria();

        return $this->model->sum($column);
    }

    /**
     * @param array $data
     * @return Model
     * @throws RepositoryException
     */
    public function create(array $data) : Model
    {
        $model = $this->model->create($data);

        if ( $model ) {
            $this->app['events']->dispatch(new EntityCreated($this, $model));

            return $model;
        }

        throw new RepositoryException('Nāo foi possível salvar os dados. Tente novamente');
    }

    /**
     * @param array $data
     * @return Model
     * @throws RepositoryException
     */
    public function firstOrCreate(array $data) : Model
    {
        $model = $this->model->firstOrCreate($data);

        if( $model ) {
            $this->app['events']->dispatch(new EntityCreated($this, $model));

            return $model;
        }

        throw new RepositoryException('Não foi possível salvar os dados. Tente novamente');
    }

    /**
     * @param array $data
     * @return Model
     */
    public function firstOrNew(array $data) : Model
    {
        return $this->model->firstOrNew($data);
    }

    /**
     * @param array $data
     * @return Model
     * @throws RepositoryException
     */
    public function save(array $data) : Model
    {
        $model = $this->model;

        $model->fill($data);

        if ( $model->save() ) {
            $this->app['events']->dispatch(new EntityCreated($this, $model));

            return $model;
        }

        throw new RepositoryException('Nao foi possível salvar os dados, Tente Novamente');
    }

    /**
     * @param array $data
     * @return bool
     * @todo Retornar o número de rows affected
     */
    public function insert(array $data) : bool
    {
        return DB::transaction(function () use ($data) {
            if ( $this->model->insert($data) ) {
                $this->app['events']->dispatch(new EntityCreated($this, $this->model->getModel()));

                return true;
            }

            throw new RepositoryException('Não foi possível salvar alguns registros. Tente novamente');
        });
    }

    /**
     * @param int   $id
     * @param array $data
     * @return Model
     * @throws RepositoryException
     */
    public function update(int $id, array $data)
    {
        $model      = $this->find($id);
        $original   = clone $model;

        if ( $model->update($data) ) {
            $this->app['events']->dispatch(new EntityUpdated($this, $original));

            return $model;
        }

        throw new RepositoryException('Não foi possível atualizar o registro. Tente novamente');
    }

    /**
     * @param array $data
     * @return bool|int
     * @throws RepositoryException
     */
    public function massUpdate(array $data)
    {
        $this->applyCriteria();

        $updated = $this->model->update($data);

        if ( $updated ) {
            $this->app['events']->dispatch(new EntityUpdated($this, $this->model->getModel()));
        }

        return $updated;
    }

    /**
     * @param int $id
     * @return bool
     * @throws RepositoryException
     * @throws \Exception
     */
    public function delete(int $id) : bool
    {
        $model      = $this->find($id);
        $original   = clone $model;

        if ( $model->delete() ) {
            $this->app['events']->dispatch(new EntityDeleted($this, $original));

            return true;
        }

        throw new RepositoryException('Não foi possível apagar o registro. Tente Novamente');
    }

    /**
     * @param array $records
     * @return bool
     * @throws RepositoryException
     */
    public function destroy(array $records) : bool
    {
        $this->applyCriteria();

        if ( $this->model->destroy($records) ) {
            $this->app['events']->dispatch(new EntityDeleted($this, $this->model->getModel()));

            return true;
        }

        throw new RepositoryException('Os registros não foram apagados. Tente novamente');
    }

    /**
     * @param int    $id
     * @param string $relation
     * @param string $attributes
     * @param bool   $detach
     * @return mixed
     */
    public function sync($id, $relation, $attributes, $detach = true)
    {
        return $this->find($id)->{$relation}()->sync($attributes, $detach);
    }

    /**
     * @param array $columns
     * @return Collection
     * @throws RepositoryException
     */
    public function all(array $columns = ['*']) : Collection
    {
        $this->applyCriteria();

        return $this->model->get($columns);
    }

    /**
     * @param array $relations
     * @return $this
     */
    public function with(array $relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * @param string $value
     * @param null   $key
     * @return array
     * @throws RepositoryException
     */
    public function pluck(string $value, $key = null) : array
    {
        $this->applyCriteria();

        return $this->model->pluck($value, $key)->toArray();
    }

    /**
     * @param int    $perPage
     * @param array  $columns
     * @param string $method
     * @return Paginator
     * @throws RepositoryException
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], $method = 'paginate')
    {
        $this->applyCriteria();

        $results = $this->model->{$method}($perPage, $columns);

        $results->appends($this->app->make('request')->query());

        return $results;
    }

    /**
     * @param int   $perPage
     * @param array $columns
     * @return LengthAwarePaginator|Paginator
     * @throws RepositoryException
     */
    public function simplePaginate(int $perPage = 15, array $columns = ['*'])
    {
        return $this->paginate($perPage, $columns, 'simplePaginate');
    }

    /**
     * @param int   $id
     * @param array $columns
     * @return Model
     * @throws RepositoryException
     */
    public function find(int $id, $columns = array('*')) : Model
    {
        $this->applyCriteria();

        return $this->model->findOrFail($id, $columns);
    }

    /**
     * @param array $columns
     * @return Model|null
     * @throws RepositoryException
     */
    public function first(array $columns = ['*']) : ?Model
    {
        $this->applyCriteria();

        return $this->model->first($columns);
    }

    /**
     * @param array $columns
     * @return Model|null
     * @throws RepositoryException
     */
    public function last(array $columns = ['*']) : ?Model
    {
        $this->applyCriteria();

        return $this->orderBy($this->getKeyName(), 'desc')->first($columns);
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     * @param array  $columns
     * @return Model|null
     * @throws RepositoryException
     */
    public function findBy($attribute, $value, array $columns = ['*']) : ?Model
    {
        $this->applyCriteria();

        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     * @param array  $columns
     * @return Collection
     * @throws RepositoryException
     */
    public function findAllBy($attribute, $value, array $columns = ['*']) : Collection
    {
        $this->applyCriteria();

        return $this->model->where($attribute, '=', $value)->get($columns);
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->model = $this->model->limit($limit);

        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset)
    {
        $this->model = $this->model->offset($offset);

        return $this;
    }

    /**
     * @param string $column
     * @param string $operator
     * @param mixed  $value
     * @return $this
     */
    public function having(string $column, string $operator, $value)
    {
        $this->groupBy($column);

        $this->model = $this->model->having($column, $operator, $value);

        return $this;
    }

    /**
     * @param string $column
     * @param string $order
     * @return $this
     */
    public function orderBy(string $column, $order = 'asc')
    {
        $this->model = $this->model->orderBy($column, $order);

        return $this;
    }

    /**
     * @param mixed ...$columns
     * @return $this
     */
    public function groupBy(...$columns)
    {
        $this->model = $this->model->groupBy($columns);

        return $this;
    }

    /**
     * @return $this
     */
    public function resetScope()
    {
        $this->skipCriteria(false);

        return $this;
    }

    /**
     * @param bool $status
     * @return $this|CriteriaInterface
     */
    public function skipCriteria(bool $status = true)
    {
        $this->skipCriteria = $status;

        return $this;
    }

    /**
     * @return Collection|mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param AbstractCriteria $criteria
     * @return Collection
     * @throws RepositoryException
     */
    public function getByCriteria(AbstractCriteria $criteria) : Collection
    {
        $this->pushCriteria($criteria);

        return $this->all();
    }

    /**
     * @param AbstractCriteria $criteria
     * @return $this|CriteriaInterface
     */
    public function pushCriteria(AbstractCriteria $criteria)
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
     * @return $this
     * @throws RepositoryException
     */
    public function applyCriteria()
    {
        if ( $this->skipCriteria === true ) {
            return $this;
        }

        $criterias = $this->getCriteria();

        if ( $criterias->isNotEmpty() ) {

            $this->resetModel();

            foreach ( $criterias as $criteria ) {
                if ( $criteria instanceof AbstractCriteria ) {
                    $this->model = $criteria->apply($this->model, $this);
                }
            }

            $this->criteria = collect([]);
        }

        return $this;
    }
}
