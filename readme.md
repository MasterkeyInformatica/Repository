Masterkey Repository
====================

[![Build Status](https://travis-ci.org/MasterkeyInformatica/Repository.svg?branch=master)](https://travis-ci.org/MasterkeyInformatica/Repository)

Este projeto foi desenvolvido para que pudessemos ter uma camada de
abstração dos Models do Laravel, desacoplando a lógica dos Controllers.

Para utilização com o Laravel, utilize o composer:

```sh
$ composer require masterkey/repository
```
Lembre-se que, com o Laravel 5.5, não é necessario informar o Service Provider
no arquivo `config/app.php`

Para utilização com o Laravel 5.4, verifique o *branch* **2.0**

Feito isso, publique o arquivo de configuração do repositório:

```sh
$ php artisan vendor:publish
```

No arquivo de configuração você pode definir o local onde os repositories e criterias serão criados.

Criando Repositories
--------------------

Você pode Criar um repositório utilizando o artisan:
```sh
php artisan make:repository UsersRepository --model=Users
# ou ainda
php artisan make:repository Users/Users --model=Models/Users
```
para utilização:
```php
<?php

class MyController { 

    protected $user;
    
    public function __construct(\App\Repositories\UserRepository $user)
    {
        $this->user = $user;
    }
    
    public function index()
    {
        return $this->user->all(['column_a', 'column_b']);
    }
}
```

Utilizando Criterias
--------------------

Criterias podem ser utilizadas para adicionar uma query específica em uma busca, permitindo uma melhor reusabilidade com o sql. Para criar uma nova Criteria:

```sh
php artisan make:criteria MoviesNotRated --model=Movie
```
**Importante:** Nao é necessário passar o namespace completo do model. O nome do model é passado para que o package possa criar um diretório para que as Criterias dequele model possam ser agrupadas

Após criar a nova Criteria, você definir o trecho sql que deseja ser executado:
```php
<?php

namespace App\Repositories\Criteria\Movies;

use Masterkey\Repository\Criteria;
use Masterkey\Repository\Contracts\RepositoryContract as Repository;

/**
 * MoviesNotRated
 *
 * @package App\Repositories\Criteria\Movies
 */
class MoviesNotRated extends Criteria
{
    /**
     * @param   $model
     * @param   Repository $repository
     * @return  mixed
     */
    public function apply($model, Repository $repository)
    {
        return $model->where('was_rated', false);
    }
}
```

### Usando no Controller
Para utilização no controller, basta instanciar a nova classe e passá-la para o repository

```php
<?php

use App\Repositories\Criteria\Movies\MoviesNotRated;
use App\Repositories\FilmRepository as Film;

class FilmsController extends Controller {

    /**
     * @var Film
     */
    private $film;

    public function __construct(Film $film)
    {
        $this->film = $film;
    }

    public function index()
    {
        $this->film->pushCriteria(new MoviesNotRated());
        return \Response::json($this->film->all());
    }

    /*
     * Você também pode utilizar o método getByCriteria
     */
    public function notRated()
    {
        $criteria = new MoviesNotRated();
        return $this->film
                    ->getByCriteria($criteria)
                    ->all();
    }
}
```
