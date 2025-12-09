<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'npk',
        'nama',
        'email',
        'password',
        'dept',
        'jabatan',
        'golongan',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function scopeByDepartment($query, $dept = null)
    {
        if ($dept) {
            return $query->where('dept', $dept);
        }
        return $query;
    }

    public function otps()
    {
        return $this->hasMany(Otp::class);
    }

    public function latestValidOtp()
    {
        return $this->otps()->valid()->latest()->first();
    }
}
