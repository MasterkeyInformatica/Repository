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

        $repository = new UserRepository($app);

        $this->assertAttributeInstanceOf(UserPresenter::class, 'presenter', $repository);
    }
}