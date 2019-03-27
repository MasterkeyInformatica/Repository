<?php

namespace Masterkey\Repository\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Http\Request;
use Masterkey\Repository\Cache\CacheKeyStorage;
use Masterkey\Repository\AbstractCriteria;
use ReflectionObject;

/**
 * ShouldBeCached
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   27/03/2019
 * @package Masterkey\Repository\Traits
 */
trait ShouldBeCached
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var CacheKeyStorage
     */
    protected $keyStorage;

    /**
     * @return  void
     */
    public function bootShouldBeCached()
    {
        $this->setCache($this->app->make(Cache::class));

        $this->setKeyStorage($this->app->make(CacheKeyStorage::class));
    }

    /**
     * @param   Cache  $cache
     * @return  $this
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @return  Cache
     */
    public function getCache() : Cache
    {
        return $this->cache;
    }

    /**
     * @param   CacheKeyStorage  $keyStorage
     * @return  $this
     */
    public function setKeyStorage(CacheKeyStorage $keyStorage)
    {
        $this->keyStorage = $keyStorage;

        return $this;
    }

    /**
     * @param   bool  $status
     * @return  $this
     */
    public function skipCache(bool $status = true)
    {
        $this->skipCache = $status;

        return $this;
    }

    /**
     * @return  bool
     */
    public function isSkippedCache()
    {
        $skipped        = $this->skipCache ?? false;
        $request        = $this->app->make(Request::class);
        $skipCacheParam = Config::get('repository.cache.params.skipCache', 'skipCache');

        if ( $request->has($skipCacheParam) ) {
            $skipped = true;
        }

        return $skipped;
    }

    /**
     * @param   string  $method
     * @return  bool
     */
    protected function allowedCache($method)
    {
        $cacheEnabled = Config::get('repository.cache.enabled', true);

        if ( ! $cacheEnabled ) {
            return false;
        }

        $cacheOnly      = $this->cacheOnly ?? Config::get('repository.cache.allowed.only', null);
        $cacheExcept    = $this->cacheExcept ?? Config::get('repository.cache.allowed.except', null);

        if ( is_array($cacheOnly) ) {
            return in_array($method, $cacheOnly);
        }

        if ( is_array($cacheExcept) ) {
            return ! in_array($method, $cacheExcept);
        }

        if ( is_null($cacheOnly) && is_null($cacheExcept) ) {
            return true;
        }

        return false;
    }

    /**
     * @param   string  $method
     * @param   mixed  $args
     * @return  string
     */
    public function getCacheKey($method, $args = null)
    {
        $request    = $this->app->make(Request::class);
        $args       = serialize($args);
        $criteria   = $this->serializeCriteria();
        $key        = sprintf('%s@%s-%s', get_called_class(), $method, md5($args . $criteria . $request->fullUrl()));

        $this->keyStorage->storeKey(get_called_class(), $key);

        return $key;
    }

    /**
     * @return string
     */
    protected function serializeCriteria()
    {
        try {
            return serialize($this->getCriteria());
        } catch (Exception $e) {
            return serialize($this->getCriteria()->map(function ($criterion)
            {
                return $this->serializeCriterion($criterion);
            }));
        }
    }

    /**
     * @param   mixed  $criterion
     * @return  array
     * @throws  Exception
     */
    protected function serializeCriterion($criterion)
    {
        try {
            serialize($criterion);
            return $criterion;
        } catch (Exception $e) {
            // We want to take care of the closure serialization errors,
            // other than that we will simply re-throw the exception.
            if ($e->getMessage() !== "Serialization of 'Closure' is not allowed") {
                throw $e;
            }

            $r = new ReflectionObject($criterion);

            return [
                'hash' => md5((string) $r),
                'properties' => $r->getProperties(),
            ];
        }
    }

    /**
     * @return  int
     */
    public function getCacheMinutes() : int
    {
        return $this->cacheMinutes ?? Config::get('repository.cache.minutes', 30);
    }

    /**
     * @param   array  $columns
     * @return  mixed
     */
    public function all(array $columns = ['*']) : Collection
    {
        if ( ! $this->allowedCache('all') || $this->isSkippedCache() ) {
            return parent::all($columns);
        }

        $key        = $this->getCacheKey('all', func_get_args());
        $minutes    = $this->getCacheMinutes();

        return $this->getCache()->remember($key, $minutes, function () use ($columns)
        {
            return parent::all($columns);
        });
    }

    /**
     * @param   int  $perPage
     * @param   array  $columns
     * @param   string  $method
     * @return  mixed
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], $method = 'paginate')
    {
        if ( ! $this->allowedCache('paginate') || $this->isSkippedCache() ) {
            return parent::paginate($perPage, $columns, $method);
        }

        $key        = $this->getCacheKey($method, func_get_args());
        $minutes    = $this->getCacheMinutes();

        return $this->getCache()->remember($key, $minutes, function () use($perPage, $columns, $method)
        {
            return parent::paginate($perPage, $columns, $method);
        });
    }

    /**
     * @param   AbstractCriteria  $criteria
     * @return  Collection
     */
    public function getByCriteria(AbstractCriteria $criteria) : Collection
    {
        if ( ! $this->allowedCache('getByCriteria') || $this->isSkippedCache() ) {
            return parent::getByCriteria($criteria);
        }

        $key        = $this->getCacheKey('getByCriteria', func_get_args());
        $minutes    = $this->getCacheMinutes();

        return $this->getCache()->remember($key, $minutes, function () use($criteria)
        {
            return parent::getByCriteria($criteria);
        });
    }

    /**
     * @param   int  $id
     * @param   array  $columns
     * @return  mixed
     */
    public function find(int $id, $columns = ['*']) : Model
    {
        if ( ! $this->allowedCache('find') || $this->isSkippedCache() ) {
            return parent::find($id, $columns);
        }

        $key        = $this->getCacheKey('find', func_get_args());
        $minutes    = $this->getCacheMinutes();

        return $this->getCache()->remember($key, $minutes, function () use ($id, $columns)
        {
            return parent::find($id, $columns);
        });
    }

    /**
     * @param   string  $attribute
     * @param   mixed  $value
     * @param   array  $columns
     * @return  Model
     */
    public function findBy($attribute, $value, array $columns = ['*']) : Model
    {
        if ( ! $this->allowedCache('findBy') || $this->isSkippedCache() ) {
            return parent::findBy($attribute, $value, $columns);
        }

        $key        = $this->getCacheKey('findBy', func_get_args());
        $minutes    = $this->getCacheMinutes();

        return $this->getCache()->remember($key, $minutes, function () use ($attribute, $value, $columns)
        {
            return parent::findBy($attribute, $value, $columns);
        });
    }

    /**
     * @param   string  $attribute
     * @param   mixed  $value
     * @param   array  $columns
     * @return  Collection
     */
    public function findAllBy($attribute, $value, array $columns = ['*']) : Collection
    {
        if ( ! $this->allowedCache('findAllBy') || $this->isSkippedCache() ) {
            return parent::findAllBy($attribute, $value, $columns);
        }

        $key        = $this->getCacheKey('findAllBy', func_get_args());
        $minutes    = $this->getCacheMinutes();

        return $this->getCache()->remember($key, $minutes, function () use ($attribute, $value, $columns)
        {
            return parent::findAllBy($attribute, $value, $columns);
        });
    }
}