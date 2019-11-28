<?php

namespace Masterkey\Tests\Models;

use League\Fractal\TransformerAbstract;

class UserTransform extends TransformerAbstract
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