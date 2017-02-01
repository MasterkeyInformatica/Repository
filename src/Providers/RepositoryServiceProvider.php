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
        /**
         * @var bool
         */
        protected $defer = true;

        /**
         * Realiza o boot após os registros
         *
         * @return  void
         */
        public function boot()
        {
            $config_path = __DIR__  . '/../../config/repository.php';

            $this->publishes([$config_path => config_path('repository.php')], 'repositories');
        }

        /**
         * Registra os serviços providos pelo Package
         */
        public function register()
        {
            $this->registerBindings();

            $this->registerMakeRepositoryCommand();

            $this->registerMakeCriteriaCommand();

            $this->commands(['command.repository.make', 'command.criteria.make']);

            $config_path = __DIR__ . '/../../config/repository.php';

            $this->mergeConfigFrom($config_path, 'repositories');
        }

        /**
         * Realiza o registro dos Bindings
         *
         * @return  void
         */
        protected function registerBindings()
        {
            $this->app->instance(Filesystem::class, new Filesystem());

            $this->app->bind(Composer::class, function ($app) {
                return new Composer($app['FileSystem']);
            });

            $this->app->singleton(RepositoryCreator::class, function ($app) {
                return new RepositoryCreator($app['FileSystem']);
            });

            $this->app->singleton(CriteriaCreator::class, function ($app) {
                return new CriteriaCreator($app['FileSystem']);
            });
        }

        /**
         * Registra o comando de criação de repositórios
         *
         * @return  void
         */
        protected function registerMakeRepositoryCommand()
        {
            $this->app['command.repository.make'] = $this->app->singleton(MakeRepositoryCommand::class, function($app) {
                return new MakeRepositoryCommand($app['RepositoryCreator'], $app['Composer']);
            });
        }

        /**
         * Registra o comando de criação de criterias
         *
         * @return  void
         */
        protected function registerMakeCriteriaCommand()
        {
            $this->app['command.criteria.make'] = $this->app->singleton(MakeCriteriaCommand::class, function($app) {
                return new MakeCriteriaCommand($app['CriteriaCreator'], $app['Composer']);
            });
        }

        /**
         * Retorna os serviços providos
         *
         * @return  array
         */
        public function provides()
        {
            return [
                'command.repository.make',
                'command.criteria.make'
            ];
        }
    }
