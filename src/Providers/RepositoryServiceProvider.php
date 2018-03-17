<?php

namespace Masterkey\Repository\Providers;

use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Masterkey\Repository\Cache\CacheKeyStorage;
use Masterkey\Repository\Console\Commands\Creators\RepositoryCreator;
use Masterkey\Repository\Console\Commands\Creators\CriteriaCreator;
use Masterkey\Repository\Console\Commands\Creators\ValidatorCreator;
use Masterkey\Repository\Console\Commands\MakeCriteriaCommand;
use Masterkey\Repository\Console\Commands\MakeRepositoryCommand;
use Masterkey\Repository\Console\Commands\MakeValidatorCommand;

/**
 * RepositoryServiceProvider
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 4.0.1
 * @since   17/03/2018
 * @package Masterkey\Repository\Providers
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @return  void
     */
    public function boot()
    {
        $config_path = __DIR__  . '/../../config/repository.php';

        $this->publishes([$config_path => config_path('repository.php')], 'repositories');
    }

    /**
     * @return  void
     */
    public function register()
    {
        $this->registerBindings();

        $this->registerMakeRepositoryCommand();
        $this->registerMakeCriteriaCommand();
        $this->registerMakeValidatorCommand();

        $this->commands([
            MakeRepositoryCommand::class,
            MakeCriteriaCommand::class,
            MakeValidatorCommand::class
        ]);

        $config_path = __DIR__ . '/../../config/repository.php';

        $this->mergeConfigFrom($config_path, 'repositories');
    }

    /**
     * @return  void
     */
    protected function registerBindings()
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

        $this->app->singleton('ValidatorCreator', function($app) {
            return new ValidatorCreator($app['Filesystem']);
        });
    }

    protected function registerMakeRepositoryCommand()
    {
        $this->app['command.repository.make'] = $this->app->singleton(MakeRepositoryCommand::class, function($app) {
            return new MakeRepositoryCommand($app['RepositoryCreator'], $app['Composer']);
        });
    }

    protected function registerMakeCriteriaCommand()
    {
        $this->app['command.criteria.make'] = $this->app->singleton(MakeCriteriaCommand::class, function($app) {
            return new MakeCriteriaCommand($app['CriteriaCreator'], $app['Composer']);
        });
    }

    protected function registerMakeValidatorCommand()
    {
        $this->app['command.validator.make'] = $this->app->singleton(MakeValidatorCommand::class, function($app) {
            return new MakeValidatorCommand($app['ValidatorCreator'], $app['Composer']);
        });
    }

    /**
     * @return  array
     */
    public function provides()
    {
        return [
            'command.repository.make',
            'command.criteria.make',
            'command.validator.make'
        ];
    }
}
