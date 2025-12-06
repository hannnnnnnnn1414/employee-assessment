<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
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
        'bobot_man_management'
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

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
