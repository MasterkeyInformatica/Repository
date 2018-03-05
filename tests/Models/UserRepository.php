<?php

namespace Masterkey\Tests\Models;

use Masterkey\Repository\BaseRepository;


class UserRepository extends BaseRepository
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
