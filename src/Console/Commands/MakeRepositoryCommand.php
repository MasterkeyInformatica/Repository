<?php

    namespace Masterkey\Repository\Console\Commands;

    use Illuminate\Console\Command;
    use Symfony\Component\Console\Input\InputOption;
    use Symfony\Component\Console\Input\InputArgument;
    use Masterkey\Repository\Console\Commands\Creators\RepositoryCreator;

    /**
     * MakeRepositoryCommand
     *
     * Define commands to create a new repository class
     *
     * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
     * @version 1.0.1
     * @since   29/12/2016
     * @package Masterkey\Repository\Console\Commands
     */
    class MakeRepositoryCommand extends Command
    {
        /**
         * Command's name
         *
         * @var string
         */
        protected $name = 'make:repository';

        /**
         * Command's description
         *
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
         * Class Constructor
         *
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
         * Write a new repository class
         *
         * @param   array  $arguments
         * @param   array  $options
         */
        protected function writeRepository($arguments, $options)
        {
            $repository = $arguments['repository'];
            $model      = $options['model'];

            if($this->creator->create($repository, $model)) {
                $this->info("Successfully created the repository class");
            }
        }

        /**
         * Return the console's args
         *
         * @return  array
         */
        protected function getArguments()
        {
            return [
                ['repository', InputArgument::REQUIRED, 'The repository name.']
            ];
        }

        /**
         * Return the console's options
         *
         * @return  array
         */
        protected function getOptions()
        {
            return [
                ['model', null, InputOption::VALUE_OPTIONAL, 'The model name.', null],
            ];
        }
    }
