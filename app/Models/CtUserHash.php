<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class CtUserHash extends Model implements Authenticatable
{
    use AuthenticatableTrait;

    protected $connection = 'mysql2';           
    protected $table = 'ct_users_hash';
    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'full_name', 'pwd', 'npk', 'dept', 'sect', 'subsect', 'golongan', 'acting'
    ];

    public function getAuthPassword()
    {
        return $this->pwd;
    }
}