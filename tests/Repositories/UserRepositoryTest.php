<?php

    use Mockery;

    class UserRepositoryTest extends PHPUnit_Framework_TestCase
    {
        public function testUserRepository()
        {
            $app        = Mockery::mock('Illuminate\Container\Container');
            $collection = Mockery::mock('Illuminate\Support\Collection');

            $app->shouldReceive('make')->once();

            $user = new UserRepository($app, $collection);
        }
    }
