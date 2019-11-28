<?php

namespace Masterkey\Repository;

use Illuminate\Support\Collection;
use League\Fractal\Manager;
use League\Fractal\Resource\ResourceInterface;

/**
 * AbstractPresenter
 *
 * @author Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository
 */
class AbstractPresenter
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param ResourceInterface $resource
     * @return array
     */
    public function toArray(ResourceInterface $resource)
    {
        return $this->manager->createData($resource)->toArray()['data'];
    }

    /**
     * @param ResourceInterface $resource
     * @return \Illuminate\Support\Collection
     */
    public function toCollection(ResourceInterface $resource) : Collection
    {
        return collect($this->toArray($resource));
    }
}