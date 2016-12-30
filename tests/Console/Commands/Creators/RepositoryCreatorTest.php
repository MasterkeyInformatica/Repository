<?php

    use Masterkey\Repository\Console\Commands\Creators\RepositoryCreator;
    use Illuminate\Filesystem\Filesystem;

    class RepositoryCreatorTest extends PHPUnit_Framework_TestCase
    {
        public function testCreate()
        {
            $fileSystem         = new Filesystem;
            $repositoryCreator  = new RepositoryCreator($fileSystem);

            $repositoryCreator->create('Staff/EmployeesRepository', 'Models/Employee');
            $repositoryCreator->create('Users', 'Models/Users');

            $this->assertInstanceOf(RepositoryCreator::class, $repositoryCreator);
            $this->assertFileExists(__DIR__ . '/../../../../app/Repositories/Staff/EmployeesRepository.php');
            $this->assertFileExists(__DIR__ . '/../../../../app/Repositories/UsersRepository.php');
        }
    }
