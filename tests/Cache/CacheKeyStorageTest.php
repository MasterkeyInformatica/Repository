<?php

namespace Masterkey\Tests\Cache;

use Masterkey\Repository\Cache\CacheKeyStorage;
use PHPUnit\Framework\TestCase;

class CacheKeyStorageTest extends TestCase
{
    protected $keyStorage;

    public function __construct()
    {
        global $app;

        $this->keyStorage = $app->make(CacheKeyStorage::class);

        parent::__construct();
    }

    public function test_cache_key_creation()
    {
        $this->keyStorage->storeKey('user', 'create');
        $key = $this->keyStorage->readKeys('user');

        $this->assertFileExists(__DIR__ . '/../../app/repository-cache-keys.json');
        $this->assertEquals($key[0], 'create');
    }
}
