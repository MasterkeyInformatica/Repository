<?php

namespace Masterkey\Tests\Models;

use Masterkey\Repository\BaseRepository;
use Masterkey\Repository\Contracts\CachableInterface;
use Masterkey\Repository\Traits\ShouldBeCached;

class FileRepository extends BaseRepository implements CachableInterface
{
    use ShouldBeCached;

    protected $cacheOnly = ['all', 'paginate', 'getByCriteria', 'find', 'findBy', 'findAllBy'];

    public function model()
    {
        return File::class;
    }
}