<?php

namespace Masterkey\Repository\Console\Commands\Creators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

/**
 * CriteriaCreator
 *
 * Realiza a criação da classe de Criteria
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 4.0.0
 * @package Masterkey\Repository\Console\Commands\Creators
 */
class CriteriaCreator
{
    protected $files;

    /**
     * @var string
     */
    protected $criteria;

    /**
     * @var string
     */
    protected $model;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function create(string $criteria, string $model) : bool
    {
        return $this->setCriteria($criteria)
            ->setModel($model)
            ->createDirectory()
            ->createClass();
    }

    protected function createClass() : bool
    {
        return $this->files->put($this->getPath(), $this->populateStub());
    }

    protected function getPath() : string
    {
        return $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getCriteria() . '.php';
    }

    public function getDirectory() : string
    {
        $model = $this->getModel();
        $directory = Config::get('repository.criteria_path');

        if ( isset($model) && ! empty($model) ) {
            $directory .= DIRECTORY_SEPARATOR . $this->pluralizeModel();
        }

        return $directory;
    }

    public function getModel() : string
    {
        return $this->model;
    }

    public function setModel(string $model)
    {
        $this->model = $model;

        return $this;
    }

    protected function pluralizeModel() : string
    {
        return ucfirst(
            Str::plural($this->getModel())
        );
    }

    public function getCriteria() : string
    {
        return $this->criteria;
    }

    public function setCriteria(string $criteria) : CriteriaCreator
    {
        $this->criteria = $criteria;

        return $this;
    }

    protected function populateStub() : string
    {
        $populate_data = $this->getPopulateData();
        $stub = $this->getStub();

        foreach ( $populate_data as $search => $replace ) {
            $stub = str_replace($search, $replace, $stub);
        }

        return $stub;
    }

    protected function getPopulateData() : array
    {
        $criteria = $this->getCriteria();
        $model = $this->pluralizeModel();

        $criteria_namespace = Config::get('repository.criteria_namespace');
        $criteria_class = $criteria;

        if ( isset($model) && ! empty($model) ) {
            $criteria_namespace .= '\\' . str_replace('/', '\\', $model);
        }

        return [
            'criteria_namespace' => $criteria_namespace,
            'criteria_class'     => $criteria_class
        ];
    }

    protected function getStub() : string
    {
        return $this->files->get($this->getStubPath() . "criteria.stub");
    }

    protected function getStubPath() : string
    {
        return __DIR__ . '/../../../../resources/stubs/';
    }

    public function createDirectory() : CriteriaCreator
    {
        $directory = $this->getDirectory();

        if ( ! $this->files->isDirectory($directory) ) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        return $this;
    }
}
