<?php

namespace Masterkey\Tests\Presenters;

use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Masterkey\Repository\AbstractPresenter;
use Masterkey\Repository\AbstractTransformer;
use Masterkey\Tests\Models\User;
use Masterkey\Tests\Models\UserRepository;
use Masterkey\Tests\Models\UserTransform;
use PHPUnit\Framework\TestCase;

class PresenterTest extends TestCase
{
    protected $app;

    public function setUp() : void
    {
        global $app;

        parent::setUp();

        $this->app = $app;
    }

    protected function getPresenter() : AbstractPresenter
    {
        return $this->app->make(AbstractPresenter::class);
    }

    protected function getUsers() : UserRepository
    {
        return $this->app->make(UserRepository::class);
    }

    public function testInstance()
    {
        $manager = $this->getPresenter();

        $this->assertInstanceOf(AbstractPresenter::class, $manager);
    }

    public function testToArray()
    {
        $manager = $this->getPresenter();
        $users   = $this->getUsers();

        $resource = new Collection($users->all(), function(User $user)
        {
            return [
                'nome'          => $user->name,
                'ativo'         => (bool) $user->active,
                'tentativas'    => $user->logins
            ];
        });

        $this->assertInternalType('array', $manager->toArray($resource));
    }

    public function testCollection()
    {
        $manager = $this->getPresenter();
        $users   = $this->getUsers();

        $resource = new Collection($users->all(), function(User $user)
        {
            return [
                'nome'          => $user->name,
                'ativo'         => (bool) $user->active,
                'tentativas'    => $user->logins
            ];
        });

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $manager->toCollection($resource));
    }

    public function testArrayStructure()
    {
        $manager = $this->getPresenter();
        $users   = $this->getUsers();

        $resource = new Collection($users->all(), function(User $user)
        {
            return [
                'nome'          => $user->name,
                'ativo'         => (bool) $user->active,
                'tentativas'    => $user->logins
            ];
        });

        $data = $manager->toArray($resource);

        $this->assertArrayHasKey('nome', $data[0]);
        $this->assertArrayHasKey('ativo', $data[0]);
        $this->assertArrayHasKey('tentativas', $data[0]);
        $this->assertArrayNotHasKey('data', $data);
    }

    public function testEmptyArray()
    {
        $manager = $this->getPresenter();

        $resource = new Collection([], function(User $user)
        {
            return [];
        });

        $data = $manager->toArray($resource);

        $this->assertEmpty($data);
    }

    public function testItem()
    {
        $manager = $this->getPresenter();
        $users   = $this->getUsers();

        $user = $users->first();

        $resource = new Item($user, function(User $user)
        {
            return [
                'nome'          => strtoupper($user->name),
                'ativo'         => (bool) $user->active,
                'tentativas'    => $user->logins
            ];
        });

        $data = $manager->toArray($resource);

        $this->assertEquals(strtoupper($user->name), $data['nome']);
        $this->assertArrayHasKey('nome', $data);
        $this->assertArrayHasKey('ativo', $data);
        $this->assertArrayHasKey('tentativas', $data);
        $this->assertArrayNotHasKey('data', $data);
    }

    public function testEmptyItem()
    {
        $this->expectException(\TypeError::class);

        $manager = $this->getPresenter();

        $resource = new Item(null, function(User $user)
        {
            return [
                'nome'          => strtoupper($user->name),
                'ativo'         => (bool) $user->active,
                'tentativas'    => $user->logins
            ];
        });

        $data = $manager->toArray($resource);
    }

    public function testArrayEmpty()
    {
        $manager = $this->getPresenter();

        $resource = new Item(null, function($user)
        {
            if ( empty($user) ) {
                return [
                    'nome' => null,
                    'ativo' => null,
                    'tentativas' => null
                ];
            }

            return [
                'nome' => strtoupper($user['name']),
                'ativo' => (bool) $user['active'],
                'tentativas' => $user['logins']
            ];
        });

        $data = $manager->toArray($resource);

        $this->assertNull($data['tentativas']);
        $this->assertNull($data['ativo']);
    }

    public function testTransformUse()
    {
        $manager        = $this->getPresenter();
        $users          = $this->getUsers();
        $transformer    = new UserTransform;

        $resource = new Item($users->first(), $transformer);

        $data = $manager->toArray($resource);

        $this->assertInstanceOf(AbstractTransformer::class, $transformer);

        $this->assertArrayHasKey('nome', $data);
        $this->assertArrayHasKey('ativo', $data);
        $this->assertArrayHasKey('tentativas', $data);
        $this->assertArrayNotHasKey('data', $data);
    }
}