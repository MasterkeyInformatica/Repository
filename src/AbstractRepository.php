<?php

namespace Masterkey\Repository;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Query\Expression;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\{Collection, Str};
use Masterkey\Repository\Contracts\{CountableInterface,
    CreatableInterface,
    CriteriaInterface,
    RepositoryInterface,
    SearchableInterface,
    SortableInterface
};
use Masterkey\Repository\Events\{EntityCreated, EntityDeleted, EntityUpdated};
use PDO;
use RepositoryException;
use RuntimeException;
use Throwable;

/**
 * AbstractRepository
 *
 * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version  7.1.0
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
     * @var AbstractPresenter
     */
    protected $presenter;

    protected $skipPresenter = false;

    /**
     * @param Container $container
     * @throws RepositoryException
     */
    public function __construct(Container $container)
    {
        $this->app = $container;
        $this->criteria = new Collection();

        $this->resetScope();

        $this->makeModel($this->model());
        $this->makePresenter($this->presenter());

        $this->bootTraits();
        $this->boot();
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
     * @param string $model
     * @throws RepositoryException
     */
    public function makeModel($model)
    {
        unset($this->model);

        $model = $this->app->make($model);

        if ( ! $model instanceof Model ) {
            throw new RepositoryException("Class {$model} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        $this->model = $model;
    }

    public abstract function model();

    /**
     * @param string|null $presenter
     * @throws RuntimeException
     */
    public function makePresenter(string $presenter = null)
    {
        unset($this->presenter);

        if ( ! is_null($presenter) ) {
            $presenter = $this->app->make($presenter);

            if ( ! $presenter instanceof AbstractPresenter ) {
                throw new RuntimeException("Class {$presenter} must be an instance of Masterkey\\Repository\\AbstractPresenter");
            }

            $this->presenter = $presenter;
        }
    }

    /**
     * @return string|null
     */
    public function presenter()
    {
        return null;
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
     * @return void
     */
    public function boot()
    {
    }

    /**
     * @return array|mixed
     */
    public function getFieldsSearchable()
    {
        return $this->fieldsSearchable;
    }

    public function count(string $column = '*') : int
    {
        $this->applyCriteria();

        return $this->model->count($column);
    }

    /**
     * @return $this|CriteriaInterface
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

    public function getCriteria() : Collection
    {
        return $this->criteria;
    }

    public function resetModel() : void
    {
        $this->makeModel($this->model());
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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
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
    public function firstOrCreate(array $data) : Model
    {
        $model = $this->model->firstOrCreate($data);

        if ( $model ) {
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
        if ( $this->model instanceof Builder ) {
            $this->resetModel();
        }

        $model = $this->model;

        $model->fill($data);

        if ( $model->save() ) {
            $this->app['events']->dispatch(new EntityCreated($this, $model));

            return $model;
        }

        throw new RepositoryException('Nao foi possível salvar os dados, Tente Novamente');
    }

    /**
     * É extremamente recomendado o uso de transaction
     * neste método
     *
     * @param array $data
     * @return bool
     * @throws Throwable
     * @todo Retornar o número de rows affected
     */
    public function insert(array $data) : bool
    {
        $response = true;

        if ( $this->driver() == 'firebird' ) {
            foreach ( $data as $row ) {
                $this->create($row);
            }
        } else {
            $response = $this->model->insert($data);
        }

        if ( $response ) {
            $this->app['events']->dispatch(new EntityCreated($this, $this->model->getModel()));

            return true;
        }

        throw new RepositoryException('Não foi possível salvar os registros. Tente novamente');
    }

    /**
     * @param Closure $closure
     * @param int     $attempts
     * @return mixed
     * @throws Throwable
     */
    public function transaction(Closure $closure, int $attempts = 1)
    {
        if ( $this->driver() == 'firebird' ) {
            $this->disableAutoCommit();

            $response = $this->connection()->transaction($closure, $attempts);

            $this->enableAutoCommit();

            return $response;
        }

        return $this->connection()->transaction($closure, $attempts);
    }

    /**
     * @return string
     */
    protected function driver()
    {
        return $this->connection()->getDriverName();
    }

    /**
     * @return Connection
     */
    public function connection() : Connection
    {
        return $this->model->getConnection();
    }

    /**
     * @return bool
     */
    public function disableAutoCommit() : bool
    {
        return $this->getPDO()->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
    }

    /**
     * @return PDO
     */
    public function getPDO() : PDO
    {
        return $this->connection()->getPdo();
    }

    /**
     * @return bool
     */
    public function enableAutoCommit() : bool
    {
        return $this->getPDO()->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
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
     * @param array    $data
     * @param int|null $id
     * @return Model|int|null
     * @throws RepositoryException
     */
    public function update(array $data, int $id = null)
    {
        $this->resetModel();

        if ( is_null($id) && $this->criteria->isEmpty() ) {
            throw new RepositoryException('Para atualização de dados, é necessário identificar os registros a serem atualizados');
        }

        if ( $this->criteria->isEmpty() ) {
            $model = $this->find($id);

            if ( $model->update($data) ) {
                $this->app['events']->dispatch(new EntityUpdated($this, $this->model));

                return $model;
            }
        } else {
            $this->applyCriteria();

            $builder = $this->getBuilder();

            if ( $update = $builder->update($data) ) {
                $this->resetModel();
                $this->app['events']->dispatch(new EntityUpdated($this, $this->model));

                return $update;
            }
        }

        throw new RepositoryException('Não foi possível atualizar o registro. Tente novamente');
    }

    /**
     * @param int   $id
     * @param array $columns
     * @return Model|null
     * @throws RepositoryException
     */
    public function find(int $id, $columns = array('*')) : ?Model
    {
        $this->applyCriteria();

        return $this->model->find($id, $columns);
    }

    /**
     * @param array $data
     * @return int
     * @throws RepositoryException
     * @throws Throwable
     */
    public function massUpdate(array $data)
    {
        $this->applyCriteria();

        $affectedRows = $this->transaction(function () use ($data) {
            return $this->getBuilder()->update($data);
        });

        if ( $affectedRows > 0 ) {
            $this->app['events']->dispatch(new EntityUpdated($this, $this->model->getModel()));
        }

        $this->resetModel();

        return $affectedRows;
    }

    /**
     * @return Builder
     */
    public function getBuilder() : Builder
    {
        return $this->model->newQuery();
    }

    /**
     * @param int $id
     * @return bool
     * @throws RepositoryException
     * @throws \Exception
     */
    public function delete(int $id) : bool
    {
        $this->resetModel();

        $model = $this->find($id);
        $original = clone $model;

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
     * @throws RepositoryException
     */
    public function sync($id, $relation, $attributes, $detach = true)
    {
        $this->resetModel();

        return $this->find($id)->{$relation}()->sync($attributes, $detach);
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
     * @param string     $value
     * @param mixed|null $key
     * @return array
     * @throws RepositoryException
     */
    public function pluck(string $value, $key = null) : array
    {
        $this->applyCriteria();

        return $this->model->pluck($value, $key)->toArray();
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
     * @param int   $id
     * @param array $columns
     * @return Model
     * @throws RepositoryException
     */
    public function findOrFail(int $id, $columns = ['*']) : Model
    {
        $this->applyCriteria();

        return $this->model->findOrFail($id, $columns);
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

    public function groupBy(...$columns)
    {
        $this->model = $this->model->groupBy($columns);

        return $this;
    }

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
                return ( is_object($item) && ( get_class($item) == get_class($criteria) ) );
            });

            // Remove old criteria
            if ( is_int($key) ) {
                $this->criteria->offsetUnset($key);
            }
        }

        $this->criteria->push($criteria);

        return $this;
    }

    public function all(array $columns = ['*']) : Collection
    {
        $this->applyCriteria();

        return $this->model->get($columns);
    }

    public function enableQueryLog() : void
    {
        $this->connection()->enableQueryLog();
    }

    public function disableQueryLog() : void
    {
        $this->connection()->disableQueryLog();
    }

    public function getLastQuery() : ?string
    {
        $logs = $this->getQueryLog();
        $last = array_pop($logs);

        if ( is_null($last) ) {
            return null;
        }

        return Str::replaceArray('?', $last['bindings'], $last['query']);
    }

    public function getQueryLog() : array
    {
        return $this->connection()->getQueryLog();
    }

    public function exists() : bool
    {
        $this->applyCriteria();

        return $this->model->exists();
    }

    public function doesntExists() : bool
    {
        $this->applyCriteria();

        return $this->model->doesntExist();
    }

    /**
     * @param string    $column
     * @param int|float $amount
     * @param array     $extra
     * @return int
     * @throws RepositoryException
     */
    public function increment(string $column, $amount = 1, array $extra = [])
    {
        $this->applyCriteria();

        return $this->model->increment($column, $amount, $extra);
    }

    /**
     * @param string    $column
     * @param int|float $amount
     * @param array     $extra
     * @return int
     * @throws RepositoryException
     */
    public function decrement(string $column, $amount = 1, array $extra = [])
    {
        $this->applyCriteria();

        return $this->model->decrement($column, $amount, $extra);
    }

    public function select(string $query, array $bindings = [], bool $useReadPdo = true) : Collection
    {
        $this->resetModel();

        return $this->model->newCollection(
            $this->connection()->select($query, $bindings, $useReadPdo)
        );
    }

    public function selectOne(string $query, array $bindings = [], bool $useReadPdo = true) : ?Model
    {
        $this->resetModel();

        if ( $result = $this->connection()->selectOne($query, $bindings, $useReadPdo) ) {
            return $this->model->newInstance(
                json_decode(json_encode($result), true), true
            );
        }

        return null;
    }

    public function statement(string $query, array $bindings = []) : bool
    {
        return $this->connection()->statement($query, $bindings);
    }

    public function raw(string $value) : Expression
    {
        return $this->connection()->raw($value);
    }

    public function chunk(int $count, callable $callback)
    {
        $this->applyCriteria();

        return $this->model->chunk($count, $callback);
    }

    public function chunkById(int $count, callable $callback, string $column = null, string $alias = null)
    {
        $this->applyCriteria();

        return $this->model->chunkById($count, $callback, $column, $alias);
    }

    public function skipPresenter() : AbstractRepository
    {
        $this->skipPresenter = true;

        return $this;
    }

    public function presenterSkipped() : bool
    {
        return $this->skipPresenter;
    }

    public function enablePresenter() : AbstractRepository
    {
        $this->skipPresenter = false;

        return $this;
    }

    /**
     * @return \Generator
     */
    public function cursor()
    {
        $this->applyCriteria();

        return $this->model->cursor();
    }
}
