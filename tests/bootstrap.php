<?php

    require_once __DIR__ . '/../vendor/autoload.php';

    use Illuminate\Config\Repository as Config;
    use Illuminate\Container\Container;
    use Illuminate\Database\Connection as DB;
    use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

    $config = require_once(__DIR__ . '/../config/repository.php');

    /*
     * Registra o container do Laravel para que os testes possam ser realizados
     * de forma bem sucedida
     */
    $app = new Container;
    Facade::setFacadeApplication($app);

    // Seta as configurações do repositório
    $app->singleton('config', function($app) use($config) {
        return new Config(['repository' => $config]);
    });

    $app->singleton('request', function() {
        return new Request();
    });

    $app->singleton(\Illuminate\Validation\Factory::class, function ($app) {
        return new \Illuminate\Validation\Factory(
            new \Illuminate\Translation\Translator(
                new \Illuminate\Translation\FileLoader(new \Illuminate\Filesystem\Filesystem(),''),
                'en'
            ),
            $app
        );
    });

    $capsule = new Capsule();
    $capsule->addConnection([
        'driver'    => 'sqlite',
        'database'  => ':memory:'
    ], 'sqlite');

    // Inicializa o Bootstrap e o disponibiliza de forma global
    $capsule->setFetchMode(\PDO::FETCH_OBJ);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    // realiza o gerenciamento do PDO
    $pdo    = $capsule->getConnection('sqlite')->getPdo();
    $db     = new DB($pdo, 'sqlite');

    $db->statement('CREATE TABLE if not exists users (id integer not null primary key AUTOINCREMENT, name varchar(20) not null, active bool not null, logins integer not null)');
    $db->table('users')->insert([
        ['name' => 'Jonas', 'active' => true, 'logins' => 10],
        ['name' => 'Matilda', 'active' => false, 'logins' => 5]
    ]);
