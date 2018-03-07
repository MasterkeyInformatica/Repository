<?php

namespace Masterkey\Repository\Console\Commands;

use Illuminate\Support\Composer;
use Illuminate\Console\Command;
use Masterkey\Repository\Console\Commands\Creators\ValidatorCreator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * MakeValidatorCommand
 *
 * Define commands to create a new criteria class
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   07/03/2018
 * @package Masterkey\Repository\Console\Commands
 */
class MakeValidatorCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'make:validator';

    /**
     * @var string
     */
    protected $description = 'Create a new validator class';

    /**
     * @var ValidatorCreator
     */
    protected $creator;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @param   ValidatorCreator  $creator
     */
    public function __construct(ValidatorCreator $creator)
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

        $this->writeValidator($arguments, $options);
        $this->composer->dumpAutoloads();
    }

    /**
     * @param   array  $arguments
     * @param   array  $options
     */
    public function writeValidator($arguments, $options)
    {
         $validator = $arguments['validator'];

        if( $this->creator->create($validator) ) {
            $this->info("Succesfully created the validator class.");
        }
    }

    /**
     * @return  array
     */
    protected function getArguments()
    {
        return [
            ['validator', InputArgument::REQUIRED, 'The validator name.']
        ];
    }

    /**
     * @return  array
     */
    protected function getOptions()
    {
        return [

        ];
    }
}
