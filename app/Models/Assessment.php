<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'periode_penilaian',
        'tanggal_penilaian',
        'nama',
        'jabatan',
        'dept_seksi',
        'npk',
        'golongan',
        'kualitas',
        'kuantitas',
        'rata_prestasi',
        'sub_total_prestasi',
        'kerjasama',
        'inisiatif_kreatifitas',
        'keandalan_tanggung_jawab',
        'disiplin',
        'integritas_loyalitas',
        'qcc_ss',
        'rata_non_prestasi',
        'sub_total_non_prestasi',
        'mengarahkan_menghargai',
        'sub_total_man_management',
        'ijin',
        'mangkir',
        'sp1',
        'sp2',
        'sp3',
        'demerit',
        'nilai_total',
        'nilai_akhir',
        'nilai_mutu',
        'kekuatan',
        'kelemahan',
        'yang_menilai',
        'atasan_yang_menilai',
        'bobot_prestasi',
        'bobot_non_prestasi',
        'bobot_man_management',
        'status',
        'is_imported',
        'submitted_at'
    ];

    protected $casts = [
        'tanggal_penilaian' => 'date',
        'kualitas' => 'float',
        'kuantitas' => 'float',
        'rata_prestasi' => 'float',
        'sub_total_prestasi' => 'float',
        'kerjasama' => 'float',
        'inisiatif_kreatifitas' => 'float',
        'keandalan_tanggung_jawab' => 'float',
        'disiplin' => 'float',
        'integritas_loyalitas' => 'float',
        'qcc_ss' => 'float',
        'rata_non_prestasi' => 'float',
        'sub_total_non_prestasi' => 'float',
        'mengarahkan_menghargai' => 'float',
        'sub_total_man_management' => 'float',
        'demerit' => 'integer',
        'nilai_total' => 'float',
        'nilai_akhir' => 'float',
        'bobot_prestasi' => 'float',
        'bobot_non_prestasi' => 'float',
        'bobot_man_management' => 'float',
        'is_imported' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    public function getBobotPrestasiPercentAttribute(): string
    {
        return ($this->bobot_prestasi * 100) . '%';
    }

    public function getBobotNonPrestasiPercentAttribute(): string
    {
        return ($this->bobot_non_prestasi * 100) . '%';
    }

    public function getBobotManManagementPercentAttribute(): string
    {
        return ($this->bobot_man_management * 100) . '%';
    }

    public function getFormattedNilaiTotalAttribute(): string
    {
        return number_format($this->nilai_total, 2);
    }

    public function getFormattedNilaiAkhirAttribute(): string
    {
        return number_format($this->nilai_akhir, 2);
    }

    public function getIsManagerAttribute(): bool
    {
        $jabatan = Str::lower($this->jabatan);
        $managerKeywords = ['manager', 'mgr', 'kepala', 'head', 'superintendent', 'supervisor'];

        foreach ($managerKeywords as $keyword) {
            if (Str::contains($jabatan, $keyword)) {
                return true;
            }
        }

        return false;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Not Assessed',
            'submitted' => 'Pending Review',
            'completed' => 'Assessed',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'warning',
            'submitted' => 'info',
            'completed' => 'success',
            default => 'secondary'
        };
    }

    public function getIsAssessedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilterByUserDepartment($query, $user)
    {
        if ($user->role !== 'HR') {
            $query->whereHas('user', function ($query) use ($user) {
                $query->where('dept', $user->dept);
            });
        }

        return $query;
    }

    public function scopeFilterByPeriod($query, $period)
    {
        if ($period) {
            $query->where('periode_penilaian', $period);
        }

        return $query;
    }

    public function scopeFilterByStatus($query, $status)
    {
        if ($status) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function scopeNotAssessed($query)
    {
        return $query->where('status', 'draft')
            ->where('is_imported', true);
    }

    public function scopeAssessed($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeImported($query)
    {
        return $query->where('is_imported', true);
    }

    public function scopeManual($query)
    {
        return $query->where('is_imported', false);
    }

    public function scopeNeedAssessment($query)
    {
        return $query->where('status', 'draft')
            ->orWhere(function ($q) {
                $q->where('status', 'submitted');
            });
    }
}
