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
     * @version 1.0.2
     * @since   29/12/2016
     * @package Masterkey\Repository\Console\Commands\Creators
     */
    class CriteriaCreator {

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
         * @param   Filesystem  $files
         */
        public function __construct(Filesystem $files)
        {
            $this->files = $files;
        }

        /**
         * @return  mixed
         */
        public function getCriteria()
        {
            return $this->criteria;
        }

        /**
         * @param   mixed  $criteria
         * @return  $this
         */
        public function setCriteria($criteria)
        {
            $this->criteria = $criteria;
            return $this;
        }

        /**
         * @return  mixed
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
         * Create the criteria.
         *
         * @param   string  $criteria
         * @param   string  $model
         * @return  bool
         */
        public function create($criteria, $model)
        {
            return $this->setCriteria($criteria)
                        ->setModel($model)
                        ->createDirectory()
                        ->createClass();
        }


        /**
         * Create the criteria directory.
         *
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
         * Get the criteria directory.
         *
         * @return  string
         */
        public function getDirectory()
        {
            $model      = $this->getModel();
            $directory  = Config::get('repository.criteria_path');

            if(isset($model) && !empty($model)) {
                $directory .= DIRECTORY_SEPARATOR . $this->pluralizeModel();
            }

            return $directory;
        }


        /**
         * Get the populate data.
         *
         * @return  array
         */
        protected function getPopulateData()
        {
            $criteria   =  $this->getCriteria();
            $model      = $this->pluralizeModel();

            $criteria_namespace = Config::get('repository.criteria_namespace');
            $criteria_class     = $criteria;

            if(isset($model) && !empty($model)) {
                $criteria_namespace .= '\\' . $model;
            }

            return [
                'criteria_namespace' => $criteria_namespace,
                'criteria_class'     => $criteria_class
            ];
        }

        /**
         * Get the path.
         *
         * @return  string
         */
        protected function getPath()
        {
            return $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getCriteria() . '.php';
        }

        /**
         * Get the stub.
         *
         * @return  string
         * @throws  \Illuminate\Contracts\Filesystem\FileNotFoundException
         */
        protected function getStub()
        {
            return $this->files->get($this->getStubPath() . "criteria.stub");
        }

        /**
         * Get the stub path.
         *
         * @return  string
         */
        protected function getStubPath()
        {
            return __DIR__ . '/../../../../resources/stubs/';
        }

        /**
         * Populate the stub.
         *
         * @return  mixed
         */
        protected function populateStub()
        {
            $populate_data  = $this->getPopulateData();
            $stub           = $this->getStub();

            foreach ($populate_data as $search => $replace) {
                $stub = str_replace($search, $replace, $stub);
            }

            return $stub;
        }

        /**
         * Create the repository class.
         *
         * @return  bool
         */
        protected function createClass()
        {
            return $this->files->put($this->getPath(), $this->populateStub());
        }

        /**
         * Pluralize the model.
         *
         * @return  string
         */
        protected function pluralizeModel()
        {
            $pluralized = Inflector::pluralize($this->getModel());

            return ucfirst($pluralized);
        }
    }
