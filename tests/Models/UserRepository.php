<?php

    namespace Masterkey\Tests\Models;

    use Masterkey\Repository\BaseRepository;

    class UserRepository extends BaseRepository
    {
        public function __construct($app, $collection)
        {
            parent::__construct($app, $collection);
        }

        public function model()
        {
            return User::class;
        }
    }
