<?php

namespace Masterkey\Repository\Console\Commands\Creators;

use Doctrine\Common\Inflector\Inflector;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;

/**
 * Class RepositoryCreator
 *
 * Realiza a criação da classe repositório;
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 3.0.0
 * @package Masterkey\Repository\Console\Commands\Creators
 */
class RepositoryCreator
{
    use NamespaceTrait;

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var string
     */
    protected $repository;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var string
     */
    protected $repositoryNamespace;

    /**
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * @param string $namespace
     * @return $this
     */
    public function setRepositoryNamespace(string $namespace)
    {
        $this->repositoryNamespace = $namespace;

        return $this;
    }

    /**
     * @return string
     */
    public function getRepositoryNamespace() : string
    {
        return $this->repositoryNamespace;
    }

    /**
     * @return string
     */
    public function getRepository() : string
    {
        return $this->repository;
    }

    /**
     * @param string $repository
     * @return $this
     */
    public function setRepository(string  $repository)
    {
        $this->repository = $repository;

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
     * @param $repository
     * @param $model
     * @return bool
     */
    public function create($repository, $model) : bool
    {
        $repository = $this->getNamespaceOf($repository);
        $model      = $this->getNamespaceOf($model);

        return $this->setRepositoryNamespace($repository->namespace)
                    ->setRepository($repository->className)
                    ->setModelNamespace($model->namespace)
                    ->setModel($model->className)
                    ->createDirectory()
                    ->createClass();
    }

    /**
     * @return $this
     */
    protected function createDirectory()
    {
        $directory = $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getRepositoryNamespace();

        if ( ! $this->files->isDirectory($directory) ) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        return $this;
    }

    /**
     * @return string
     */
    protected function getDirectory() : string
    {
        return Config::get('repository.repository_path');
    }

    /**
     * @return string
     */
    protected function getRepositoryName() : string
    {
        $repository_name = $this->getRepository();

        if ( ! strpos($repository_name, 'Repository') !== false ) {
            $repository_name .= 'Repository';
        }

        return $repository_name;
    }

    /**
     * @return string
     */
    protected function getModelName() : string
    {
        $model = $this->getModel();

        if ( isset($model) && !empty($model) ) {
            $model_name = $model;
        } else {
            $model_name = Inflector::singularize($this->stripRepositoryName());
        }

        return $model_name;
    }

    /**
     * @return string
     */
    protected function stripRepositoryName() : string
    {
        $repository = strtolower($this->getRepository());
        $stripped   = str_replace("repository", "", $repository);

        return ucfirst($stripped);
    }

    /**
     * @return array
     */
    protected function getPopulateData() : array
    {
        $repository_namespace   = Config::get('repository.repository_namespace');
        $repository_class       = $this->getRepositoryName();
        $model_path             = Config::get('repository.model_namespace');
        $model_name             = $this->getModelName();

        if ( $this->getRepositoryNamespace() != '' ) {
            $repository_namespace .= '\\' . str_replace('/', '\\', $this->getRepositoryNamespace());
        }

        if ( $this->getModelNamespace() !== '' ) {
            $model_path .= '\\' . str_replace('/', '\\', $this->getModelNamespace());
        }

        return [
            'repository_namespace' => $repository_namespace,
            'repository_class'     => $repository_class,
            'model_path'           => $model_path,
            'model_name'           => $model_name
        ];
    }

    /**
     * @return string
     */
    protected function getPath() : string
    {
        $directory = $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getRepositoryNamespace();

        return $directory . DIRECTORY_SEPARATOR . $this->getRepositoryName() . '.php';
    }

    /**
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getStub() : string
    {
        return $this->files->get($this->getStubPath() . "repository.stub");
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

        foreach ( $populate_data as $key => $value ) {
            $stub = str_replace($key, $value, $stub);
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
}
