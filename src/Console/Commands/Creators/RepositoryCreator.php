<?php

    namespace Masterkey\Repository\Console\Commands\Creators;

    use Illuminate\Filesystem\Filesystem;
    use Illuminate\Support\Facades\Config;
    use Doctrine\Common\Inflector\Inflector;

    /**
     * Class RepositoryCreator
     *
     * Realiza a criação da classe repositório;
     *
     * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
     * @version 1.1.0
     * @since   29/12/2016
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
         * @param   Filesystem  $files
         */
        public function __construct(Filesystem $files)
        {
            $this->files = $files;
        }

        /**
         * @param   string  $namespace
         */
        public function setRepositoryNamespace($namespace)
        {
            $this->repositoryNamespace = $namespace;
            return $this;
        }

        /**
         * @return  string
         */
        public function getRepositoryNamespace()
        {
            return $this->repositoryNamespace;
        }

        /**
         * @return  mixed
         */
        public function getRepository()
        {
            return $this->repository;
        }

        /**
         * @param   mixed  $repository
         * @return  $this
         */
        public function setRepository($repository)
        {
            $this->repository = $repository;
            return $this;
        }

        /**
         * @return mixed
         */
        public function getModel()
        {
            return $this->model;
        }

        /**
         * @param   mixed  $model
         * @return  $this
         */
        public function setModel($model)
        {
            $this->model = $model;
            return $this;
        }

        /**
         * Create the repository.
         *
         * @param   string  $repository
         * @param   string  $model
         * @return  bool
         */
        public function create($repository, $model)
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
         * Creates de directory form the repository
         *
         * @return  $this
         */
        protected function createDirectory()
        {
            $directory = $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getRepositoryNamespace();

            if(!$this->files->isDirectory($directory)) {
                $this->files->makeDirectory($directory, 0755, true);
            }

            return $this;
        }

        /**
         * Get the repository directory.
         *
         * @return mixed
         */
        protected function getDirectory()
        {
            return Config::get('repository.repository_path');
        }

        /**
         * Get the repository name.
         *
         * @return  mixed|string
         */
        protected function getRepositoryName()
        {
            $repository_name = $this->getRepository();

            if(!strpos($repository_name, 'Repository') !== false) {
                $repository_name .= 'Repository';
            }

            return $repository_name;
        }

        /**
         * Get the model name.
         *
         * @return string
         */
        protected function getModelName()
        {
            $model = $this->getModel();

            if(isset($model) && !empty($model)) {
                $model_name = $model;
            } else {
                $model_name = Inflector::singularize($this->stripRepositoryName());
            }

            // Return the model name.
            return $model_name;
        }

        /**
         * Get the stripped repository name.
         *
         * @return  string
         */
        protected function stripRepositoryName()
        {
            $repository = strtolower($this->getRepository());
            $stripped   = str_replace("repository", "", $repository);

            return ucfirst($stripped);
        }

        /**
         * Get the populate data.
         *
         * @return  array
         */
        protected function getPopulateData()
        {
            $repository_namespace   = Config::get('repository.repository_namespace');
            $repository_class       = $this->getRepositoryName();
            $model_path             = Config::get('repository.model_namespace');
            $model_name             = $this->getModelName();

            if($this->getRepositoryNamespace() != '') {
                $repository_namespace .= '\\' . str_replace('/', '\\', $this->getRepositoryNamespace());
            }

            if($this->getModelNamespace() !== '') {
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
         * Get the path.
         *
         * @return string
         */
        protected function getPath()
        {
            $directory = $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getRepositoryNamespace();
            return $directory . DIRECTORY_SEPARATOR . $this->getRepositoryName() . '.php';
        }

        /**
         * Get the stub file.
         *
         * @return  string
         * @throws  \Illuminate\Contracts\Filesystem\FileNotFoundException
         */
        protected function getStub()
        {
            return $this->files->get($this->getStubPath() . "repository.stub");
        }

        /**
         * Get the stub path.
         *
         * @return string
         */
        protected function getStubPath()
        {
            return __DIR__ . '/../../../../resources/stubs/';
        }

        /**
         * Populate the stub.
         *
         * @return mixed
         */
        protected function populateStub()
        {
            $populate_data  = $this->getPopulateData();
            $stub           = $this->getStub();

            foreach ($populate_data as $key => $value) {
                $stub = str_replace($key, $value, $stub);
            }

            return $stub;
        }

        /**
         * Create the new Class
         *
         * @return  mixed
         */
        protected function createClass()
        {
            return $this->files->put($this->getPath(), $this->populateStub());
        }
    }
