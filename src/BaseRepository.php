<?php

    namespace Masterkey\Repository;

    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Contracts\Container\Container;
    use Illuminate\Database\Eloquent\Model;

    use Masterkey\Repository\Contracts\CriteriaContract;
    use Masterkey\Repository\Contracts\RepositoryContract;

    use Masterkey\Repository\Exceptions\Model\ModelNotSavedException;
    use Masterkey\Repository\Exceptions\RepositoryException;

    /**
     * BaseRepository
     *
     * Classe desenvolvida para trabalhar com o padrão repository com o Laravel 5
     *
     * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
     * @version  1.1.0
     * @since    31/01/2017
     * @package  Masterkey\Repository
     */
    abstract class BaseRepository implements CriteriaContract, RepositoryContract
    {
        /**
         * @var \Illuminate\Contracts\Container\Container
         */
        protected $app;

        /**
         * @var \Illuminate\Database\Eloquent\Model
         */
        protected $model;

        /**
         * @var \Illuminate\Contracts\Support\Arrayable
         */
        protected $criteria;

        /**
         * Skip defined criterias
         *
         * @var boolean
         */
        protected $skipCriteria = false;

        /**
         * Prevent criteria's overwriting
         *
         * @var boolean
         */
        protected $preventCriteriaOverwriting = true;

        /**
         * @param   Container  $container
         * @param   Arrayable  $arrayable
         */
        public function __construct(Container $container, Arrayable $arrayable)
        {
            $this->app      = $container;
            $this->criteria = $arrayable;

            $this->resetScope();
            $this->makeModel();
        }

        /**
         * Return the model from a Repository
         *
         * @return  mixed
         */
        public abstract function model();

        /**
         * @param   array  $columns
         * @return  \Illuminate\Support\Collection
         */
        public function all($columns = ['*'])
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
         * Create a key -> value with DB data
         *
         * @param   string  $value
         * @param   string|null $key
         * @return  mixed
         */
        public function pluck($value, $key = null)
        {
            $this->applyCriteria();

            $lists = $this->model->pluck($value, $key);

            if (is_array($lists)) {
                return $lists;
            }

            return $lists->all();
        }

        /**
         * @param   integer  $perPage
         * @param   array  $columns
         * @return  mixed
         */
        public function paginate($perPage = 15, $columns = array('*'))
        {
            $this->applyCriteria();
            return $this->model->paginate($perPage, $columns);
        }

        /**
         * @param   array  $data
         * @return  mixed|bool
         * @throws  ModelNotSavedException
         */
        public function create(array $data)
        {
            $model = $this->model->create($data);

            if($model) {
                return $model;
            }

            throw new ModelNotSavedException;
        }

        /**
         * @param   array $data
         * @return  mixed
         * @throws  ModelNotSavedException
         */
        public function firstOrCreate(array $data)
        {
            $model = $this->model->firstOrCreate($data);

            if($model) {
                return $model;
            }

            throw new ModelNotSavedException;
        }

        /**
         * Save a new model without mass assignement
         *
         * @param   array  $data
         * @return  bool
         * @throws  ModelNotSavedException
         */
        public function save(array $data)
        {
            foreach ($data as $k => $v) {
                $this->model->$k = $v;
            }

            if($this->model->save()) {
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
        public function massInsert(array $data)
        {
            if($this->model->insert($data)) {
                return true;
            }

            throw new ModelNotSavedException('Não foi possível salvar alguns registros. Tente novamente');
        }

        /**
         * @param   int  $id
         * @param   array  $data
         * @return  bool
         */
        public function update($id, array $data)
        {
            $model = $this->find($id);
            return $model->update($data);
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
         */
        public function delete($id)
        {
            $model = $this->find($id);
            return $model->destroy($id);
        }

        /**
         * Make a massive deleting on DB
         *
         * @param   int|array  $ids
         * @return  bool
         */
        public function destroy($ids)
        {
            $this->applyCriteria();
            return $this->model->destroy($ids);
        }

        /**
         * @param   int   $id
         * @param   array  $columns
         * @return  mixed
         */
        public function find($id, $columns = array('*'))
        {
            $this->applyCriteria();
            return $this->model->findOrFail($id, $columns);
        }

        /**
         * @param   array  $columns
         * @return  mixed
         */
        public function first($columns = ['*'])
        {
            $this->applyCriteria();

            return $this->model->first($columns);
        }

        /**
         * @param   array  $columns
         * @return  mixed
         */
        public function last($columns = ['*'])
        {
            $this->applyCriteria();

            $primaryKey = $this->model->getKeyName();

            return $this->model
                        ->orderBy($primaryKey, 'desc')
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
        public function findBy($attribute, $value, $columns = array('*'))
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
        public function findAllBy($attribute, $value, $columns = array('*'))
        {
            $this->applyCriteria();
            return $this->model->where($attribute, '=', $value)->get($columns);
        }

        /**
         * Search for records usgin where
         *
         * @param   array  $where
         * @param   array  $columns
         * @param   boolean $or
         * @return  mixed
         */
        public function findWhere($where, $columns = ['*'], $or = false)
        {
            $this->applyCriteria();
            $model = $this->model;

            foreach ($where as $field => $value) {
                if ($value instanceof \Closure) {
                    $model = (!$or)
                        ? $model->where($value)
                        : $model->orWhere($value);
                } else if (is_array($value)) {
                    if (count($value) === 3) {
                        list($field, $operator, $search) = $value;
                        $model = (!$or)
                            ? $model->where($field, $operator, $search)
                            : $model->orWhere($field, $operator, $search);
                    } else if (count($value) === 2) {
                        list($field, $search) = $value;
                        $model = (!$or)
                            ? $model->where($field, '=', $search)
                            : $model->orWhere($field, '=', $search);
                    }
                } else {
                    $model = (!$or)
                        ? $model->where($field, '=', $value)
                        : $model->orWhere($field, '=', $value);
                }
            }

            return $model->get($columns);
        }

        /**
         * Call the factory for the model specified on the Repository
         *
         * @return  \Illuminate\Database\Eloquent\Builder
         */
        public function makeModel()
        {
            return $this->setModel($this->model());
        }

        /**
         * Create the model's instance
         *
         * @param   Model  $eloquentModel
         * @return  \Illuminate\Database\Eloquent\Builder
         * @throws  RepositoryException
         */
        public function setModel($eloquentModel)
        {
            $model = $this->app->make($eloquentModel);

            if (!$model instanceof Model) {
                throw new RepositoryException("Class {$model} must be an instance of Illuminate\\Database\\Eloquent\\Model");
            }

            return $this->model = $model;
        }

        /**
         * Reset querying's scope
         *
         * @return   $this
         */
        public function resetScope()
        {
            $this->skipCriteria(false);
            return $this;
        }

        /**
         * Skip the criteria's querying scope
         *
         * @param   boolean  $status
         * @return  $this
         */
        public function skipCriteria($status = true)
        {
            $this->skipCriteria = $status;
            return $this;
        }

        /**
         * Return all criterias
         *
         * @return   \Illuminate\Database\Eloquent\Collection
         */
        public function getCriteria()
        {
            return $this->criteria;
        }

        /**
         * Apply a criteria on a model
         *
         * @param   Criteria  $criteria
         * @return  $this
         */
        public function getByCriteria(Criteria $criteria)
        {
            $this->model = $criteria->apply($this->model, $this);
            return $this;
        }

        /**
         * Push Criteria into Criteria's collection
         *
         * @param   Criteria  $criteria
         * @return  $this
         */
        public function pushCriteria(Criteria $criteria)
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

        /**
         * Apply a criteria on a model
         *
         * @return   $this
         */
        public function applyCriteria()
        {
            if ($this->skipCriteria === true){
                return $this;
            }

            foreach ($this->getCriteria() as $criteria) {
                if ($criteria instanceof Criteria) {
                    $this->model = $criteria->apply($this->model, $this);
                }
            }

            return $this;
        }

        /**
         * @return  integer
         */
        public function count()
        {
            $this->applyCriteria();
            return $this->model->count();
        }

        /**
         * @param   string  $column
         * @return  int|float
         */
        public function max($column)
        {
            $this->applyCriteria();
            return $this->model->max($column);
        }

        /**
         * @param   string  $column
         * @return  int|float
         */
        public function min($column)
        {
            $this->applyCriteria();
            return $this->model->min($column);
        }

        /**
         * @param   string  $column
         * @return  int|float
         */
        public function avg($column)
        {
            $this->applyCriteria();
            return $this->model->avg($column);
        }

        /**
         * @param   string  $column
         * @return  int|float
         */
        public function sum($column)
        {
            $this->applyCriteria();
            return $this->model->sum($column);
        }
    }
