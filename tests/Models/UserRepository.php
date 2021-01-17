<?php

namespace Masterkey\Tests\Models;

use Masterkey\Repository\AbstractRepository;

class UserRepository extends AbstractRepository
{
    protected array $fieldsSearchable = [
        'name'
    ];

    public function model(): string
    {
        return User::class;
    }
}
