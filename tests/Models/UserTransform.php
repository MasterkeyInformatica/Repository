<?php

namespace Masterkey\Tests\Models;

use Masterkey\Repository\AbstractTransformer;

class UserTransform extends AbstractTransformer
{
    public function transform(User $user)
    {
        return [
            'nome'          => strtoupper($user->name),
            'ativo'         => (bool) $user->active,
            'tentativas'    => $user->logins
        ];
    }
}