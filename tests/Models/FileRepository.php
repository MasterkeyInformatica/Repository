<?php

namespace Masterkey\Tests\Models;

use Masterkey\Repository\BaseRepository;
use Masterkey\Repository\Contracts\CachableContract;
use Masterkey\Repository\Traits\ShouldBeCached;

class FileRepository extends BaseRepository implements CachableContract
{
    use ShouldBeCached;

    protected $cacheOnly = ['all'];

    public function model()
    {
        return File::class;
    }
}