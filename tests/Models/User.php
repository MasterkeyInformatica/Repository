<?php

namespace Masterkey\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = 'sqlite';

    protected $fillable = [
        'name',
        'active',
        'logins',
        'failed_logins'
    ];

    public $timestamps = false;
}
