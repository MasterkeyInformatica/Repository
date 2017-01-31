<?php

    namespace Masterkey\Repository\Providers;

    use Illuminate\Support\Composer;
    use Illuminate\Filesystem\Filesystem;
    use Illuminate\Support\ServiceProvider;

    use Masterkey\Repository\Console\Commands\MakeCriteriaCommand;
    use Masterkey\Repository\Console\Commands\MakeRepositoryCommand;
    use Masterkey\Repository\Console\Commands\Creators\CriteriaCreator;
    use Masterkey\Repository\Console\Commands\Creators\RepositoryCreator;

    class RepositoryServiceProvider extends ServiceProvider
    {
        protected $defer = true;

        public function boot()
        {
            $config_path = __DIR__  . '/../../config/repository.php';

            $this->publishes(
                [$config_path => config_path('repository.php')],
                'repositories'
            );
        }

        public function register()
        {
            // Register bindings.
            $this->registerBindings();

            // Register make repository command.
            $this->registerMakeRepositoryCommand();

            // Register make criteria command.
            $this->registerMakeCriteriaCommand();

            // Register commands
            $this->commands(['command.repository.make', 'command.criteria.make']);

            // Config path.
            $config_path = __DIR__ . '/../../config/repository.php';

            // Merge config.
            $this->mergeConfigFrom(
                $config_path,
                'repositories'
            );
        }

        protected function registerBindings()
        {
            // FileSystem.
            $this->app->instance('FileSystem', new Filesystem());

            // Composer.
            $this->app->bind('Composer', function ($app) {
                return new Composer($app['FileSystem']);
            });

            // Repository creator.
            $this->app->singleton('RepositoryCreator', function ($app) {
                return new RepositoryCreator($app['FileSystem']);
            });

            // Criteria creator.
            $this->app->singleton('CriteriaCreator', function ($app) {
                return new CriteriaCreator($app['FileSystem']);
            });
        }

        protected function registerMakeRepositoryCommand()
        {
            // Make repository command.
            $this->app['command.repository.make'] = $this->app->share(function($app) {
                return new MakeRepositoryCommand($app['RepositoryCreator'], $app['Composer']);
            });
        }

        protected function registerMakeCriteriaCommand()
        {
            // Make criteria command.
            $this->app['command.criteria.make'] = $this->app->share(function($app) {
                return new MakeCriteriaCommand($app['CriteriaCreator'], $app['Composer']);
            });
        }

        public function provides()
        {
            return [
                'command.repository.make',
                'command.criteria.make'
            ];
        }
    }
