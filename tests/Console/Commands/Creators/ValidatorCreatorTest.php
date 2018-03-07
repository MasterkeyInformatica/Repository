<?php

namespace Masterkey\Tests\Console\Commands\Creators;

use Illuminate\Filesystem\Filesystem;
use Masterkey\Repository\Console\Commands\Creators\ValidatorCreator;
use PHPUnit\Framework\TestCase;

class ValidatorCreatorTest extends TestCase
{
    public function testCreator()
    {
        $fs         = new Filesystem();
        $creator    = new ValidatorCreator($fs);

        $creator->create('UserValidator');
        $creator->create('PostValidator');

        $this->assertInstanceOf(ValidatorCreator::class, $creator);
        $this->assertFileExists(__DIR__ . '/../../../../app/Validators/UserValidator.php');
        $this->assertFileExists(__DIR__ . '/../../../../app/Validators/PostValidator.php');
    }
}
