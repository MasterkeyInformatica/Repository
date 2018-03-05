<?php

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

    public function testSimplePaginate()
    {
        $all    = $this->user->simplePaginate(1);
        $count  = $all->toArray();

        $this->assertCount(1, $count['data']);
        $this->assertInstanceOf(\Illuminate\Pagination\Paginator::class, $all);
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
     * @throws ModelNotSavedException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
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
     * @throws ModelNotSavedException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @expectedException   \Prettus\Validator\Exceptions\ValidatorException
     */
    public function testCreateValidationFail()
    {
        $user = $this->user->create([
            'active'    => true,
            'logins'    => 2
        ]);
    }

    /**
     * @throws ModelNotSavedException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
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
     * @throws ModelNotSavedException
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
     * @throws ModelNotSavedException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function testUpdate()
    {
        $this->user->update(1, ['name' => 'Jonas Dawson']);

        $user = $this->user->find(1);

        $this->assertEquals('Jonas Dawson', $user->name);
    }

    /**
     * @throws  ModelNotSavedException
     * @throws  \Prettus\Validator\Exceptions\ValidatorException
     * @expectedException \Prettus\Validator\Exceptions\ValidatorException
     */
    public function testUpdateValidation()
    {
        $this->user->update(1, ['name' => null]);
    }

    /**
     * @throws              ModelNotDeletedException
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
}
