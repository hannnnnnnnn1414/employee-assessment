<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Assessment;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;

class ImportController extends Controller
{
    public function index()
    {
        return view('import');
    }

    public function store(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            // Import data dari Excel
            $data = Excel::toArray(new ExcelImport, $request->file('excel_file'));

            if (empty($data[0])) {
                return back()->with('error', 'File Excel kosong atau format tidak sesuai');
            }

            // Proses data
            $importedCount = $this->processExcelData($data[0]);

            return redirect()->route('assessment')
                ->with('success', "Berhasil mengimpor {$importedCount} data karyawan dan penilaian");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function processExcelData(array $rows)
    {
        $importedCount = 0;

        // Lewati header (baris pertama)
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            // Pastikan ada data minimal NPK dan NAMA
            if (empty($row[1]) || empty($row[2])) {
                continue;
            }

            DB::transaction(function () use ($row, &$importedCount) {
                // Proses data karyawan
                $employeeData = $this->prepareEmployeeData($row);

                // Cek apakah karyawan sudah ada berdasarkan NPK
                $employee = Employee::where('npk', $employeeData['npk'])->first();

                if (!$employee) {
                    // Buat karyawan baru
                    $employee = Employee::create($employeeData);
                } else {
                    // Update data karyawan yang sudah ada
                    $employee->update($employeeData);
                }

                // Proses data assessment (demerit)
                $this->processAssessmentData($employee, $row);

                $importedCount++;
            });
        }

        return $importedCount;
    }

    private function prepareEmployeeData(array $row): array
    {
        // Mapping kolom Excel ke field database
        // NO[0], NPK[1], NAMA[2], GOL[3], DEPT[4]
        return [
            'npk' => $this->cleanValue($row[1]),
            'nama' => $this->cleanValue($row[2]),
            'golongan' => $this->cleanValue($row[3]),
            'dept' => $this->cleanValue($row[4]),
            'jabatan' => $this->determineJabatan($this->cleanValue($row[3]), $this->cleanValue($row[4]))
        ];
    }

    private function processAssessmentData(Employee $employee, array $row)
    {
        // Mapping demerit dari Excel
        // SD + I[6], M+ST[7], SP I[8], SP II[9], SP III[10]

        // Ambil periode penilaian saat ini (misal: tahun 2025)
        $tahun = date('Y');
        $periode = $this->determinePeriode();

        // Cek apakah sudah ada assessment untuk periode ini
        $assessment = Assessment::where('employee_id', $employee->id)
            ->where('periode_penilaian', $periode)
            ->first();

        $assessmentData = [
            'employee_id' => $employee->id,
            'periode_penilaian' => $periode,
            'tanggal_penilaian' => now(),
            'nama' => $employee->nama,
            'jabatan' => $employee->jabatan,
            'dept_seksi' => $employee->dept,
            'npk' => $employee->npk,
            'golongan' => $employee->golongan,
            'ijin' => $this->cleanValue($row[9], true) ?? 0,
            'mangkir' => $this->cleanValue($row[10], true) ?? 0,
            'sp1' => $this->cleanValue($row[11], true) ?? 0,
            'sp2' => $this->cleanValue($row[12], true) ?? 0,
            'sp3' => $this->cleanValue($row[13], true) ?? 0,
            'kualitas' => 40, // Default
            'kuantitas' => 40,
            'kerjasama' => 40,
            'inisiatif_kreatifitas' => 40,
            'keandalan_tanggung_jawab' => 40,
            'disiplin' => 40,
            'integritas_loyalitas' => 40,
            'qcc_ss' => 40,
            'mengarahkan_menghargai' => 40,
        ];

        if ($assessment) {
            // Update assessment yang sudah ada
            $assessment->update($assessmentData);
        } else {
            // Buat assessment baru
            $newAssessment = new Assessment($assessmentData);

            // Hitung nilai berdasarkan bobot
            $this->calculateAssessmentValues($newAssessment);

            $newAssessment->save();
        }
    }

    private function determineJabatan($golongan, $dept): string
    {
        // Logika menentukan jabatan berdasarkan golongan dan departemen
        $golongan = strtoupper($golongan);

        if (in_array($golongan, ['IV', 'V'])) {
            return 'Manager';
        }

        if (str_contains($golongan, 'III') && str_contains(strtoupper($dept), 'MANAGER')) {
            return 'Manager';
        }

        // Default berdasarkan departemen
        $deptLower = strtolower($dept);
        if (str_contains($deptLower, 'staff') || str_contains($deptLower, 'staf')) {
            return 'Staff';
        }

        if (str_contains($deptLower, 'supervisor') || str_contains($deptLower, 'spv')) {
            return 'Supervisor';
        }

        return 'Staff'; // Default
    }

    private function determinePeriode(): string
    {
        $bulan = date('n');
        $tahun = date('Y');

        if ($bulan >= 4 && $bulan <= 9) {
            return "Periode 2 | April - September {$tahun}";
        }

        return "Periode 1 | Oktober " . ($tahun - 1) . " - Maret {$tahun}";
    }

    private function calculateAssessmentValues(Assessment $assessment)
    {
        // Reuse calculation logic dari AssessmentController
        $assessmentController = new AssessmentController();

        // Hitung bobot
        $jabatanType = $assessmentController->getJabatanType($assessment->jabatan);
        $bobot = $assessmentController->getBobot($assessment->golongan, $jabatanType);

        // Hitung semua nilai
        $calculations = $this->calculateAssessmentValuesLogic($assessment, $bobot);

        // Update assessment dengan nilai yang dihitung
        $assessment->fill($calculations);
    }

    private function calculateAssessmentValuesLogic(Assessment $assessment, array $bobot): array
    {
        // Replikasi logika perhitungan dari AssessmentController
        $rataPrestasi = ($assessment->kualitas + $assessment->kuantitas) / 2;
        $subTotalPrestasi = $rataPrestasi * $bobot['prestasi'];

        $disiplinSetelahIjin = max(40, $assessment->disiplin - ($assessment->ijin * 10));

        // Hitung rata-rata non prestasi
        $nilaiNonPrestasi = [
            $assessment->kerjasama,
            $assessment->inisiatif_kreatifitas,
            $assessment->keandalan_tanggung_jawab,
            $disiplinSetelahIjin,
            $assessment->integritas_loyalitas,
            $assessment->qcc_ss
        ];
        $rataNonPrestasi = array_sum($nilaiNonPrestasi) / count($nilaiNonPrestasi);
        $subTotalNonPrestasi = $rataNonPrestasi * $bobot['non_prestasi'];

        // Hitung man management
        $subTotalManManagement = $assessment->mengarahkan_menghargai * $bobot['man_management'];

        // Hitung total nilai
        $nilaiTotal = $subTotalPrestasi + $subTotalNonPrestasi + $subTotalManManagement;

        // Hitung demerit (tanpa ijin)
        $demerit = ($assessment->mangkir * 3) +
            ($assessment->sp1 * 4) +
            ($assessment->sp2 * 8) +
            ($assessment->sp3 * 12);

        // Hitung nilai akhir
        $nilaiAkhir = max(0, $nilaiTotal - $demerit);

        // Tentukan nilai mutu
        $nilaiMutu = match (true) {
            $nilaiAkhir >= 90 => 'BS',
            $nilaiAkhir >= 80 => 'B',
            $nilaiAkhir >= 70 => 'C',
            $nilaiAkhir >= 60 => 'K',
            default => 'KS',
        };

        return [
            'rata_prestasi' => round($rataPrestasi, 2),
            'sub_total_prestasi' => round($subTotalPrestasi, 2),
            'rata_non_prestasi' => round($rataNonPrestasi, 2),
            'sub_total_non_prestasi' => round($subTotalNonPrestasi, 2),
            'sub_total_man_management' => round($subTotalManManagement, 2),
            'demerit' => $demerit,
            'nilai_total' => round($nilaiTotal, 2),
            'nilai_akhir' => round($nilaiAkhir, 2),
            'nilai_mutu' => $nilaiMutu,
            'bobot_prestasi' => $bobot['prestasi'],
            'bobot_non_prestasi' => $bobot['non_prestasi'],
            'bobot_man_management' => $bobot['man_management'],
        ];
    }

    private function cleanValue($value, $forceNumeric = false)
    {
        if (is_null($value) || $value === '') {
            return 0;
        }

        // Hapus spasi ekstra dan karakter khusus
        $value = trim($value);
        $value = str_replace(['"', "'"], '', $value);

        if ($value === '') {
            return 0;
        }

        // Jika forceNumeric true atau nilai adalah kolom angka (ijin, mangkir, sp1, dll)
        if ($forceNumeric || is_numeric($value)) {
            // Hapus semua karakter non-digit kecuali titik dan koma
            $numericValue = preg_replace('/[^0-9.,-]/', '', $value);

            // Ganti koma dengan titik untuk float
            $numericValue = str_replace(',', '.', $numericValue);

            // Konversi ke float atau int
            if (strpos($numericValue, '.') !== false) {
                return (float) $numericValue;
            }

            return (int) $numericValue;
        }

        // Untuk nama, departemen, dll kembalikan string
        return $value;
    }

    public function template()
    {
        // Buat template Excel sederhana
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        $data = [
            ['NO', 'NPK', 'NAMA', 'GOL', 'DEPT', 'NILAI', 'GRADE', 'SD + I', 'M+ST', 'SP I', 'SP II', 'SP III', 'LATE'],
            [1, 1592, 'Saputra', 'II', 'MIS', '', '', 1, 1, 1, 1, 1, ''],
            [2, 1593, 'Budi', 'III', 'HRD', '', '', 2, 0, 0, 0, 0, ''],
            [3, 1594, 'Sari', 'I', 'Finance', '', '', 0, 1, 1, 0, 0, ''],
        ];

        $filename = 'template_import_penilaian_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new class($data) implements FromArray {
            private $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }
        }, $filename, \Maatwebsite\Excel\Excel::XLSX, $headers);
    }
}
