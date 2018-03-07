<?php

namespace Masterkey\Repository\Console\Commands\Creators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Doctrine\Common\Inflector\Inflector;

/**
 * CriteriaCreator
 *
 * Realiza a criação da classe de Criteria
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 3.0.0
 * @since   02/09/2017
 * @package Masterkey\Repository\Console\Commands\Creators
 */
class ValidatorCreator {

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var string
     */
    protected $validator;

    /**
     * @param   Filesystem  $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * @return  string
     */
    public function getValidator() : string
    {
        return $this->validator;
    }

    /**
     * @param   string  $validator
     * @return  $this
     */
    public function setValidator(string $validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Create the validator.
     *
     * @param   string  $validator
     * @return  bool
     */
    public function create(string $validator) : bool
    {
        return $this->setValidator($validator)
                    ->createDirectory()
                    ->createClass();
    }


    /**
     * @return  $this
     */
    public function createDirectory()
    {
        $directory = $this->getDirectory();

        if(!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        return $this;
    }

    /**
     * @return  string
     */
    public function getDirectory() : string
    {
        return Config::get('repository.validator_path');
    }


    /**
     * @return  array
     */
    protected function getPopulateData() : array
    {
        $validator = $this->getValidator();

        $validator_namespace = Config::get('repository.validator_namespace');
        $validator_class     = $validator;

        return [
            'validator_namespace' => $validator_namespace,
            'validator_class'     => $validator_class
        ];
    }

    /**
     * @return  string
     */
    protected function getPath() : string
    {
        return $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getValidator() . '.php';
    }

    /**
     * @return  string
     * @throws  \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getStub() : string
    {
        return $this->files->get($this->getStubPath() . "validator.stub");
    }

    /**
     * @return  string
     */
    protected function getStubPath() : string
    {
        return __DIR__ . '/../../../../resources/stubs/';
    }

    /**
     * @return  string
     * @throws  \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function populateStub() : string
    {
        $populate_data  = $this->getPopulateData();
        $stub           = $this->getStub();

        foreach ($populate_data as $search => $replace) {
            $stub = str_replace($search, $replace, $stub);
        }

        return $stub;
    }

    /**
     * @return  bool
     */
    protected function createClass() : bool
    {
        return $this->files->put($this->getPath(), $this->populateStub());
    }
}
