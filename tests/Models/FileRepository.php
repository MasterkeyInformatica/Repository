<?php

namespace Masterkey\Tests\Models;

use Masterkey\Repository\AbstractRepository;
use Masterkey\Repository\Contracts\CachableInterface;
use Masterkey\Repository\Traits\ShouldBeCached;

class FileRepository extends AbstractRepository implements CachableInterface
{
    use ShouldBeCached;

    protected $cacheOnly = ['all', 'paginate', 'getByCriteria', 'find', 'findBy', 'findAllBy'];

    public function model()
    {
        return File::class;
    }
}