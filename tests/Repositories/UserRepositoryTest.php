<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Masterkey\Tests\Models\InactiveUsers;
use Masterkey\Tests\Models\User;
use Masterkey\Tests\Models\UserRepository;
use Masterkey\Tests\Models\ActiveUsers;
use PHPUnit\Framework\TestCase;

use Masterkey\Repository\Criteria\RequestCriteria;
use Symfony\Component\HttpFoundation\Request as BaseRequest;
use Illuminate\Http\Request;

class UserRepositoryTest extends TestCase
{
    protected $user;

    /**
     * @throws  RepositoryException
     */
    public function __construct()
    {
        global $app;

        $this->user = new UserRepository($app);

        parent::__construct();
    }

    public function testInstanceOfUser()
    {
        $user = new User();

        $this->assertInstanceOf(Model::class, $user);
    }

    public function testAll()
    {
        $all        = $this->user->all();
        $received   = $all->toArray();

        $this->assertCount(2, $received);
        $this->assertInstanceOf(Collection::class, $all);
    }

    public function testPaginate()
    {
        $all    = $this->user->paginate(1);
        $count  = $all->toArray();

        $this->assertCount(1, $count['data']);
        $this->assertInstanceOf(LengthAwarePaginator::class, $all);
    }

    public function testSimplePaginate()
    {
        $all    = $this->user->simplePaginate(1);
        $count  = $all->toArray();

        $this->assertCount(1, $count['data']);
        $this->assertInstanceOf(Paginator::class, $all);
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

    /**
     * @throws RepositoryException
     */
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

    /**
     * @throws  RepositoryException
     */
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

    /**
     * @throws RepositoryException
     */
    public function testMassInsert()
    {
        $user = $this->user->insert([
            ['name' => 'Maria', 'active' => false, 'logins' => 5],
            ['name' => 'Sharon', 'active' => false, 'logins' => 3]
        ]);

        $this->assertEquals(true, $user);
    }

    /**
     * @throws RepositoryException
     */
    public function testUpdate()
    {
        $this->user->update(1, ['name' => 'Jonas Dawson']);

        $user = $this->user->find(1);

        $this->assertEquals('Jonas Dawson', $user->name);
    }

    /**
     * @throws              RepositoryException
     * @expectedException   \Illuminate\Database\Eloquent\ModelNotFoundException
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
        $all    = $this->user->count();
        $active = $this->user->pushCriteria(new ActiveUsers())->count();

        $this->assertEquals(5, $all);
        $this->assertEquals(3, $active);
    }

    public function testGetByCriteria()
    {
        $users = $this->user->getByCriteria(new ActiveUsers);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $users);
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

        $this->assertInstanceOf(Builder::class, $builder);
    }

    public function testRequestCriteria()
    {
        $symfonyRequest = new BaseRequest(['search' => 'Jonas', 'searchFields' => 'name:like']);
        $request = Request::createFromBase($symfonyRequest);

        $this->user->pushCriteria(new RequestCriteria($request));

        $users = $this->user->all();

        $this->assertEquals(1, $users->count());
    }

    /**
     * @expectedException   RepositoryException
     */
    public function testFailedRequestCriteria()
    {
        $symfonyRequest = new BaseRequest(['search' => '1', 'searchFields' => 'id']);
        $request = Request::createFromBase($symfonyRequest);

        $this->user->pushCriteria(new RequestCriteria($request));

        $this->user->all();
    }

    public function testLimit()
    {
        $users = $this->user->limit(2)->all();

        $this->assertEquals(2, $users->count());
    }

    public function testHaving()
    {
        $users = $this->user->having('logins' , '=', 2)->all();

        $this->assertEquals(1, $users->count());
    }

    public function testOrderBy()
    {
        $users  = $this->user->orderBy('id', 'desc')->all();
        $last   = $users->first();

        $this->assertEquals(6, $last->id);
    }

    public function testTransaction()
    {
        $user = $this->user->transaction(function()
        {
            return $this->user->create(['name' => 'Marcos', 'active' => false, 'logins' => 3]);
        });

        $this->assertInstanceOf(User::class, $user);
    }

    public function testMassUpdateWithCriteria()
    {
        $countActive    = $this->user->pushCriteria(new ActiveUsers)->count();
        $affectedRows   = $this->user->massUpdate(['active' => 0]);

        $this->assertEquals($countActive, $affectedRows);
    }

    public function testMassUpdate()
    {
        $countActive    = $this->user->pushCriteria(new ActiveUsers)->count();
        $affectedRows   = $this->user->massUpdate(['active' => '0']);

        $countInactive  = $this->user->pushCriteria(new InactiveUsers)->count();

        $this->assertEquals($countActive, $affectedRows);
        $this->assertEquals($this->user->count(), $countInactive);
    }

    public function testQueryLog()
    {
        $this->user->enableQueryLog();

        $this->user->all();

        $queryLog = $this->user->getQueryLog();
        $lastQuery = $this->user->getLastQuery();

        $this->user->disableQueryLog();

        $this->assertArrayHasKey('query', $queryLog[0]);
        $this->assertArrayHasKey('bindings', $queryLog[0]);
        $this->assertEquals('select * from "users"', $lastQuery);
    }

    public function testLastQueryWithCriteria()
    {
        $sql = 'select count(*) as aggregate from "users" where "active" = 1';

        $this->user->enableQueryLog();

        $this->user->pushCriteria(new ActiveUsers)->count();

        $lastQuery = $this->user->getLastQuery();

        $this->user->disableQueryLog();

        $this->assertEquals($sql, $lastQuery);
    }
}
