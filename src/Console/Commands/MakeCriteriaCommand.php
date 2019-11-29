<?php

namespace Masterkey\Repository\Console\Commands;

use Illuminate\Support\Composer;
use Illuminate\Console\Command;
use Masterkey\Repository\Console\Commands\Creators\CriteriaCreator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * MakeCriteriaCommand
 *
 * Define commands to create a new criteria class
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 3.0.0
 * @since   02/09/2017
 * @package Masterkey\Repository\Console\Commands
 */
class MakeCriteriaCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'make:criteria';

    /**
     * @var string
     */
    protected $description = 'Create a new criteria class';

    /**
     * @var CriteriaCreator
     */
    protected $creator;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @param   CriteriaCreator  $creator
     */
    public function __construct(CriteriaCreator $creator)
    {
        parent::__construct();

        $this->creator  = $creator;
        $this->composer = app()['composer'];
    }

    /**
     * Execute the console command.
     *
     * @return  mixed
     */
    public function handle()
    {
        $arguments = $this->argument();
        $options   = $this->option();

        $this->writeCriteria($arguments, $options);
        $this->composer->dumpAutoloads();
    }

    /**
     * @param   array  $arguments
     * @param   array  $options
     */
    public function writeCriteria($arguments, $options)
    {
        $criteria = $arguments['criteria'];
        $model    = $options['model'];

        if ( $this->creator->create($criteria, $model) ) {
            $this->info("Succesfully created the criteria class.");
        }
    }

    /**
     * @return  array
     */
    protected function getArguments()
    {
        return [
            ['criteria', InputArgument::REQUIRED, 'The criteria name.']
        ];
    }

    /**
     * @return  array
     */
    protected function getOptions()
    {
        return [
            ['model', null, InputOption::VALUE_OPTIONAL, 'The model name.', null],
        ];
    }
}
