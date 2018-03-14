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

    public function bootstrapCache()
    {
        $cache = Config::get('repository.cache.repository', 'cache');
        $this->setCache($this->app->make($cache));

        $this->setKeyStorage($this->app->make(CacheKeyStorage::class));
    }

    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    public function getCache() : Cache
    {
        return $this->cache;
    }

    public function setKeyStorage(CacheKeyStorage $keyStorage)
    {
        $this->keyStorage = $keyStorage;

        return $this;
    }

    public function skipCache(bool $status = true)
    {
        $this->skipCache = $status;

        return $this;
    }

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

    public function getCacheKey($method, $args = null)
    {
        $request    = $this->app->make(Request::class);
        $args       = serialize($args);
        $criteria   = $this->serializeCriteria();
        $key        = sprintf('%s@%s-%s', get_called_class(), $method, md5($args . $criteria . $request->fullUrl()));

        $this->keyStorage->storeKey(get_called_class(), $key);

        return $key;
    }

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

    public function getCacheMinutes() : int
    {
        return $this->cacheMinutes ?? Config::get('repository.cache.minutes', 30);
    }

    public function all(array $columns = ['*'])
    {
        if ( ! $this->allowedCache('all') || $this->isSkippedCache() ) {
            return parent::all($columns);
        }

        $key        = $this->getCacheKey('all', func_get_args());
        $minutes    = $this->getCacheMinutes();

        $this->getCache()->remember($key, $minutes, function () use ($columns) {
            return parent::all($columns);
        });
    }
}