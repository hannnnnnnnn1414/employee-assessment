<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'npk',
        'nama',
        'email',
        'password',
        'dept',
        'jabatan',
        'golongan'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeByDepartment(Builder $query, $department = null)
    {
        if ($department) {
            return $query->where('dept', $department);
        }
        return $query;
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
}
