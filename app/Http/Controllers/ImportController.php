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
    private function getPeriode1(): string
    {
        return 'periode 1';
    }

    private function getPeriode2(): string
    {
        return "Periode 2";
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

    // private function processExcelData(array $rows)
    // {
    //     $importedCount = 0;

    //     if (count($rows) < 2) {
    //         return 0;
    //     }

    //     for ($i = 1; $i < count($rows); $i++) {
    //         $row = $rows[$i];

    //         if (empty($row[1]) || empty($row[2])) {
    //             continue;
    //         }

    //         try {
    //             DB::transaction(function () use ($row, &$importedCount) {
    //                 $userData = $this->prepareUserData($row);

    //                 $user = User::where('npk', $userData['npk'])->first();

    //                 if (!$user) {
    //                     $user = User::create($userData);
    //                 } else {
    //                     $user->update($userData);
    //                 }

    //                 $this->processAssessmentData($user, $row);
    //                 $importedCount++;
    //             });
    //         } catch (\Exception $e) {
    //         }
    //     }

    //     return $importedCount;
    // }

    private function processExcelData(array $rows)
    {
        $importedCount = 0;

        if (count($rows) < 2) {
            return 0;
        }

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            if (empty($row[1]) || empty($row[2])) {
                Log::info("Baris ke-{$i} dilewati: NPK atau Nama kosong", ['row' => $row]);
                continue;
            }

            $npk = $this->cleanValue($row[1], true);
            $nama = $this->cleanValue($row[2]);

            try {
                DB::transaction(function () use ($row, &$importedCount, $i, $npk, $nama) {
                    $userData = $this->prepareUserData($row);

                    $user = User::where('npk', $userData['npk'])->first();

                    if (!$user) {
                        $user = User::create($userData);
                        Log::info("Baris {$i} - User BARU dibuat", [
                            'npk' => $npk,
                            'nama' => $nama,
                            'user_id' => $user->id
                        ]);
                    } else {
                        $user->update($userData);
                        Log::info("Baris {$i} - User DIUPDATE", [
                            'npk' => $npk,
                            'nama' => $nama,
                            'user_id' => $user->id
                        ]);
                    }

                    $this->processAssessmentData($user, $row);
                    $importedCount++;
                });
            } catch (\Exception $e) {
                Log::error("Baris {$i} - GAGAL diproses", [
                    'npk' => $npk ?? '-',
                    'nama' => $nama ?? '-',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        Log::info("Import selesai", [
            'total_baris_diproses' => $importedCount,
            'total_baris_excel' => count($rows) - 1,
        ]);

        return $importedCount;
    }

    private function prepareUserData(array $row): array
    {
        $npk = $this->cleanValue($row[1], true);
        $nama = $this->cleanValue($row[2]);
        $email = strtolower(str_replace(' ', '.', trim($nama))) . '@gmail.com';

        return [
            'npk' => $npk,
            'nama' => $nama,
            'email' => $email,
            'password' => Hash::make('admin'),
            'golongan' => $this->cleanValue($row[6]),
            'dept' => $this->cleanValue($row[3]),
            'jabatan' => $this->determineJabatan(
                $this->cleanValue($row[6]),
                $this->cleanValue($row[3])
            ),
            'seksi' => $this->cleanValue($row[4] ?? ''),
            'sub_seksi' => $this->cleanValue($row[5] ?? ''),
        ];
    }

    // private function processAssessmentData(User $user, array $row)
    // {
    //     for ($i = 12; $i <= 16; $i++) {
    //         if ((int) $this->cleanValue($row[$i] ?? 0, false, true) > 0) {
    //             $hasP1 = true;
    //             break;
    //         }
    //     }

    //     $hasP2 = false;
    //     for ($i = 17; $i <= 21; $i++) {
    //         if ((int) $this->cleanValue($row[$i] ?? 0, false, true) > 0) {
    //             $hasP2 = true;
    //             break;
    //         }
    //     }

    //     if ($hasP1) {
    //         $this->processOnePeriod($user, $row, $this->getPeriode1(), 12, 13, 14, 15, 16);
    //     }

    //     if ($hasP2) {
    //         $this->processOnePeriod($user, $row, $this->getPeriode2(), 17, 18, 19, 20, 21);
    //     }

    // }

    private function processAssessmentData(User $user, array $row)
    {
        $hasP1 = false;
        for ($i = 12; $i <= 16; $i++) {
            if ((int) $this->cleanValue($row[$i] ?? 0, false, true) > 0) {
                $hasP1 = true;
                break;
            }
        }

        $hasP2 = false;
        for ($i = 17; $i <= 21; $i++) {
            if ((int) $this->cleanValue($row[$i] ?? 0, false, true) > 0) {
                $hasP2 = true;
                break;
            }
        }

        Log::debug("Assessment check untuk {$user->npk}", [
            'hasP1' => $hasP1,
            'hasP2' => $hasP2,
            'row_indices_12-16' => array_slice($row, 12, 5),
            'row_indices_17-21' => array_slice($row, 17, 5),
        ]);

        if ($hasP1) {
            $this->processOnePeriod($user, $row, $this->getPeriode1(), 12, 13, 14, 15, 16);
        }

        if ($hasP2) {
            $this->processOnePeriod($user, $row, $this->getPeriode2(), 17, 18, 19, 20, 21);
        }
    }

    private function processOnePeriod(User $user, array $row, string $periode, int $sdIdx, int $mstIdx, int $sp1Idx, int $sp2Idx, int $sp3Idx)
    {
        $ijin = (int) $this->cleanValue($row[$sdIdx] ?? 0, false, true);
        $mangkir = (int) $this->cleanValue($row[$mstIdx] ?? 0, false, true);
        $sp1 = (int) $this->cleanValue($row[$sp1Idx] ?? 0, false, true);
        $sp2 = (int) $this->cleanValue($row[$sp2Idx] ?? 0, false, true);
        $sp3 = (int) $this->cleanValue($row[$sp3Idx] ?? 0, false, true);

        if ($ijin === 0 && $mangkir === 0 && $sp1 === 0 && $sp2 === 0 && $sp3 === 0) {
            return;
        }

        $assessment = Assessment::where('user_id', $user->id)
            ->where('periode_penilaian', $periode)
            ->first();

        $assessmentData = [
            'user_id' => $user->id,
            'periode_penilaian' => $periode,
            'tanggal_penilaian' => now(),
            'nama' => $user->nama,
            'jabatan' => $user->jabatan,
            'dept' => $user->dept,
            'seksi' => $this->cleanValue($row[4] ?? ''),
            'sub_seksi' => $this->cleanValue($row[5] ?? ''),
            'npk' => $user->npk,
            'golongan' => $user->golongan,
            'ijin' => $ijin,
            'mangkir' => $mangkir,
            'sp1' => $sp1,
            'sp2' => $sp2,
            'sp3' => $sp3,
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
                ? (float) $numericValue
                : (int) $numericValue;
        }

        return $value;
    }

    public function template()
    {
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;

        $data = [
            [
                'NO',
                'NPK',
                'NAMA',
                'DEPARTEMEN',
                'SEKSI',
                'SUB SEKSI',
                'GRADE',
                'TGL. MASUK',
                'MASA KERJA',
                'JENIS KELAMIN',
                'TGL. LAHIR',
                'USIA',
                'SD + I P1',
                'M + ST P1',
                'SP I P1',
                'SP II P1',
                'SP III P1',
                'SD + I P2',
                'M + ST P2',
                'SP I P2',
                'SP II P2',
                'SP III P2',
                'SD + I AKUM',
                'M + ST AKUM',
                'SP I-III AKUM',
                'SD + I AKUM2',
                'M + ST AKUM2',
                'SP I-III AKUM2'
            ]
        ];

        $filename = 'employee_assessment' . '.xlsx';

        return Excel::download(
            new class ($data) implements FromArray, \Maatwebsite\Excel\Concerns\WithEvents {
            private $data;

            public function __construct($data)
            {
                $this->data = $data;
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

                        $sheet->getColumnDimension('A')->setWidth(6);
                        $sheet->getColumnDimension('B')->setWidth(12);
                        $sheet->getColumnDimension('C')->setWidth(25);
                        $sheet->getColumnDimension('D')->setWidth(25);
                        $sheet->getColumnDimension('E')->setWidth(20);
                        $sheet->getColumnDimension('F')->setWidth(25);
                        $sheet->getColumnDimension('G')->setWidth(8);
                        $sheet->getColumnDimension('H')->setWidth(15);
                        $sheet->getColumnDimension('I')->setWidth(12);
                        $sheet->getColumnDimension('J')->setWidth(12);
                        $sheet->getColumnDimension('K')->setWidth(15);
                        $sheet->getColumnDimension('L')->setWidth(8);

                        $sheet->getColumnDimension('M')->setWidth(10);
                        $sheet->getColumnDimension('N')->setWidth(10);
                        $sheet->getColumnDimension('O')->setWidth(8);
                        $sheet->getColumnDimension('P')->setWidth(8);
                        $sheet->getColumnDimension('Q')->setWidth(8);

                        $sheet->getColumnDimension('R')->setWidth(10);
                        $sheet->getColumnDimension('S')->setWidth(10);
                        $sheet->getColumnDimension('T')->setWidth(8);
                        $sheet->getColumnDimension('U')->setWidth(8);
                        $sheet->getColumnDimension('V')->setWidth(8);

                        $sheet->getColumnDimension('W')->setWidth(12);
                        $sheet->getColumnDimension('X')->setWidth(12);
                        $sheet->getColumnDimension('Y')->setWidth(12);
                        $sheet->getColumnDimension('Z')->setWidth(12);
                        $sheet->getColumnDimension('AA')->setWidth(12);
                        $sheet->getColumnDimension('AB')->setWidth(12);

                        $headerStyle = [
                        'font' => ['bold' => true, 'color' => ['rgb' => '212529']],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'ffffff']
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

                        $event->sheet->getStyle('A1:AB1')->applyFromArray($headerStyle);

                        $periode1HeaderStyle = [
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'BDD7EE']
                            ],
                        'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                        ];
                        $event->sheet->getStyle('M1:Q1')->applyFromArray($periode1HeaderStyle);

                        $periode2HeaderStyle = [
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'E2F0D9']
                            ],
                        'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                        ];
                        $event->sheet->getStyle('R1:V1')->applyFromArray($periode2HeaderStyle);

                        $akumulasiHeaderStyle = [
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'EDEDED']
                            ],
                        'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                        ];
                        $event->sheet->getStyle('W1:AB1')->applyFromArray($akumulasiHeaderStyle);

                        $sheet->setCellValue('A2', '1');
                        $sheet->setCellValue('M2', '0');
                        $sheet->setCellValue('N2', '0');
                        $sheet->setCellValue('O2', '0');
                        $sheet->setCellValue('P2', '0');
                        $sheet->setCellValue('Q2', '0');
                        $sheet->setCellValue('R2', '0');
                        $sheet->setCellValue('S2', '0');
                        $sheet->setCellValue('T2', '0');
                        $sheet->setCellValue('U2', '0');
                        $sheet->setCellValue('V2', '0');

                        $dataRowStyle = [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000']
                                ]
                            ],
                        'alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                            ]
                        ];

                        $event->sheet->getStyle('A2:AB2')->applyFromArray($dataRowStyle);

                        $event->sheet->getStyle('M2:Q2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAF7'); // biru super muda
                        $event->sheet->getStyle('R2:V2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2F0D9'); // hijau super muda
    
                        $centerColumns = ['A', 'G', 'I', 'J', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V'];

                        foreach ($centerColumns as $col) {
                            $event->sheet->getStyle("{$col}2")
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        }

                        $sheet->setAutoFilter('A1:AB1');
                        $sheet->freezePane('A2');
                    }
                ];
            }
            },
            $filename
        );
    }
}