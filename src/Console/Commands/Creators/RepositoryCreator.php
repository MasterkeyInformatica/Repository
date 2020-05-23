<?php

namespace Masterkey\Repository\Console\Commands\Creators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

/**
 * Class RepositoryCreator
 *
 * Realiza a criação da classe repositório;
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 4.0.0
 * @package Masterkey\Repository\Console\Commands\Creators
 */
class RepositoryCreator
{
    use NamespaceTrait;

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

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function create(string $repository, string $model) : bool
    {
        $repository = $this->getNamespaceOf($repository);
        $model = $this->getNamespaceOf($model);

        return $this->setRepositoryNamespace($repository->namespace)
            ->setRepository($repository->className)
            ->setModelNamespace($model->namespace)
            ->setModel($model->className)
            ->createDirectory()
            ->createClass();
    }

    protected function createClass() : bool
    {
        return $this->files->put($this->getPath(), $this->populateStub());
    }

    protected function getPath() : string
    {
        $directory = $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getRepositoryNamespace();

        return $directory . DIRECTORY_SEPARATOR . $this->getRepositoryName() . '.php';
    }

    protected function getDirectory() : string
    {
        return Config::get('repository.repository_path');
    }

    public function getRepositoryNamespace() : string
    {
        return $this->repositoryNamespace;
    }

    public function setRepositoryNamespace(string $namespace) : RepositoryCreator
    {
        $this->repositoryNamespace = $namespace;

        return $this;
    }

    protected function getRepositoryName() : string
    {
        $repository_name = $this->getRepository();

        if ( ! strpos($repository_name, 'Repository') !== false ) {
            $repository_name .= 'Repository';
        }

        return $repository_name;
    }

    public function getRepository() : string
    {
        return $this->repository;
    }

    public function setRepository(string $repository) : RepositoryCreator
    {
        $this->repository = $repository;

        return $this;
    }

    protected function populateStub() : string
    {
        $populate_data = $this->getPopulateData();
        $stub = $this->getStub();

        foreach ( $populate_data as $key => $value ) {
            $stub = str_replace($key, $value, $stub);
        }

        return $stub;
    }

    protected function getPopulateData() : array
    {
        $repository_namespace = Config::get('repository.repository_namespace');
        $repository_class = $this->getRepositoryName();
        $model_path = Config::get('repository.model_namespace');
        $model_name = $this->getModelName();

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

    protected function getModelName() : string
    {
        $model = $this->getModel();

        if ( isset($model) && ! empty($model) ) {
            $model_name = $model;
        } else {
            $model_name = Str::singular($this->stripRepositoryName());
        }

        return $model_name;
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

    protected function stripRepositoryName() : string
    {
        $repository = strtolower($this->getRepository());
        $stripped = str_replace("repository", "", $repository);

        return ucfirst($stripped);
    }

    protected function getStub() : string
    {
        return $this->files->get($this->getStubPath() . "repository.stub");
    }

    protected function getStubPath() : string
    {
        return __DIR__ . '/../../../../resources/stubs/';
    }

    protected function createDirectory() : RepositoryCreator
    {
        $directory = $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getRepositoryNamespace();

        if ( ! $this->files->isDirectory($directory) ) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        return $this;
    }
}
