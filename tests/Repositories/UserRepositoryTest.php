<?php

use Masterkey\Tests\Models\User;
use Masterkey\Tests\Models\UserRepository;
use Masterkey\Tests\Models\ActiveUsers;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    protected $user;

    public function __construct()
    {
        global $app;
        global $collection;

        $this->user = new UserRepository($app, $collection);

        parent::__construct();
    }

    public function testInstanceOfUser()
    {
        $user = new User();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Model::class, $user);
    }

    public function testAll()
    {
        $all        = $this->user->all();
        $received   = $all->toArray();

        $this->assertCount(2, $received);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $all);
    }

    public function testPaginate()
    {
        $all    = $this->user->paginate(1);
        $count  = $all->toArray();

        $this->assertCount(1, $count['data']);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $all);
    }

    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testFind()
    {
        $user1 = $this->user->find(1);

        $this->assertEquals('Jonas', $user1->name);
        $this->assertInstanceOf(User::class, $user1);

        $user2 = $this->user->find(3);
    }

    public function testCreate()
    {
        $user = $this->user->create([
            'name'      => 'Garcia',
            'active'    => true,
            'logins'    => 2
        ]);

        $this->assertEquals(true, $user->exists);
        $this->assertInstanceOf(User::class, $user);
    }

    public function testSave()
    {
        $user = $this->user->save([
            'name'      => 'Penelope',
            'active'    => true,
            'logins'    => 8
        ]);

        $this->assertEquals(true, $user->exists);
        $this->assertInstanceOf(User::class, $user);
    }

    public function testMassInsert()
    {
        $user = $this->user->massInsert([
            ['name' => 'Maria', 'active' => false, 'logins' => 5],
            ['name' => 'Sharon', 'active' => false, 'logins' => 3]
        ]);

        $this->assertEquals(true, $user);
    }

    public function testUpdate()
    {
        $this->user->update(1, ['name' => 'Jonas Dawson']);

        $user = $this->user->find(1);

        $this->assertEquals('Jonas Dawson', $user->name);
    }

    /**
     * @expectedException  \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testDelete()
    {
        $this->user->delete(2);

        $this->user->find(2);
    }

    public function testCount()
    {
        $saved = $this->user->count();
        $this->assertEquals(5, $saved);
    }

    public function testMax()
    {
        $max = $this->user->max('logins');
        $this->assertEquals(10, $max);
    }

    public function testMin()
    {
        $min = $this->user->min('logins');
        $this->assertEquals(2, $min);
    }

    public function testAvg()
    {
        $avg = $this->user->avg('logins');
        $this->assertEquals(5.6, $avg);
    }

    public function testSum()
    {
        $sum = $this->user->sum('logins');
        $this->assertEquals(28, $sum);
    }

    public function testCriteria()
    {
        $all        = $this->user->count();
        $criteria   = new ActiveUsers();
        $active     = $this->user->getByCriteria($criteria)->count();

        $this->assertEquals(5, $all);
        $this->assertEquals(3, $active);
    }

    public function testFirst()
    {
        $user   = $this->user->find(1);
        $first  = $this->user->first();

        $this->assertEquals($user->name, $first->name);
    }

    public function testLast()
    {
        $user = $this->user->find(6);
        $last = $this->user->last();

        $this->assertEquals($user->name, $last->name);
    }

    public function testBuilder()
    {
        $builder = $this->user->getBuilder();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }
}
