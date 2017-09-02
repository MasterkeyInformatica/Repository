<?php

use Illuminate\Filesystem\Filesystem;
use Masterkey\Repository\Console\Commands\Creators\CriteriaCreator;
use PHPUnit\Framework\TestCase;

class CriteriaCreatorTest extends TestCase
{
    public function testCreate()
    {
        $filesystem         = new Filesystem;
        $criteriaCreator    = new CriteriaCreator($filesystem);

        $criteriaCreator->create('FilmsOverTwoHoursLength', 'Film');
        $criteriaCreator->create('MoviesNotRated', 'Movie');

        $this->assertInstanceOf(CriteriaCreator::class, $criteriaCreator);
        $this->assertFileExists(__DIR__ . '/../../../../app/Repositories/Criteria/Films/FilmsOverTwoHoursLength.php');
    }
}
