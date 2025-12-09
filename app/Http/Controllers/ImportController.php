<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Assessment;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportController extends Controller
{
    private function determinePeriodeFromExcel($periodeCell)
    {
        $periodeCell = $this->cleanValue($periodeCell);

        if (empty($periodeCell)) {
            return $this->determinePeriode();
        }

        if (str_contains($periodeCell, 'Periode 1')) {
            $tahun = date('Y');
            $bulan = date('n');

            return $bulan >= 10
                ? "Periode 1 | Oktober {$tahun} - Maret " . ($tahun + 1)
                : "Periode 1 | Oktober " . ($tahun - 1) . " - Maret {$tahun}";
        }

        if (str_contains($periodeCell, 'Periode 2')) {
            $tahun = date('Y');
            return "Periode 2 | April {$tahun} - September {$tahun}";
        }

        return $this->determinePeriode();
    }

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
            Log::error('Import error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function processExcelData(array $rows)
    {
        $importedCount = 0;

        if (count($rows) < 2) {
            return 0;
        }

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            if (empty($row[2]) || empty($row[3])) {
                continue;
            }

            try {
                DB::transaction(function () use ($row, &$importedCount) {
                    $userData = $this->prepareUserData($row);

                    $user = User::where('npk', $userData['npk'])->first();

                    if (!$user) {
                        $user = User::create($userData);
                    } else {
                        $user->update($userData);
                    }

                    $this->processAssessmentData($user, $row);
                    $importedCount++;
                });
            } catch (\Exception $e) {
            }
        }

        return $importedCount;
    }

    private function prepareUserData(array $row): array
    {
        $npk = $this->cleanValue($row[2], true);
        $nama = $this->cleanValue($row[3]);
        $email = strtolower(str_replace(' ', '.', trim($nama))) . '@gmail.com';

        return [
            'npk' => $npk,
            'nama' => $nama,
            'email' => $email,
            'password' => Hash::make('admin'),
            'golongan' => $this->cleanValue($row[4]),
            'dept' => $this->cleanValue($row[5]),
            'jabatan' => $this->determineJabatan(
                $this->cleanValue($row[4]),
                $this->cleanValue($row[5])
            ),
        ];
    }

    private function processAssessmentData(User $user, array $row)
    {
        $periode = $this->determinePeriodeFromExcel($row[1] ?? '');

        $assessment = Assessment::where('user_id', $user->id)
            ->where('periode_penilaian', $periode)
            ->first();

        $assessmentData = [
            'user_id' => $user->id,
            'periode_penilaian' => $periode,
            'tanggal_penilaian' => now(),
            'nama' => $user->nama,
            'jabatan' => $user->jabatan,
            'dept_seksi' => $user->dept,
            'npk' => $user->npk,
            'golongan' => $user->golongan,
            'ijin' => (int)$this->cleanValue($row[8] ?? 0, false, true),
            'mangkir' => (int)$this->cleanValue($row[9] ?? 0, false, true),
            'sp1' => (int)$this->cleanValue($row[10] ?? 0, false, true),
            'sp2' => (int)$this->cleanValue($row[11] ?? 0, false, true),
            'sp3' => (int)$this->cleanValue($row[12] ?? 0, false, true),
            'kualitas' => 40,
            'kuantitas' => 40,
            'kerjasama' => 40,
            'inisiatif_kreatifitas' => 40,
            'keandalan_tanggung_jawab' => 40,
            'disiplin' => 40,
            'integritas_loyalitas' => 40,
            'qcc_ss' => 40,
            'mengarahkan_menghargai' => 40,
            'status' => 'draft',
            'is_imported' => true,

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
        $golongan = strtoupper(trim($golongan));
        $dept = strtoupper(trim($dept));

        if (in_array($golongan, ['IV', 'V'])) {
            return 'Manager';
        }

        if (str_contains($golongan, 'III') && str_contains($dept, 'MANAGER')) {
            return 'Manager';
        }

        $deptLower = strtolower($dept);

        if (str_contains($deptLower, 'staff') || str_contains($deptLower, 'staf')) {
            return 'Staff';
        }

        if (str_contains($deptLower, 'supervisor') || str_contains($deptLower, 'spv')) {
            return 'Supervisor';
        }

        return 'non-mgr';
    }

    private function determinePeriode(): string
    {
        $bulan = date('n');
        $tahun = date('Y');

        return ($bulan >= 4 && $bulan <= 9)
            ? "Periode 2 | April - September {$tahun}"
            : "Periode 1 | Oktober " . ($tahun - 1) . " - Maret {$tahun}";
    }

    private function calculateAssessmentValues(Assessment $assessment)
    {
        $assessmentController = new AssessmentController();

        $jabatanType = $assessmentController->getJabatanType($assessment->jabatan);
        $bobot = $assessmentController->getBobot($assessment->golongan, $jabatanType);

        $assessment->fill(
            $this->calculateAssessmentValuesLogic($assessment, $bobot)
        );
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

        $demerit =
            ($assessment->mangkir * 3) +
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

        $value = trim(str_replace(['"', "'"], '', $value));

        if ($value === '') {
            return 0;
        }

        if ($keepZero) {
            return $value;
        }

        if ($forceNumeric || is_numeric($value)) {
            $numericValue = preg_replace('/[^0-9.,-]/', '', $value);
            $numericValue = str_replace(',', '.', $numericValue);

            return strpos($numericValue, '.') !== false
                ? (float)$numericValue
                : (int)$numericValue;
        }

        return $value;
    }

    public function template()
    {
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;

        $data = [
            ['NO', 'PERIODE', 'NPK', 'NAMA', 'GOL', 'DEPT', 'NILAI', 'GRADE', 'SD + I', 'M+ST', 'SP I', 'SP II', 'SP III', 'LATE']
        ];

        $filename = 'template_import_penilaian_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new class($data, $currentYear, $nextYear) implements FromArray, \Maatwebsite\Excel\Concerns\WithEvents {
            private $data;
            private $currentYear;
            private $nextYear;

            public function __construct($data, $currentYear, $nextYear)
            {
                $this->data = $data;
                $this->currentYear = $currentYear;
                $this->nextYear = $nextYear;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function registerEvents(): array
            {
                return [
                    \Maatwebsite\Excel\Events\AfterSheet::class => function (\Maatwebsite\Excel\Events\AfterSheet $event) {
                        $sheet = $event->sheet->getDelegate();

                        $sheet->getColumnDimension('A')->setWidth(8);
                        $sheet->getColumnDimension('B')->setWidth(30);
                        $sheet->getColumnDimension('C')->setWidth(12);
                        $sheet->getColumnDimension('D')->setWidth(25);
                        $sheet->getColumnDimension('E')->setWidth(8);
                        $sheet->getColumnDimension('F')->setWidth(20);
                        $sheet->getColumnDimension('G')->setWidth(10);
                        $sheet->getColumnDimension('H')->setWidth(10);
                        $sheet->getColumnDimension('I')->setWidth(10);
                        $sheet->getColumnDimension('J')->setWidth(10);
                        $sheet->getColumnDimension('K')->setWidth(8);
                        $sheet->getColumnDimension('L')->setWidth(8);
                        $sheet->getColumnDimension('M')->setWidth(8);
                        $sheet->getColumnDimension('N')->setWidth(10);

                        $headerStyle = [
                            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => '4472C4']
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                                'wrapText' => true
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => '000000']
                                ]
                            ]
                        ];

                        $event->sheet->getStyle('A1:N1')->applyFromArray($headerStyle);

                        $validation = $sheet->getDataValidation('B2:B1000');
                        $validation->setType(DataValidation::TYPE_LIST);
                        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                        $validation->setAllowBlank(false);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setErrorTitle('Input error');
                        $validation->setError('Value is not in list.');
                        $validation->setPromptTitle('Pilih Periode');
                        $validation->setPrompt('Silakan pilih periode dari dropdown.');

                        $periodeOptions = [
                            "Periode 1 | Okt {$this->currentYear} - Mar {$this->nextYear}",
                            "Periode 2 | Apr {$this->currentYear} - Sep {$this->currentYear}"
                        ];

                        $validation->setFormula1('"' . implode(',', $periodeOptions) . '"');

                        $sheet->setCellValue('A2', '1');
                        $sheet->setCellValue('I2', '0');
                        $sheet->setCellValue('J2', '0');
                        $sheet->setCellValue('K2', '0');
                        $sheet->setCellValue('L2', '0');
                        $sheet->setCellValue('M2', '0');
                        $sheet->setCellValue('N2', '0');

                        $dataRowStyle = [
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => '000000']
                                ]
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F2F2F2']
                            ],
                            'alignment' => [
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                            ]
                        ];

                        $event->sheet->getStyle('A2:N2')->applyFromArray($dataRowStyle);

                        $centerColumns = ['A', 'E', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];

                        foreach ($centerColumns as $col) {
                            $event->sheet->getStyle("{$col}2")
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        }

                        $sheet->setAutoFilter('A1:N1');
                        $sheet->freezePane('A2');
                    }
                ];
            }
        }, $filename);
    }
}
