<?php

    require_once __DIR__ . '/../vendor/autoload.php';

    use Illuminate\Config\Repository as Config;
    use Illuminate\Container\Container;
    use Illuminate\Support\Facades\Facade;

    $config = require_once(__DIR__ . '/../config/repository.php');

    /*
     * Registra o container do Laravel para que os testes possam ser realizados
     * de forma bem sucedida
     */
    $app = new Container();
    Facade::setFacadeApplication($app);

    // Seta as configurações do repositório
    $app->singleton('config', function($app) use($config) {
        return new Config(['repository' => $config]);
    });
