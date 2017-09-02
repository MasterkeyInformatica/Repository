<?php

use Illuminate\Filesystem\Filesystem;
use Masterkey\Repository\Console\Commands\Creators\RepositoryCreator;
use PHPUnit\Framework\TestCase;

class RepositoryCreatorTest extends TestCase
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
