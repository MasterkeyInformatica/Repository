<?php

namespace Masterkey\Tests\Models;

use Prettus\Validator\LaravelValidator;

class UserValidator extends LaravelValidator
{
    protected $rules = [
        'name'  => 'required'
    ];
}