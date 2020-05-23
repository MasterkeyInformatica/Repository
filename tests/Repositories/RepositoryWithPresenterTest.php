<?php

namespace Masterkey\Tests\Repositories;

use Masterkey\Tests\Models\UserPresenter;
use Masterkey\Tests\Models\UserRepository;
use PHPUnit\Framework\TestCase;

class RepositoryWithPresenterTest extends TestCase
{
    public function testPresenterOnRepository()
    {
        global $app;

        $repo = new UserRepository($app);

        $reflection = new \ReflectionClass($repo);
        $presenter = $reflection->getProperty('presenter');
        $presenter->setAccessible(true);

        $this->assertInstanceOf(UserPresenter::class, $presenter->getValue($repo));
    }
}