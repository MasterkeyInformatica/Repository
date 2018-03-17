<?php

namespace Masterkey\Tests\Repositories;

use Illuminate\Cache\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Masterkey\Tests\Models\File;
use Masterkey\Tests\Models\FileRepository;
use Masterkey\Tests\Models\OrderFileByName;
use PHPUnit\Framework\TestCase;

class FileRepositoryTest extends TestCase
{
    protected $files;

    /**
     * @var Repository
     */
    protected $cache;

    public function __construct()
    {
        global $app;

        $this->files    = new FileRepository($app);
        $this->cache    = $app->make(Repository::class);

        parent::__construct();
    }

    public function test_cache_of_all()
    {
        $key = 'Masterkey\\Tests\\Models\\FileRepository@all-f827e42a16eb250430cb3fe01ffa24bb';

        $all    = $this->files->all();
        $cache  = $this->cache->has($key);
        $cached = $this->cache->get($key);

        $this->assertInstanceOf(Collection::class, $all);
        $this->assertInstanceOf(Collection::class, $cached);
        $this->assertEquals(4, $cached->count());
        $this->assertTrue($cache);
    }

    public function test_cache_of_paginate()
    {
        $key = "Masterkey\\Tests\\Models\\FileRepository@paginate-f827e42a16eb250430cb3fe01ffa24bb";

        $paginate   = $this->files->paginate();
        $hasCache   = $this->cache->has($key);
        $cached     = $this->cache->get($key);

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginate);
        $this->assertInstanceOf(LengthAwarePaginator::class, $cached);
        $this->assertTrue($hasCache);
    }

    public function test_cache_simple_paginate()
    {
        $key = 'Masterkey\\Tests\\Models\\FileRepository@simplePaginate-b1b27f0bdaf5bca745cba4f4ddf08e01';

        $paginate   = $this->files->simplePaginate();
        $hasCache   = $this->cache->has($key);
        $cached     = $this->cache->get($key);

        $this->assertInstanceOf(Paginator::class, $paginate);
        $this->assertInstanceOf(Paginator::class, $cached);
        $this->assertTrue($hasCache);
    }

    public function test_get_by_criteria()
    {
        $key = "Masterkey\\Tests\\Models\\FileRepository@getByCriteria-04a75b81192831dcb5cb25f2e1c95d80";

        $all        = $this->files->getByCriteria(new OrderFileByName);
        $hasCache   = $this->cache->has($key);
        $cached     = $this->cache->get($key);

        $this->assertInstanceOf(Collection::class, $all);
        $this->assertInstanceOf(Collection::class, $cached);
        $this->assertTrue($hasCache);
    }

    public function test_cache_find()
    {
        $key        = 'Masterkey\\Tests\\Models\\FileRepository@find-aa65770843b0c967f28dd9e1b171e992';
        $first      = $this->files->find(1);
        $hasCache   = $this->cache->has($key);
        $cached     = $this->cache->get($key);

        $this->assertInstanceOf(File::class, $first);
        $this->assertInstanceOf(Model::class, $cached);
        $this->assertTrue($hasCache);
    }

    public function test_cache_find_by()
    {
        $key        = 'Masterkey\\Tests\\Models\\FileRepository@findBy-96be8ee87f08aa49a54d90867a385c31';
        $findBy     = $this->files->findBy('file', 'horse.png');
        $hasCache   = $this->cache->has($key);
        $cached     = $this->cache->get($key);

        $this->assertInstanceOf(File::class, $findBy);
        $this->assertInstanceOf(Model::class, $cached);
        $this->assertTrue($hasCache);
    }

    public function test_cache_find_all_by()
    {
        $key        = 'Masterkey\\Tests\\Models\\FileRepository@findAllBy-05ffb3b833885b074c75a9954ce46dba';
        $findAllBy  = $this->files->findAllBy('file', 'dog.png');
        $hasCache   = $this->cache->has($key);
        $cached     = $this->cache->get($key);

        $this->assertInstanceOf(Collection::class, $findAllBy);
        $this->assertInstanceOf(Collection::class, $cached);
        $this->assertTrue($hasCache);
    }
}
