<?php

    namespace Masterkey\Repository\Console\Commands\Creators;

    use Illuminate\Support\Facades\Config;
    use stdClass;

    /**
     * NamespaceTrait
     *
     * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
     * @version 1.1.0
     * @since   30/01/2016
     * @package Masterkey\Repository\Console\Commands\Creators
     */
    trait NamespaceTrait
    {
        /**
         * @var string
         */
        protected $modelNamespace;

        /**
         * @param   string  $namespace
         * @return  $this
         */
        public function setModelNamespace($namespace)
        {
            $this->modelNamespace = $namespace;
            return $this;
        }

        /**
         * @return  string
         */
        public function getModelNamespace()
        {
            return $this->modelNamespace;
        }

        /**
         * Returns the namespace from string
         *
         * @param   string  $archive
         * @return  array
         */
        public function getNamespaceOf($archive)
        {
            $spaces     = explode('/', $archive);
            $className  = array_pop($spaces);

            $archive = new stdClass;
            $archive->namespace = implode('/', $spaces);
            $archive->className = $className;

            return $archive;
        }
    }
