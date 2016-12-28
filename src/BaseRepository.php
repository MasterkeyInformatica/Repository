<?php

    namespace Masterkey\Repository;

    use Illuminate\Support\Collection;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Container\Container as App;

    use Masterkey\Repository\Contracts\CriteriaContract;
    use Masterkey\Repository\Contracts\RepositoryContract;
    use Masterkey\Repository\Exceptions\RepositoryException;

    /**
     * BaseRepository
     *
     * Classe desenvolvida para trabalhar com o padrão repository com o Laravel 5
     *
     * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
     * @version  1.0.0
     * @since    28/12/2016
     * @package  Masterkey\Repository
     */
    abstract class BaseRepository implements CriteriaContract, RepositoryContract
    {
        /**
         * Container's instance
         * 
         * @var App
         */
        protected $app;

        /**
         * Model's instance
         * 
         * @var Model
         */
        protected $model;

        /**
         * Collection's instance
         * 
         * @var Collection
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
         * Class Constructor
         * 
         * @param   App  $app
         * @param   Collection  $collection
         */
        public function __construct(App $app, Collection $collection)
        {
            $this->app      = $app;
            $this->criteria = $collection;

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
         * Return all columns in DB
         * 
         * @param   array  $columns
         * @return  
         */
        public function all($columns = ['*'])
        {
            $this->applyCriteria();
            return $this->model->get($columns);
        }

        /**
         * Returns the model with relationships
         * 
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
            $laravel = app();

            $this->applyCriteria();

            if($laravel::VERSION >= 5.2) {
                $lists = $this->model->pluck($value, $key);
            } else {
                $lists = $this->model->lists($value, $key);
            }

            if (is_array($lists)) {
                return $lists;
            }

            return $lists->all();
        }

        /**
         * Paginate results from DB
         * 
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
         * Creates a new model
         * 
         * @param   array  $data
         * @return  mixed|bool
         */
        public function create(array $data)
        {
            return $this->model->create($data);
        }

        /**
         * Save a new model without mass assignement
         * 
         * @param   array  $data
         * @return  bool
         */
        public function save(array $data)
        {
            foreach ($data as $k => $v) {
                $this->model->$k = $v;
            }
           return $this->model->save();
        }

        /**
         * Update a model
         * 
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
         * Make a massive update on DB
         * 
         * @param   array  $data
         * @return  bool
         */
        public function massUpdate(array $data)
        {
            $this->applyCriteria();
            return $this->model->update($data);
        }

        /**
         * Delete a model
         * 
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
        public function destroy($id)
        {
            $this->applyCriteria();
            return $this->model->destroy($id0);
        }

        /**
         * Find a model in DB
         * 
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
         * @return   Collection
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
    }
