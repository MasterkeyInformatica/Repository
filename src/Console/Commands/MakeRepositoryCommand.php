<?php

namespace Masterkey\Repository\Console\Commands;

use Illuminate\Support\Composer;
use Illuminate\Console\Command;
use Masterkey\Repository\Console\Commands\Creators\RepositoryCreator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * MakeRepositoryCommand
 *
 * Define commands to create a new repository class
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 3.0.0
 * @package Masterkey\Repository\Console\Commands
 */
class MakeRepositoryCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * @var RepositoryCreator
     */
    protected $creator;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @param   RepositoryCreator  $creator
     */
    public function __construct(RepositoryCreator $creator)
    {
        parent::__construct();

        $this->creator  = $creator;
        $this->composer = app()['composer'];
    }

    /**
     * Execute the console command
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->argument();
        $options   = $this->option();

        $this->writeRepository($arguments, $options);

        $this->composer->dumpAutoloads();
    }

    /**
     * @param   array  $arguments
     * @param   array  $options
     */
    protected function writeRepository($arguments, $options)
    {
        $repository = $arguments['repository'];
        $model      = $options['model'];

        if ( $this->creator->create($repository, $model) ) {
            $this->info("Successfully created the repository class");
        }
    }

    /**
     * @return  array
     */
    protected function getArguments()
    {
        return [
            ['repository', InputArgument::REQUIRED, 'The repository name.']
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
