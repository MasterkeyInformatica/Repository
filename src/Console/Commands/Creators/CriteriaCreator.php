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
 * @package Masterkey\Repository\Console\Commands\Creators
 */
class CriteriaCreator
{
    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var string
     */
    protected $criteria;

    /**
     * @var string
     */
    protected $model;

    /**
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * @return string
     */
    public function getCriteria() : string
    {
        return $this->criteria;
    }

    /**
     * @param string $criteria
     * @return $this
     */
    public function setCriteria(string $criteria)
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * @return string
     */
    public function getModel() : string
    {
        return $this->model;
    }

    /**
     * @param string $model
     * @return $this
     */
    public function setModel(string $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param string $criteria
     * @param string $model
     * @return bool
     */
    public function create(string $criteria, string $model) : bool
    {
        return $this->setCriteria($criteria)
                    ->setModel($model)
                    ->createDirectory()
                    ->createClass();
    }

    /**
     * @return $this
     */
    public function createDirectory()
    {
        $directory = $this->getDirectory();

        if ( ! $this->files->isDirectory($directory) ) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDirectory() : string
    {
        $model      = $this->getModel();
        $directory  = Config::get('repository.criteria_path');

        if ( isset($model) && ! empty($model) ) {
            $directory .= DIRECTORY_SEPARATOR . $this->pluralizeModel();
        }

        return $directory;
    }

    /**
     * @return array
     */
    protected function getPopulateData() : array
    {
        $criteria   =  $this->getCriteria();
        $model      = $this->pluralizeModel();

        $criteria_namespace = Config::get('repository.criteria_namespace');
        $criteria_class     = $criteria;

        if ( isset($model) && ! empty($model) ) {
            $criteria_namespace .= '\\' . str_replace('/', '\\', $model);
        }

        return [
            'criteria_namespace' => $criteria_namespace,
            'criteria_class'     => $criteria_class
        ];
    }

    /**
     * @return string
     */
    protected function getPath() : string
    {
        return $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getCriteria() . '.php';
    }

    /**
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getStub() : string
    {
        return $this->files->get($this->getStubPath() . "criteria.stub");
    }

    /**
     * @return string
     */
    protected function getStubPath() : string
    {
        return __DIR__ . '/../../../../resources/stubs/';
    }

    /**
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function populateStub() : string
    {
        $populate_data  = $this->getPopulateData();
        $stub           = $this->getStub();

        foreach ( $populate_data as $search => $replace ) {
            $stub = str_replace($search, $replace, $stub);
        }

        return $stub;
    }

    /**
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function createClass() : bool
    {
        return $this->files->put($this->getPath(), $this->populateStub());
    }

    /**
     * @return string
     */
    protected function pluralizeModel() : string
    {
        $pluralized = Inflector::pluralize($this->getModel());

        return ucfirst($pluralized);
    }
}
