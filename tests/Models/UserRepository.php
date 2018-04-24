<?php

namespace Masterkey\Tests\Models;

use Masterkey\Repository\AbstractRepository;


class UserRepository extends AbstractRepository
{
    protected $fieldsSearchable = [
        'name'
    ];

    public function model()
    {
        return User::class;
    }

    public function validator()
    {
        return UserValidator::class;
    }
}
