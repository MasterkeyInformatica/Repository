<?php

namespace Masterkey\Tests\Repositories;

use Illuminate\Cache\Repository;
use Illuminate\Support\Collection;
use Masterkey\Tests\Models\FileRepository;
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
        $this->cache    = $app->make('cache');

        parent::__construct();
    }

    public function test_cache_of_all()
    {
        $all    = $this->files->all();
        $cache  = $this->cache->has('Masterkey\\Tests\\Models\\FileRepository@all-f827e42a16eb250430cb3fe01ffa24bb');
        $cached = $this->cache->get('Masterkey\\Tests\\Models\\FileRepository@all-f827e42a16eb250430cb3fe01ffa24bb');

        $this->assertInstanceOf(Collection::class, $all);
        $this->assertInstanceOf(Collection::class, $cached);
        $this->assertTrue($cache);
    }
}
