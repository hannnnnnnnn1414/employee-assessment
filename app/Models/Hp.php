<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class Hp extends Model implements Authenticatable
{
    use AuthenticatableTrait;

    protected $connection = 'mysql3';           
    protected $table = 'hp';
    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'npk', 'no_hp'
    ];
}