<?php

namespace Masterkey\Repository\Traits;

use Exception;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\{Collection, Facades\Config};
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Cache\CacheKeyStorage;
use ReflectionObject;

/**
 * ShouldBeCached
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Traits
 */
trait ShouldBeCached
{
    protected Cache $cache;

    protected CacheKeyStorage $keyStorage;

    public function bootShouldBeCached(): void
    {
        $this->setCache($this->app->make(Cache::class));

        $this->setKeyStorage($this->app->make(CacheKeyStorage::class));
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

    public function all(array $columns = ['*']): Collection
    {
        if (!$this->allowedCache('all') || $this->isSkippedCache()) {
            return parent::all($columns);
        }

        $key     = $this->getCacheKey('all', func_get_args());
        $minutes = $this->getCacheMinutes();

        return $this->getCache()->remember($key, $minutes, function () use ($columns) {
            return parent::all($columns);
        });
    }

    protected function allowedCache(string $method): bool
    {
        $cacheEnabled = Config::get('repository.cache.enabled', true);

        if (!$cacheEnabled) {
            return false;
        }

        $cacheOnly   = $this->cacheOnly ?? Config::get('repository.cache.allowed.only', null);
        $cacheExcept = $this->cacheExcept ?? Config::get('repository.cache.allowed.except', null);

        if (is_array($cacheOnly)) {
            return in_array($method, $cacheOnly);
        }

        if (is_array($cacheExcept)) {
            return !in_array($method, $cacheExcept);
        }

        if (is_null($cacheOnly) && is_null($cacheExcept)) {
            return true;
        }

        return false;
    }

    public function isSkippedCache(): bool
    {
        $skipped        = $this->skipCache ?? false;
        $request        = $this->app->make(Request::class);
        $skipCacheParam = Config::get('repository.cache.params.skipCache', 'skipCache');

        if ($request->has($skipCacheParam)) {
            $skipped = true;
        }

        return $skipped;
    }

    public function getCacheKey(string $method, $args = null): string
    {
        $request  = $this->app->make(Request::class);
        $args     = serialize($args);
        $criteria = $this->serializeCriteria();
        $key      = sprintf('%s@%s-%s', get_called_class(), $method, md5($args . $criteria . $request->fullUrl()));

        $this->keyStorage->storeKey(get_called_class(), $key);

        return $key;
    }

    protected function serializeCriteria(): string
    {
        try {
            return serialize($this->getCriteria());
        } catch (Exception $e) {
            return serialize($this->getCriteria()->map(function ($criterion) {
                return $this->serializeCriterion($criterion);
            }));
        }
    }

    protected function serializeCriterion($criterion): array
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
                'hash'       => md5((string)$r),
                'properties' => $r->getProperties(),
            ];
        }
    }

    public function getCacheMinutes(): int
    {
        return $this->cacheMinutes ?? Config::get('repository.cache.minutes', 30);
    }

    public function getCache(): Cache
    {
        return $this->cache;
    }

    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    public function paginate(int $perPage = 15, array $columns = ['*'], string $method = 'paginate'): AbstractPaginator
    {
        if (!$this->allowedCache('paginate') || $this->isSkippedCache()) {
            return parent::paginate($perPage, $columns, $method);
        }

        $key     = $this->getCacheKey($method, func_get_args());
        $minutes = $this->getCacheMinutes();

        return $this->getCache()->remember($key, $minutes, function () use ($perPage, $columns, $method) {
            return parent::paginate($perPage, $columns, $method);
        });
    }

    public function getByCriteria(AbstractCriteria $criteria): Collection
    {
        if (!$this->allowedCache('getByCriteria') || $this->isSkippedCache()) {
            return parent::getByCriteria($criteria);
        }

        $key     = $this->getCacheKey('getByCriteria', func_get_args());
        $minutes = $this->getCacheMinutes();

        return $this->getCache()->remember($key, $minutes, function () use ($criteria) {
            return parent::getByCriteria($criteria);
        });
    }

    public function find(int $id, $columns = ['*']): ?Model
    {
        if (!$this->allowedCache('find') || $this->isSkippedCache()) {
            return parent::find($id, $columns);
        }

        $key     = $this->getCacheKey('find', func_get_args());
        $minutes = $this->getCacheMinutes();

        return $this->getCache()->remember($key, $minutes, function () use ($id, $columns) {
            return parent::find($id, $columns);
        });
    }

    public function findBy(string $field, $value, array $columns = ['*']): ?Model
    {
        if (!$this->allowedCache('findBy') || $this->isSkippedCache()) {
            return parent::findBy($field, $value, $columns);
        }

        $key     = $this->getCacheKey('findBy', func_get_args());
        $minutes = $this->getCacheMinutes();

        return $this->getCache()->remember($key, $minutes, function () use ($field, $value, $columns) {
            return parent::findBy($field, $value, $columns);
        });
    }

    public function findAllBy($field, $value, array $columns = ['*']): Collection
    {
        if (!$this->allowedCache('findAllBy') || $this->isSkippedCache()) {
            return parent::findAllBy($field, $value, $columns);
        }

        $key     = $this->getCacheKey('findAllBy', func_get_args());
        $minutes = $this->getCacheMinutes();

        return $this->getCache()->remember($key, $minutes, function () use ($field, $value, $columns) {
            return parent::findAllBy($field, $value, $columns);
        });
    }
}