<?php

namespace Masterkey\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $connection = 'sqlite';

    protected $fillable = [
        'file'
    ];

    public $timestamps = false;
}