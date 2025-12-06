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
            $data = Excel::toArray(new ExcelImport, $request->file('excel_file'));

            if (empty($data[0])) {
                return back()->with('error', 'File Excel kosong atau format tidak sesuai');
            }

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

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            if (empty($row[1]) || empty($row[2])) {
                continue;
            }

            DB::transaction(function () use ($row, &$importedCount) {
                $employeeData = $this->prepareEmployeeData($row);

                $employee = Employee::where('npk', $employeeData['npk'])->first();

                if (!$employee) {
                    $employee = Employee::create($employeeData);
                } else {
                    $employee->update($employeeData);
                }

                $this->processAssessmentData($employee, $row);

                $importedCount++;
            });
        }

        return $importedCount;
    }

    private function prepareEmployeeData(array $row): array
    {
        return [
            'npk' => $this->cleanValue($row[1], true),
            'nama' => $this->cleanValue($row[2]),
            'golongan' => $this->cleanValue($row[3]),
            'dept' => $this->cleanValue($row[4]),
            'jabatan' => $this->determineJabatan($this->cleanValue($row[3]), $this->cleanValue($row[4]))
        ];
    }

    private function processAssessmentData(Employee $employee, array $row)
    {
        $tahun = date('Y');
        $periode = $this->determinePeriode();

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
            'ijin' => $this->cleanValue($row[7], true) ?? 0,
            'mangkir' => $this->cleanValue($row[8], true) ?? 0,
            'sp1' => $this->cleanValue($row[9], true) ?? 0,
            'sp2' => $this->cleanValue($row[10], true) ?? 0,
            'sp3' => $this->cleanValue($row[11], true) ?? 0,
            'kualitas' => 40,
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
            $assessment->update($assessmentData);
        } else {
            $newAssessment = new Assessment($assessmentData);
            $this->calculateAssessmentValues($newAssessment);
            $newAssessment->save();
        }
    }

    private function determineJabatan($golongan, $dept): string
    {
        $golongan = strtoupper($golongan);

        if (in_array($golongan, ['IV', 'V'])) {
            return 'Manager';
        }

        if (str_contains($golongan, 'III') && str_contains(strtoupper($dept), 'MANAGER')) {
            return 'Manager';
        }

        $deptLower = strtolower($dept);
        if (str_contains($deptLower, 'staff') || str_contains($deptLower, 'staf')) {
            return 'Staff';
        }

        if (str_contains($deptLower, 'supervisor') || str_contains($deptLower, 'spv')) {
            return 'Supervisor';
        }

        return 'Staff';
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
        $assessmentController = new AssessmentController();

        $jabatanType = $assessmentController->getJabatanType($assessment->jabatan);
        $bobot = $assessmentController->getBobot($assessment->golongan, $jabatanType);

        $calculations = $this->calculateAssessmentValuesLogic($assessment, $bobot);

        $assessment->fill($calculations);
    }

    private function calculateAssessmentValuesLogic(Assessment $assessment, array $bobot): array
    {
        $rataPrestasi = ($assessment->kualitas + $assessment->kuantitas) / 2;
        $subTotalPrestasi = $rataPrestasi * $bobot['prestasi'];

        $disiplinSetelahIjin = max(40, $assessment->disiplin - ($assessment->ijin * 10));

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

        $subTotalManManagement = $assessment->mengarahkan_menghargai * $bobot['man_management'];

        $nilaiTotal = $subTotalPrestasi + $subTotalNonPrestasi + $subTotalManManagement;

        $demerit = ($assessment->mangkir * 3) +
            ($assessment->sp1 * 4) +
            ($assessment->sp2 * 8) +
            ($assessment->sp3 * 12);

        $nilaiAkhir = max(0, $nilaiTotal - $demerit);

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

    private function cleanValue($value, $keepZero = false, $forceNumeric = false)
    {
        if (is_null($value) || $value === '') {
            return 0;
        }

        $value = trim($value);
        $value = str_replace(['"', "'"], '', $value);

        if ($value === '') {
            return 0;
        }

        if ($keepZero) {
            return $value;
        }

        if ($forceNumeric || is_numeric($value)) {
            $numericValue = preg_replace('/[^0-9.,-]/', '', $value);
            $numericValue = str_replace(',', '.', $numericValue);

            if (strpos($numericValue, '.') !== false) {
                return (float) $numericValue;
            }

            return (int) $numericValue;
        }

        return $value;
    }

    public function template()
    {
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        $data = [
            ['NO', 'NPK', 'NAMA', 'GOL', 'DEPT', 'NILAI', 'GRADE', 'SD + I', 'M+ST', 'SP I', 'SP II', 'SP III', 'LATE']
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
