<?php

namespace Masterkey\Tests\Models;

use Masterkey\Repository\AbstractValidator;

class UserValidator extends AbstractValidator
{
    public function rules() : array
    {
        return [
            'name'  => 'required'
        ];
    }
}