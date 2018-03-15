<?php

namespace Masterkey\Repository\Traits;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Http\Request;
use Masterkey\Repository\Cache\CacheKeyStorage;
use ReflectionObject;

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
        $this->setCache($this->app->make('cache'));

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

        if ( is_array($cacheEnabled) ) {
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
            return serialize($this->getCriteria()->map(function ($criterion) {
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
    public function all(array $columns = ['*'])
    {
        if ( ! $this->allowedCache('all') || $this->isSkippedCache() ) {
            return parent::all($columns);
        }

        $key        = $this->getCacheKey('all', func_get_args());
        $minutes    = $this->getCacheMinutes();

        return $this->getCache()->remember($key, $minutes, function () use ($columns) {
            return parent::all($columns);
        });
    }
}