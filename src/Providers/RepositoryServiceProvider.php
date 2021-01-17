<?php

namespace Masterkey\Repository\Providers;

use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Masterkey\Repository\Cache\CacheKeyStorage;
use Masterkey\Repository\Console\Commands\{Creators\RepositoryCreator,
    Creators\CriteriaCreator,
    MakeCriteriaCommand,
    MakeRepositoryCommand};

/**
 * RepositoryServiceProvider
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 5.0.0
 * @package Masterkey\Repository\Providers
 */
class RepositoryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $config_path = __DIR__  . '/../../config/repository.php';

        $this->publishes([$config_path => config_path('repository.php')], 'repositories');
    }

    public function register(): void
    {
        $this->app->register(EventsServiceProvider::class);

        $this->registerBindings();

        $this->registerMakeRepositoryCommand();
        $this->registerMakeCriteriaCommand();

        $this->commands([
            MakeRepositoryCommand::class,
            MakeCriteriaCommand::class
        ]);

        $config_path = __DIR__ . '/../../config/repository.php';

        $this->mergeConfigFrom($config_path, 'repositories');
    }

    protected function registerBindings(): void
    {
        $this->app->singleton(CacheKeyStorage::class, function () {
            return new CacheKeyStorage(storage_path('framework/cache'));
        });

        $this->app->instance('Filesystem', new Filesystem());

        $this->app->bind('Composer', function ($app) {
            return new Composer($app['Filesystem']);
        });

        $this->app->singleton('RepositoryCreator', function ($app) {
            return new RepositoryCreator($app['Filesystem']);
        });

        $this->app->singleton('CriteriaCreator', function ($app) {
            return new CriteriaCreator($app['Filesystem']);
        });
    }

    protected function registerMakeRepositoryCommand(): void
    {
        $this->app->singleton(MakeRepositoryCommand::class, function($app) {
            return new MakeRepositoryCommand($app['RepositoryCreator']);
        });
    }

    protected function registerMakeCriteriaCommand()
    {
        $this->app->singleton(MakeCriteriaCommand::class, function($app) {
            return new MakeCriteriaCommand($app['CriteriaCreator']);
        });
    }

    public function provides(): array
    {
        return [
            MakeRepositoryCommand::class,
            MakeCriteriaCommand::class
        ];
    }
}
