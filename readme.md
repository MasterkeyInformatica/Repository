Masterkey Repository
====================

Este projeto foi desenvolvido para que pudessemos ter uma camada de
abstração dos Models do Laravel, desacoplando a lógica dos Controllers.

Para utilização com o Laravel, utilize o composer:

```sh
composer require masterkey/repository
```

Após o composer baixar a dependência, adicione o *Service Provider* em app.php

```php
[
    'providers' => [
        // Outros providers
        Masterkey\Repository\Providers\RepositoryServiceProvider::class,
    ]
]
```

Feito isso, publique o arquivo de configuração do repositório:
```php
php artisan vendor:publish
```

No arquivo de configuração você pode definir o local onde os repositories e critarias serão criados

Criando Repositories
--------------------

em breve
