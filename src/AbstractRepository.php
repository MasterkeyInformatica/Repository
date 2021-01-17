<?php

namespace Masterkey\Repository;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Query\Expression;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\{Collection, LazyCollection, Str};
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
    protected Container $app;

    /** @var Model|Builder */
    protected $model;

    protected Collection $criteria;

    protected bool $skipCriteria = false;

    protected bool $preventCriteriaOverwriting = true;

    protected array $fieldsSearchable = [];

    public function __construct(Container $container)
    {
        $this->app      = $container;
        $this->criteria = new Collection();

        $this->resetScope();

        $this->makeModel($this->model());

        $this->bootTraits();
        $this->boot();
    }

    public function resetScope(): AbstractRepository
    {
        $this->skipCriteria(false);

        return $this;
    }

    public function skipCriteria(bool $status = true): AbstractRepository
    {
        $this->skipCriteria = $status;

        return $this;
    }

    public function makeModel(string $model)
    {
        unset($this->model);

        $model = $this->app->make($model);

        if (!$model instanceof Model) {
            throw new RepositoryException("Class {$model} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        $this->model = $model;
    }

    public abstract function model(): string;

    public function bootTraits(): void
    {
        $class = $this;

        foreach (class_uses_recursive($class) as $trait) {
            if (method_exists($class, $method = 'boot' . class_basename($trait))) {
                $this->{$method}();
            }
        }
    }

    public function boot(): void
    {
    }

    public function getFieldsSearchable(): array
    {
        return $this->fieldsSearchable;
    }

    public function count(string $column = '*'): int
    {
        $this->applyCriteria();

        return $this->model->count($column);
    }

    public function applyCriteria(): AbstractRepository
    {
        if ($this->skipCriteria === true) {
            return $this;
        }

        $criterias = $this->getCriteria();

        if ($criterias->isNotEmpty()) {

            $this->resetModel();

            foreach ($criterias as $criteria) {
                if ($criteria instanceof AbstractCriteria) {
                    $this->model = $criteria->apply($this->model, $this);
                }
            }

            $this->criteria = collect([]);
        }

        return $this;
    }

    public function getCriteria(): Collection
    {
        return $this->criteria;
    }

    public function resetModel(): void
    {
        $this->makeModel($this->model());
    }

    public function max(string $column)
    {
        $this->applyCriteria();

        return $this->model->max($column);
    }

    public function min(string $column)
    {
        $this->applyCriteria();

        return $this->model->min($column);
    }

    public function avg(string $column)
    {
        $this->applyCriteria();

        return $this->model->avg($column);
    }

    public function sum(string $column)
    {
        $this->applyCriteria();

        return $this->model->sum($column);
    }

    public function firstOrCreate(array $data): Model
    {
        $model = $this->model->firstOrCreate($data);

        if ($model) {
            $this->app['events']->dispatch(new EntityCreated($this, $model));

            return $model;
        }

        throw new RepositoryException('Não foi possível salvar os dados. Tente novamente');
    }

    public function firstOrNew(array $data): Model
    {
        return $this->model->firstOrNew($data);
    }

    public function save(array $data): Model
    {
        if ($this->model instanceof Builder) {
            $this->resetModel();
        }

        $model = $this->model;

        $model->fill($data);

        if ($model->save()) {
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
    public function insert(array $data): bool
    {
        $response = true;

        if ($this->driver() == 'firebird') {
            foreach ($data as $row) {
                $this->create($row);
            }
        } else {
            $response = $this->model->insert($data);
        }

        if ($response) {
            $this->app['events']->dispatch(new EntityCreated($this, $this->model->getModel()));

            return true;
        }

        throw new RepositoryException('Não foi possível salvar os registros. Tente novamente');
    }

    protected function driver(): string
    {
        return $this->connection()->getDriverName();
    }

    public function connection(): Connection
    {
        return $this->model->getConnection();
    }

    public function create(array $data): Model
    {
        $model = $this->model->create($data);

        if ($model) {
            $this->app['events']->dispatch(new EntityCreated($this, $model));

            return $model;
        }

        throw new RepositoryException('Nāo foi possível salvar os dados. Tente novamente');
    }

    public function transaction(Closure $closure, int $attempts = 1)
    {
        if ($this->driver() == 'firebird') {
            $this->disableAutoCommit();

            $response = $this->connection()->transaction($closure, $attempts);

            $this->enableAutoCommit();

            return $response;
        }

        return $this->connection()->transaction($closure, $attempts);
    }

    public function disableAutoCommit(): bool
    {
        return $this->getPDO()->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
    }

    public function getPDO(): PDO
    {
        return $this->connection()->getPdo();
    }

    public function enableAutoCommit(): bool
    {
        return $this->getPDO()->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
    }

    public function update(array $data, int $id = null)
    {
        $this->resetModel();

        if (is_null($id) && $this->criteria->isEmpty()) {
            throw new RepositoryException('Para atualização de dados, é necessário identificar os registros a serem atualizados');
        }

        if ($this->criteria->isEmpty()) {
            $model = $this->find($id);

            if ($model->update($data)) {
                $this->app['events']->dispatch(new EntityUpdated($this, $this->model));

                return $model;
            }
        } else {
            $this->applyCriteria();

            $builder = $this->getBuilder();

            if ($update = $builder->update($data)) {
                $this->resetModel();
                $this->app['events']->dispatch(new EntityUpdated($this, $this->model));

                return $update;
            }
        }

        throw new RepositoryException('Não foi possível atualizar o registro. Tente novamente');
    }

    public function find(int $id, $columns = ['*']): ?Model
    {
        $this->applyCriteria();

        return $this->model->find($id, $columns);
    }

    public function getBuilder(): Builder
    {
        return $this->model->newQuery();
    }

    public function delete(int $id): bool
    {
        $this->resetModel();

        $model    = $this->find($id);
        $original = clone $model;

        if ($model->delete()) {
            $this->app['events']->dispatch(new EntityDeleted($this, $original));

            return true;
        }

        throw new RepositoryException('Não foi possível apagar o registro. Tente Novamente');
    }

    public function destroy(array $records): bool
    {
        $this->applyCriteria();

        if ($this->model->destroy($records)) {
            $this->app['events']->dispatch(new EntityDeleted($this, $this->model->getModel()));

            return true;
        }

        throw new RepositoryException('Os registros não foram apagados. Tente novamente');
    }

    public function sync(int $id, string $relation, string $attributes, bool $detach = true)
    {
        $this->resetModel();

        return $this->find($id)->{$relation}()->sync($attributes, $detach);
    }

    public function with(array $relations): AbstractRepository
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    public function pluck(string $value, ?string $key = null): array
    {
        $this->applyCriteria();

        return $this->model->pluck($value, $key)->toArray();
    }

    public function simplePaginate(int $perPage = 15, array $columns = ['*']): AbstractPaginator
    {
        return $this->paginate($perPage, $columns, 'simplePaginate');
    }

    public function paginate(int $perPage = 15, array $columns = ['*'], $method = 'paginate'): AbstractPaginator
    {
        $this->applyCriteria();

        $results = $this->model->{$method}($perPage, $columns);

        $results->appends($this->app->make('request')->query());

        return $results;
    }

    public function findOrFail(int $id, $columns = ['*']): Model
    {
        $this->applyCriteria();

        return $this->model->findOrFail($id, $columns);
    }

    public function last(array $columns = ['*']): ?Model
    {
        $this->applyCriteria();

        return $this->orderBy($this->getKeyName(), 'desc')->first($columns);
    }

    public function first(array $columns = ['*']): ?Model
    {
        $this->applyCriteria();

        return $this->model->first($columns);
    }

    public function orderBy(string $column, $order = 'asc'): AbstractRepository
    {
        $this->model = $this->model->orderBy($column, $order);

        return $this;
    }

    private function getKeyName(): string
    {
        if ($this->model instanceof Builder) {
            $model = $this->model->getModel();

            return $model->getKeyName();
        }

        return $this->model->getKeyName();
    }

    public function findBy(string $field, $value, array $columns = ['*']): ?Model
    {
        $this->applyCriteria();

        return $this->model->where($field, '=', $value)->first($columns);
    }

    public function findAllBy(string $field, $value, array $columns = ['*']): Collection
    {
        $this->applyCriteria();

        return $this->model->where($field, '=', $value)->get($columns);
    }

    public function limit(int $limit): AbstractRepository
    {
        $this->model = $this->model->limit($limit);

        return $this;
    }

    public function offset(int $offset): AbstractRepository
    {
        $this->model = $this->model->offset($offset);

        return $this;
    }

    public function having(string $column, string $operator, $value): AbstractRepository
    {
        $this->groupBy($column);

        $this->model = $this->model->having($column, $operator, $value);

        return $this;
    }

    public function groupBy(...$columns): AbstractRepository
    {
        $this->model = $this->model->groupBy($columns);

        return $this;
    }

    public function getByCriteria(AbstractCriteria $criteria): Collection
    {
        $this->pushCriteria($criteria);

        return $this->all();
    }

    public function pushCriteria(AbstractCriteria $criteria)
    {
        if ($this->preventCriteriaOverwriting) {
            // Find existing criteria
            $key = $this->criteria->search(function ($item) use ($criteria) {
                return (is_object($item) && (get_class($item) == get_class($criteria)));
            });

            // Remove old criteria
            if (is_int($key)) {
                $this->criteria->offsetUnset($key);
            }
        }

        $this->criteria->push($criteria);

        return $this;
    }

    public function all(array $columns = ['*']): Collection
    {
        $this->applyCriteria();

        return $this->model->get($columns);
    }

    public function enableQueryLog(): void
    {
        $this->connection()->enableQueryLog();
    }

    public function disableQueryLog(): void
    {
        $this->connection()->disableQueryLog();
    }

    public function getLastQuery(): ?string
    {
        $logs = $this->getQueryLog();
        $last = array_pop($logs);

        if (is_null($last)) {
            return null;
        }

        return Str::replaceArray('?', $last['bindings'], $last['query']);
    }

    public function getQueryLog(): array
    {
        return $this->connection()->getQueryLog();
    }

    public function exists(): bool
    {
        $this->applyCriteria();

        return $this->model->exists();
    }

    public function doesntExists(): bool
    {
        $this->applyCriteria();

        return $this->model->doesntExist();
    }

    public function increment(string $column, $amount = 1, array $extra = []): int
    {
        $this->applyCriteria();

        return $this->model->increment($column, $amount, $extra);
    }

    public function decrement(string $column, $amount = 1, array $extra = []): int
    {
        $this->applyCriteria();

        return $this->model->decrement($column, $amount, $extra);
    }

    public function select(string $query, array $bindings = [], bool $useReadPdo = true): Collection
    {
        $this->resetModel();

        return $this->model->newCollection(
            $this->connection()->select($query, $bindings, $useReadPdo)
        );
    }

    public function selectOne(string $query, array $bindings = [], bool $useReadPdo = true): ?Model
    {
        $this->resetModel();

        if ($result = $this->connection()->selectOne($query, $bindings, $useReadPdo)) {
            return $this->model->newInstance(
                json_decode(json_encode($result), true), true
            );
        }

        return null;
    }

    public function statement(string $query, array $bindings = []): bool
    {
        return $this->connection()->statement($query, $bindings);
    }

    public function raw(string $value): Expression
    {
        return $this->connection()->raw($value);
    }

    public function chunk(int $count, callable $callback): bool
    {
        $this->applyCriteria();

        return $this->model->chunk($count, $callback);
    }

    public function chunkById(int $count, callable $callback, string $column = null, string $alias = null): bool
    {
        $this->applyCriteria();

        return $this->model->chunkById($count, $callback, $column, $alias);
    }

    public function cursor(): LazyCollection
    {
        $this->applyCriteria();

        return $this->model->cursor();
    }
}
