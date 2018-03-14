<?php

namespace Masterkey\Tests\Cache;

use Masterkey\Repository\Cache\CacheKeyStorage;
use PHPUnit\Framework\TestCase;

class CacheKeyStorageTest extends TestCase
{
    public function test_cache_key_creation()
    {
        $path = __DIR__ . '/../../app';

        $keyStorage = new CacheKeyStorage($path);
        $keyStorage->storeKey('user', 'create');

        $this->assertFileExists($path . '/' . 'repository-cache-keys.json');
    }

    public function test_read_cache_key()
    {
        $path = __DIR__ . '/../../app';

        $keyStorage = new CacheKeyStorage($path);
        $key = $keyStorage->readKeys('user');

        $this->assertEquals($key[0], 'create');
    }
}
