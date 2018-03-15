<?php

namespace Masterkey\Tests\Cache;

use Illuminate\Cache\Repository;
use Masterkey\Repository\Cache\CacheKeyStorage;
use Masterkey\Repository\Events\EntityCreated;
use Masterkey\Repository\Listeners\ClearRepositoryCache;
use Masterkey\Tests\Models\File;
use Masterkey\Tests\Models\FileRepository;
use PHPUnit\Framework\TestCase;

class ClearRepositoryCacheTest extends TestCase
{
    protected $app;

    protected $storageCache;

    protected $cache;

    public function __construct()
    {
        global $app;

        $this->app          = $app;
        $this->storageCache = $app->make(CacheKeyStorage::class);
        $this->cache        = $app->make(Repository::class);

        parent::__construct();
    }

    public function test_cache_forget()
    {
        $key = 'Masterkey\\Tests\\Models\\FileRepository@all-f827e42a16eb250430cb3fe01ffa24bb';

        $repository = new FileRepository($this->app);
        $model      = new File();

        $event      = new EntityCreated($repository, $model);
        $listener   = new ClearRepositoryCache($this->cache, $this->storageCache);
        $listener->handle($event);

        $this->assertFalse($this->cache->has($key));
        $this->assertNull($this->cache->get($key));
    }
}
