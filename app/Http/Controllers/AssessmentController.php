<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\User;
use Illuminate\Support\Str;

class AssessmentController extends Controller
{
    private const BOBOT_CONFIG = [
        'I' => [
            'non-mgr' => ['prestasi' => 0.70, 'non_prestasi' => 0.30, 'man_management' => 0.00],
        ],
        'II' => [
            'non-mgr' => ['prestasi' => 0.70, 'non_prestasi' => 0.25, 'man_management' => 0.05],
        ],
        'III' => [
            'non-mgr' => ['prestasi' => 0.60, 'non_prestasi' => 0.35, 'man_management' => 0.05],
            'mgr' => ['prestasi' => 0.60, 'non_prestasi' => 0.30, 'man_management' => 0.10],
        ],
        'IV' => [
            'mgr' => ['prestasi' => 0.50, 'non_prestasi' => 0.30, 'man_management' => 0.20],
        ],
        'V' => [
            'mgr' => ['prestasi' => 0.50, 'non_prestasi' => 0.30, 'man_management' => 0.20],
        ],
    ];

    public function getJabatanType(string $jabatan): string
    {
        $jabatan = strtolower(trim($jabatan));

        if ($jabatan === 'mgr') {
            return 'mgr';
        }

        return 'non-mgr';
    }

    public function getBobot(string $golongan, string $jabatanType): array
    {
        if (!isset(self::BOBOT_CONFIG[$golongan])) {
            return ['prestasi' => 0.60, 'non_prestasi' => 0.35, 'man_management' => 0.05];
        }

        $config = self::BOBOT_CONFIG[$golongan];

        if (in_array($golongan, ['I', 'II'])) {
            return $config['non-mgr'];
        }

        if (in_array($golongan, ['IV', 'V'])) {
            return $config['mgr'];
        }

        if ($golongan === 'III') {
            return $config[$jabatanType] ?? $config['non-mgr'];
        }

        return $config['non-mgr'] ?? ['prestasi' => 0.60, 'non_prestasi' => 0.35, 'man_management' => 0.05];
    }

    public function show($id)
    {
        $assessment = Assessment::with('user')->findOrFail($id);
        return view('assessment-show-modal', compact('assessment'));
    }

    public function edit($id)
    {
        $assessment = Assessment::with('user')->findOrFail($id);
        $users = User::orderBy('nama')->get();
        $periodes = [
            'Periode 1 | Oktober - Maret',
            'Periode 2 | April - September'
        ];

        return view('assessment-edit', compact('assessment', 'users', 'periodes'));
    }

    public function update(Request $request, $id)
    {
        $assessment = Assessment::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'periode_penilaian' => 'required|string',
            'tanggal_penilaian' => 'required|date',
            'kualitas' => 'required|numeric|min:40|max:100',
            'kuantitas' => 'required|numeric|min:40|max:100',
            'kerjasama' => 'required|numeric|min:40|max:100',
            'inisiatif_kreatifitas' => 'required|numeric|min:40|max:100',
            'keandalan_tanggung_jawab' => 'required|numeric|min:40|max:100',
            'disiplin' => 'required|numeric|min:40|max:100',
            'integritas_loyalitas' => 'required|numeric|min:40|max:100',
            'qcc_ss' => 'required|numeric|min:40|max:100',
            'mengarahkan_menghargai' => 'required|numeric|min:40|max:100',
            'ijin' => 'nullable|numeric|min:0',
            'mangkir' => 'nullable|numeric|min:0',
            'sp1' => 'nullable|numeric|min:0',
            'sp2' => 'nullable|numeric|min:0',
            'sp3' => 'nullable|numeric|min:0',
            'kekuatan' => 'nullable|string|max:500',
            'kelemahan' => 'nullable|string|max:500',
            'yang_menilai' => 'nullable|string|max:100',
            'atasan_yang_menilai' => 'nullable|string|max:100',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $jabatanType = $this->getJabatanType($user->jabatan);
        $bobot = $this->getBobot($user->golongan, $jabatanType);

        $calculations = $this->calculateAllValues($validated, $bobot);

        $assessment->update(array_merge([
            'user_id' => $validated['user_id'],
            'periode_penilaian' => $validated['periode_penilaian'],
            'tanggal_penilaian' => $validated['tanggal_penilaian'],
            'nama' => $user->nama,
            'jabatan' => $user->jabatan,
            'dept_seksi' => $user->dept,
            'npk' => $user->npk,
            'golongan' => $user->golongan,
        ], $calculations));

        return redirect()->route('assessment')
            ->with('success', 'Penilaian karyawan berhasil diperbarui!');
    }

    public function index()
    {
        $assessments = Assessment::with('user')
            ->orderBy('tanggal_penilaian', 'desc')
            ->get();

        $users = User::get();
        $periodes = ['Periode 1 | Oktober - Maret', 'Periode 2 | April - September'];

        return view('assessment', compact('assessments', 'users', 'periodes'));
    }

    public function create()
    {
        $users = User::orderBy('nama')->get();

        $periodes = [
            'Periode 1 | Oktober - Maret',
            'Periode 2 | April - September'
        ];

        return view('assessment-create', compact('users', 'periodes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'periode_penilaian' => 'required|string',
            'tanggal_penilaian' => 'required|date',
            'kualitas' => 'required|numeric|min:40|max:100',
            'kuantitas' => 'required|numeric|min:40|max:100',
            'kerjasama' => 'required|numeric|min:40|max:100',
            'inisiatif_kreatifitas' => 'required|numeric|min:40|max:100',
            'keandalan_tanggung_jawab' => 'required|numeric|min:40|max:100',
            'disiplin' => 'required|numeric|min:40|max:100',
            'integritas_loyalitas' => 'required|numeric|min:40|max:100',
            'qcc_ss' => 'required|numeric|min:40|max:100',
            'mengarahkan_menghargai' => 'required|numeric|min:40|max:100',
            'ijin' => 'nullable|numeric|min:0',
            'mangkir' => 'nullable|numeric|min:0',
            'sp1' => 'nullable|numeric|min:0',
            'sp2' => 'nullable|numeric|min:0',
            'sp3' => 'nullable|numeric|min:0',
            'kekuatan' => 'nullable|string|max:500',
            'kelemahan' => 'nullable|string|max:500',
            'yang_menilai' => 'nullable|string|max:100',
            'atasan_yang_menilai' => 'nullable|string|max:100',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $jabatanType = $this->getJabatanType($user->jabatan);
        $bobot = $this->getBobot($user->golongan, $jabatanType);

        $calculations = $this->calculateAllValues($validated, $bobot);

        $assessment = Assessment::create(array_merge([
            'user_id' => $validated['user_id'],
            'periode_penilaian' => $validated['periode_penilaian'],
            'tanggal_penilaian' => $validated['tanggal_penilaian'],
            'nama' => $user->nama,
            'jabatan' => $user->jabatan,
            'dept_seksi' => $user->dept,
            'npk' => $user->npk,
            'golongan' => $user->golongan,
        ], $calculations));

        return redirect()->route('assessment')
            ->with('success', 'Penilaian karyawan berhasil disimpan!');
    }

    public function destroy($id)
    {
        $assessment = Assessment::findOrFail($id);
        $assessment->delete();

        return back()->with('success', 'Penilaian karyawan berhasil dihapus!');
    }

    private function calculateAllValues(array $data, array $bobot): array
    {
        $rataPrestasi = ($data['kualitas'] + $data['kuantitas']) / 2;
        $subTotalPrestasi = $rataPrestasi * $bobot['prestasi'];

        $ijin = $data['ijin'] ?? 0;
        $disiplinAwal = $data['disiplin'];
        $disiplinAkhir = max(40, $disiplinAwal - ($ijin * 10));

        $nilaiNonPrestasi = [
            $data['kerjasama'],
            $data['inisiatif_kreatifitas'],
            $data['keandalan_tanggung_jawab'],
            $disiplinAkhir,
            $data['integritas_loyalitas'],
            $data['qcc_ss']
        ];
        $rataNonPrestasi = array_sum($nilaiNonPrestasi) / count($nilaiNonPrestasi);
        $subTotalNonPrestasi = $rataNonPrestasi * $bobot['non_prestasi'];

        $subTotalManManagement = $data['mengarahkan_menghargai'] * $bobot['man_management'];

        $nilaiTotal = $subTotalPrestasi + $subTotalNonPrestasi + $subTotalManManagement;

        $demerit = $this->calculateDemerit(
            0,
            $data['mangkir'] ?? 0,
            $data['sp1'] ?? 0,
            $data['sp2'] ?? 0,
            $data['sp3'] ?? 0
        );

        $nilaiAkhir = max(0, $nilaiTotal - $demerit);
        $nilaiMutu = $this->determineNilaiMutu($nilaiAkhir);

        return [
            'kualitas' => $data['kualitas'],
            'kuantitas' => $data['kuantitas'],
            'rata_prestasi' => round($rataPrestasi, 2),
            'sub_total_prestasi' => round($subTotalPrestasi, 2),
            'kerjasama' => $data['kerjasama'],
            'inisiatif_kreatifitas' => $data['inisiatif_kreatifitas'],
            'keandalan_tanggung_jawab' => $data['keandalan_tanggung_jawab'],
            'disiplin' => $disiplinAkhir,
            'disiplin_awal' => $disiplinAwal,
            'integritas_loyalitas' => $data['integritas_loyalitas'],
            'qcc_ss' => $data['qcc_ss'],
            'rata_non_prestasi' => round($rataNonPrestasi, 2),
            'sub_total_non_prestasi' => round($subTotalNonPrestasi, 2),
            'mengarahkan_menghargai' => $data['mengarahkan_menghargai'],
            'sub_total_man_management' => round($subTotalManManagement, 2),
            'ijin' => $data['ijin'] ?? 0,
            'mangkir' => $data['mangkir'] ?? 0,
            'sp1' => $data['sp1'] ?? 0,
            'sp2' => $data['sp2'] ?? 0,
            'sp3' => $data['sp3'] ?? 0,
            'demerit' => $demerit,
            'nilai_total' => round($nilaiTotal, 2),
            'nilai_akhir' => round($nilaiAkhir, 2),
            'nilai_mutu' => $nilaiMutu,
            'kekuatan' => $data['kekuatan'] ?? null,
            'kelemahan' => $data['kelemahan'] ?? null,
            'yang_menilai' => $data['yang_menilai'] ?? null,
            'atasan_yang_menilai' => $data['atasan_yang_menilai'] ?? null,
            'bobot_prestasi' => $bobot['prestasi'],
            'bobot_non_prestasi' => $bobot['non_prestasi'],
            'bobot_man_management' => $bobot['man_management'],
        ];
    }

    private function calculateDemerit(int $ijin, int $mangkir, int $sp1, int $sp2, int $sp3): int
    {
        return ($mangkir * 3) + ($sp1 * 4) + ($sp2 * 8) + ($sp3 * 12);
    }

    private function determineNilaiMutu(float $nilaiAkhir): string
    {
        return match (true) {
            $nilaiAkhir >= 90 => 'BS',
            $nilaiAkhir >= 80 => 'B',
            $nilaiAkhir >= 70 => 'C',
            $nilaiAkhir >= 60 => 'K',
            default => 'KS',
        };
    }
}
