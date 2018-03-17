<?php

namespace Masterkey\Repository\Providers;

use Illuminate\Support\ServiceProvider;
use Masterkey\Repository\Events\EntityCreated;
use Masterkey\Repository\Events\EntityDeleted;
use Masterkey\Repository\Events\EntityUpdated;
use Masterkey\Repository\Listeners\ClearRepositoryCache;

/**
 * EventsServiceProvider
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   17/03/2018
 * @package Masterkey\Repository\Providers
 */
class EventsServiceProvider extends ServiceProvider
{
    protected $listen = [
        EntityCreated::class => [
            ClearRepositoryCache::class
        ],
        EntityUpdated::class => [
            ClearRepositoryCache::class
        ],
        EntityDeleted::class => [
            ClearRepositoryCache::class
        ]
    ];

    public function boot()
    {
        $events = app('events');

        foreach ( $this->listen as $event => $listeners ) {
            foreach ( $listeners as $listener ) {
                $events->listen($event, $listener);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function register()
    {

    }

    /**
     * @return array
     */
    public function listens()
    {
        return $this->listen;
    }
}