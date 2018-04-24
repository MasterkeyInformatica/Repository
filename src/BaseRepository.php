<?php

namespace Masterkey\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Masterkey\Repository\Contracts\CountableInterface;
use Masterkey\Repository\Contracts\CreatableInterface;
use Masterkey\Repository\Contracts\CriteriaInterface;
use Masterkey\Repository\Contracts\RepositoryInterface;
use Masterkey\Repository\Contracts\SearchableInterface;
use Masterkey\Repository\Contracts\SortableInterface;
use Masterkey\Repository\Contracts\ValidatorInterface;
use RepositoryException;
use ValidationException;

/**
 * BaseRepository
 *
 * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version  4.0.0
 * @since    24/04/2018
 * @package  Masterkey\Repository
 */
abstract class BaseRepository implements
    CountableInterface,
    CreatableInterface,
    CriteriaInterface,
    RepositoryInterface,
    SearchableInterface,
    SortableInterface
{
    use Traits\ClassBuilder,
        Traits\NeedsBeCountable,
        Traits\NeedsBeCreatable,
        Traits\NeedsBeCriteriable,
        Traits\NeedsBeSearchable,
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
     * @param   int  $limit
     * @return  $this
     */
    public function limit(int $limit)
    {
        $this->model->limit($limit);

        return $this;
    }
}
