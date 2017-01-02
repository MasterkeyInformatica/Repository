<?php

    use Masterkey\Tests\Models\User;
    use Masterkey\Tests\Models\UserRepository;

    class UserRepositoryTest extends PHPUnit_Framework_TestCase
    {
        protected $user;

        public function __construct()
        {
            global $app;
            global $collection;

            $this->user = new UserRepository($app, $collection);
        }

        public function testInstanceOfUser()
        {
            $user = new User();
            $this->assertInstanceOf('Illuminate\Database\Eloquent\Model', $user);
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
            $this->assertInstanceOf('Illuminate\Pagination\LengthAwarePaginator', $all);
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
                'active'    => true
            ]);

            $this->assertEquals(true, $user->exists);
            $this->assertInstanceOf(User::class, $user);
        }

        public function testSave()
        {
            $user = $this->user->save([
                'name'      => 'Penelope',
                'active'    => true
            ]);

            $this->assertEquals(true, $user->exists);
            $this->assertInstanceOf(User::class, $user);
        }

        public function testMassInsert()
        {
            $user = $this->user->massInsert([
                ['name' => 'Maria', 'active' => true],
                ['name' => 'Sharon', 'active' => false]
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
            $this->user->delete(1);

            $this->user->find(1);
        }
    }
