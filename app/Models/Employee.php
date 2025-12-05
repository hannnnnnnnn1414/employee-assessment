<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'npk',
        'nama',
        'dept',
        'jabatan',
        'golongan'
    ];

    protected $table = 'employees';

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
}
