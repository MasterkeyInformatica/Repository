<?php

    namespace Masterkey\Repository\Providers;

    use Illuminate\Support\Composer;
    use Illuminate\Filesystem\Filesystem;
    use Illuminate\Support\ServiceProvider;

    use Masterkey\Repository\Console\Commands\MakeCriteriaCommand;
    use Masterkey\Repository\Console\Commands\MakeRepositoryCommand;
    use Masterkey\Repository\Console\Commands\Creators\CriteriaCreator;
    use Masterkey\Repository\Console\Commands\Creators\RepositoryCreator;

    /**
     * RepositoryServiceProvider
     *
     * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
     * @version 2.0.0
     * @since   31/01/2017
     * @package Masterkey\Repository\Providers
     */
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
         *
         * @return  void
         */
        public function register()
        {
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

        /**
         * Realiza o registro dos Bindings
         *
         * @return  void
         */
        protected function registerBindings()
        {
            $this->app->instance('Filesystem', new Filesystem());

            $this->app->bind('Composer', function ($app) {
                return new Composer($app['Filesystem']);
            });

            $this->app->bind(
                \Illuminate\Contracts\Container\Container::class,
                \Illuminate\Container\Container::class
            );

            $this->app->bind(
                \Illuminate\Contracts\Support\Arrayable::class,
                \Illuminate\Support\Collection::class
            );

            $this->app->singleton('RepositoryCreator', function ($app) {
                return new RepositoryCreator($app['Filesystem']);
            });

            $this->app->singleton('CriteriaCreator', function ($app) {
                return new CriteriaCreator($app['Filesystem']);
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
