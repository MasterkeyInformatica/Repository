<?php

namespace Masterkey\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Masterkey\Repository\Contracts\CountableInterface;
use Masterkey\Repository\Contracts\CreatableInterface;
use Masterkey\Repository\Contracts\CriteriaInterface;
use Masterkey\Repository\Contracts\RepositoryInterface;
use Masterkey\Repository\Contracts\SearchableInterface;
use Masterkey\Repository\Contracts\SortableInterface;
use Masterkey\Repository\Contracts\ValidatorInterface;
use RepositoryException;

/**
 * BaseRepository
 *
 * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version  4.0.0
 * @since    24/04/2018
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
    use Traits\NeedsBeCountable,
        Traits\NeedsBeCreatable,
        Traits\NeedsBeCriteriable,
        Traits\NeedsBeSearchable,
        Traits\NeedsBeSortable,
        Traits\ShouldValidate;

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
     * @return  null|string
     */
    public function validator()
    {
        return null;
    }

    /**
     * @param   string  $model
     * @throws  RepositoryException
     */
    public function makeModel($model)
    {
        $model = $this->app->make($model);

        if ( ! $model instanceof Model) {
            throw new RepositoryException("Class {$model} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        $this->model = $model;
    }

    /**
     * @throws  RepositoryException
     */
    public function resetModel()
    {
        $this->makeModel($this->model());
    }

    /**
     * @return void
     */
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
}