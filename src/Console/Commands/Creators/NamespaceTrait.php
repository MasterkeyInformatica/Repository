<?php

namespace Masterkey\Repository\Console\Commands\Creators;

use Illuminate\Support\Facades\Config;
use stdClass;

/**
 * NamespaceTrait
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 3.0.0
 * @since   02/09/2017
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
    public function setModelNamespace(string $namespace)
    {
        $this->modelNamespace = $namespace;
        return $this;
    }

    /**
     * @return  string
     */
    public function getModelNamespace() : string
    {
        return $this->modelNamespace;
    }

    /**
     * Returns the namespace from string
     *
     * @param   string  $archive
     * @return  stdClass
     */
    public function getNamespaceOf($archive) : stdClass
    {
        $spaces     = explode('/', $archive);
        $className  = array_pop($spaces);

        $archive = new stdClass;
        $archive->namespace = implode('/', $spaces);
        $archive->className = $className;

        return $archive;
    }
}
