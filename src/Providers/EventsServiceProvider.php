<?php

namespace Masterkey\Repository\Providers;

use Illuminate\Support\ServiceProvider;
use Masterkey\Repository\Events\{EntityCreated, EntityDeleted, EntityUpdated};
use Masterkey\Repository\Listeners\ClearRepositoryCache;

/**
 * EventsServiceProvider
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.0
 * @package Masterkey\Repository\Providers
 */
class EventsServiceProvider extends ServiceProvider
{
    protected array $listen = [
        EntityCreated::class => [
            ClearRepositoryCache::class,
        ],
        EntityUpdated::class => [
            ClearRepositoryCache::class,
        ],
        EntityDeleted::class => [
            ClearRepositoryCache::class,
        ],
    ];

    public function boot()
    {
        $events = $this->app->make('events');

        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->listen($event, $listener);
            }
        }
    }

    public function register(): void
    {
        //
    }

    public function listens(): array
    {
        return $this->listen;
    }
}