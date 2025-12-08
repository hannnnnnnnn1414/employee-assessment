<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<body>
    @include('components.layout')

    <div class="pc-container">
        <div class="pc-content">

            <div class="row">
                <!-- Header -->
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body py-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-0">
                                        <i class="ti ti-clipboard"></i>
                                        Edit Penilaian Karyawan
                                    </h5>
                                </div>
                                <div class="col-md-6 text-end">
                                    <a href="{{ route('assessment') }}" class="btn btn-secondary btn-sm">
                                        <i class="ti ti-arrow-left"></i> Kembali ke Daftar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Edit Penilaian -->
                <div class="col-12">
                    <form action="{{ route('assessment.update', $assessment->id) }}" method="POST" id="assessmentForm">
                        @csrf
                        @method('PUT')

                        <!-- Card Data Karyawan -->
                        <div class="card mb-4">
                            <div class="card-header bg-transparent border-bottom">
                                <h5 class="mb-0 text-primary">
                                    <i class="ti ti-user"></i>
                                    Data Karyawan
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="user_id" class="form-label">Pilih Karyawan *</label>
                                        <select class="form-control" id="user_id" name="user_id" required
                                            onchange="updateEmployeeData()">
                                            <option value="">Pilih Karyawan</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" data-npk="{{ $user->npk }}"
                                                    data-nama="{{ $user->nama }}" data-dept="{{ $user->dept }}"
                                                    data-jabatan="{{ $user->jabatan }}"
                                                    data-golongan="{{ $user->golongan }}"
                                                    {{ old('user_id', $assessment->user_id) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->npk }} - {{ $user->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="npk" class="form-label">NPK</label>
                                        <input type="text" class="form-control" id="npk"
                                            value="{{ $assessment->npk }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="nama" class="form-label">Nama</label>
                                        <input type="text" class="form-control" id="nama"
                                            value="{{ $assessment->nama }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="jabatan" class="form-label">Jabatan</label>
                                        <input type="text" class="form-control" id="jabatan"
                                            value="{{ $assessment->jabatan }}" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="dept_seksi" class="form-label">Dept / Seksi</label>
                                        <input type="text" class="form-control" id="dept_seksi"
                                            value="{{ $assessment->dept_seksi }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="golongan" class="form-label">Golongan</label>
                                        <input type="text" class="form-control" id="golongan"
                                            value="{{ $assessment->golongan }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Periode Penilaian -->
                        <div class="card mb-4">
                            <div class="card-header bg-transparent border-bottom">
                                <h5 class="mb-0 text-info">
                                    <i class="ti ti-calendar"></i>
                                    Periode Penilaian
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="periode_penilaian" class="form-label">Periode Penilaian *</label>
                                        <select class="form-control" id="periode_penilaian" name="periode_penilaian"
                                            required>
                                            <option value="">Pilih Periode</option>
                                            @foreach ($periodes as $periode)
                                                <option value="{{ $periode }}"
                                                    {{ old('periode_penilaian', $assessment->periode_penilaian) == $periode ? 'selected' : '' }}>
                                                    {{ $periode }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal_penilaian" class="form-label">Tanggal Penilaian *</label>
                                        <input type="date" class="form-control" id="tanggal_penilaian"
                                            name="tanggal_penilaian"
                                            value="{{ old('tanggal_penilaian', $assessment->tanggal_penilaian->format('Y-m-d')) }}"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card A. PRESTASI -->
                        <div class="card mb-4">
                            <div class="card-header bg-transparent border-bottom">
                                <h5 class="mb-0 text-success">
                                    <i class="ti ti-trending-up"></i>
                                    A. PRESTASI (Bobot: <span id="bobot-prestasi">
                                        {{ $assessment->bobot_prestasi * 100 }}%
                                    </span>)
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="35%">KRITERIA</th>
                                                <th width="60%">NILAI (40-100)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Kualitas (Quality)</td>
                                                <td>
                                                    <input type="number" class="form-control nilai-prestasi"
                                                        name="kualitas" min="40" max="100"
                                                        value="{{ old('kualitas', $assessment->kualitas) }}"
                                                        onchange="calculateAll()">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Kuantitas (Quantity)</td>
                                                <td>
                                                    <input type="number" class="form-control nilai-prestasi"
                                                        name="kuantitas" min="40" max="100"
                                                        value="{{ old('kuantitas', $assessment->kuantitas) }}"
                                                        onchange="calculateAll()">
                                                </td>
                                            </tr>
                                            <tr class="table-warning">
                                                <td colspan="2" class="text-end"><strong>Rata-rata
                                                        Prestasi</strong></td>
                                                <td>
                                                    <input type="number" class="form-control" id="rata_prestasi"
                                                        value="{{ old('rata_prestasi', $assessment->rata_prestasi) }}"
                                                        readonly>
                                                </td>
                                            </tr>
                                            <tr class="table-warning">
                                                <td colspan="2" class="text-end"><strong>SUB TOTAL (Bobot x
                                                        Rata-rata)</strong></td>
                                                <td>
                                                    <input type="number" class="form-control"
                                                        id="sub_total_prestasi" name="sub_total_prestasi"
                                                        value="{{ old('sub_total_prestasi', $assessment->sub_total_prestasi) }}"
                                                        readonly>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Card B. NON PRESTASI -->
                        <div class="card mb-4">
                            <div class="card-header bg-transparent border-bottom">
                                <h5 class="mb-0 text-warning">
                                    <i class="ti ti-users"></i>
                                    B. NON PRESTASI (Bobot: <span id="bobot-non-prestasi">
                                        {{ $assessment->bobot_non_prestasi * 100 }}%
                                    </span>)
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="35%">KRITERIA</th>
                                                <th width="60%">NILAI (40-100)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $nonPrestasiItems = [
                                                    [
                                                        'name' => 'kerjasama',
                                                        'label' => 'Kerjasama (Team Work)',
                                                        'value' => $assessment->kerjasama,
                                                    ],
                                                    [
                                                        'name' => 'inisiatif_kreatifitas',
                                                        'label' => 'Inisiatif dan Kreatifitas',
                                                        'value' => $assessment->inisiatif_kreatifitas,
                                                    ],
                                                    [
                                                        'name' => 'keandalan_tanggung_jawab',
                                                        'label' => 'Keandalan / Tanggung Jawab',
                                                        'value' => $assessment->keandalan_tanggung_jawab,
                                                    ],
                                                    [
                                                        'name' => 'disiplin',
                                                        'label' => 'Disiplin',
                                                        'value' => $assessment->disiplin_awal ?? $assessment->disiplin,
                                                    ],
                                                    [
                                                        'name' => 'integritas_loyalitas',
                                                        'label' => 'Integritas / Loyalitas',
                                                        'value' => $assessment->integritas_loyalitas,
                                                    ],
                                                    [
                                                        'name' => 'qcc_ss',
                                                        'label' => 'QCC & SS',
                                                        'value' => $assessment->qcc_ss,
                                                    ],
                                                ];
                                            @endphp
                                            @foreach ($nonPrestasiItems as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 3 }}</td>
                                                    <td>{{ $item['label'] }}</td>
                                                    <td>
                                                        <input type="number" class="form-control nilai-non-prestasi"
                                                            name="{{ $item['name'] }}" min="40" max="100"
                                                            value="{{ old($item['name'], $item['value']) }}"
                                                            onchange="calculateAll()">
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-warning">
                                                <td colspan="2" class="text-end"><strong>Rata-rata Non
                                                        Prestasi</strong></td>
                                                <td>
                                                    <input type="number" class="form-control" id="rata_non_prestasi"
                                                        value="{{ old('rata_non_prestasi', $assessment->rata_non_prestasi) }}"
                                                        readonly>
                                                </td>
                                            </tr>
                                            <tr class="table-warning">
                                                <td colspan="2" class="text-end"><strong>SUB TOTAL (Bobot x
                                                        Rata-rata)</strong></td>
                                                <td>
                                                    <input type="number" class="form-control"
                                                        id="sub_total_non_prestasi" name="sub_total_non_prestasi"
                                                        value="{{ old('sub_total_non_prestasi', $assessment->sub_total_non_prestasi) }}"
                                                        readonly>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Card C. MAN MANAGEMENT -->
                        <div class="card mb-4">
                            <div class="card-header bg-transparent border-bottom">
                                <h5 class="mb-0 text-danger">
                                    <i class="ti ti-businessplan"></i>
                                    C. MAN MANAGEMENT (Bobot: <span id="bobot-man-management">
                                        {{ $assessment->bobot_man_management * 100 }}%
                                    </span>)
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="35%">KRITERIA</th>
                                                <th width="60%">NILAI (40-100)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>9</td>
                                                <td>Mengarahkan dan Menghargai</td>
                                                <td>
                                                    <input type="number" class="form-control nilai-man-management"
                                                        name="mengarahkan_menghargai" min="40" max="100"
                                                        value="{{ old('mengarahkan_menghargai', $assessment->mengarahkan_menghargai) }}"
                                                        onchange="calculateAll()">
                                                </td>
                                            </tr>
                                            <tr class="table-warning">
                                                <td colspan="2" class="text-end"><strong>SUB TOTAL (Bobot x
                                                        Nilai)</strong></td>
                                                <td>
                                                    <input type="number" class="form-control"
                                                        id="sub_total_man_management" name="sub_total_man_management"
                                                        value="{{ old('sub_total_man_management', $assessment->sub_total_man_management) }}"
                                                        readonly>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Card Demerit -->
                        <div class="card mb-4">
                            <div class="card-header bg-transparent border-bottom">
                                <h5 class="mb-0 text-secondary">
                                    <i class="ti ti-alert-triangle"></i>
                                    Data Demerit
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $demeritItems = [
                                            [
                                                'name' => 'ijin',
                                                'label' => 'Ijin',
                                                'points' => 10,
                                                'value' => $assessment->ijin ?? 0,
                                            ],
                                            [
                                                'name' => 'mangkir',
                                                'label' => 'Mangkir',
                                                'points' => 3,
                                                'value' => $assessment->mangkir ?? 0,
                                            ],
                                            [
                                                'name' => 'sp1',
                                                'label' => 'SP I',
                                                'points' => 4,
                                                'value' => $assessment->sp1 ?? 0,
                                            ],
                                            [
                                                'name' => 'sp2',
                                                'label' => 'SP II',
                                                'points' => 8,
                                                'value' => $assessment->sp2 ?? 0,
                                            ],
                                            [
                                                'name' => 'sp3',
                                                'label' => 'SP III',
                                                'points' => 12,
                                                'value' => $assessment->sp3 ?? 0,
                                            ],
                                        ];
                                    @endphp
                                    @foreach ($demeritItems as $item)
                                        <div class="col-md-2 mb-3">
                                            <label for="{{ $item['name'] }}" class="form-label">
                                                {{ $item['label'] }} ({{ $item['points'] }})
                                            </label>
                                            <input type="number" class="form-control demerit"
                                                name="{{ $item['name'] }}"
                                                value="{{ old($item['name'], $item['value']) }}" min="0"
                                                onchange="calculateAll()">
                                        </div>
                                    @endforeach
                                    <div class="col-md-2 mb-3">
                                        <label for="demerit" class="form-label">Total Demerit</label>
                                        <input type="number" class="form-control" id="demerit" name="demerit"
                                            value="{{ old('demerit', $assessment->demerit) }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Kekuatan & Kelemahan -->
                        <div class="card mb-4">
                            <div class="card-header bg-transparent border-bottom">
                                <h5 class="mb-0 text-dark">
                                    <i class="ti ti-notes"></i>
                                    Kekuatan & Kelemahan
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="kekuatan" class="form-label">Kekuatan</label>
                                        <textarea class="form-control" id="kekuatan" name="kekuatan" rows="4"
                                            placeholder="Tuliskan kekuatan karyawan...">{{ old('kekuatan', $assessment->kekuatan) }}</textarea>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="kelemahan" class="form-label">Kelemahan</label>
                                        <textarea class="form-control" id="kelemahan" name="kelemahan" rows="4"
                                            placeholder="Tuliskan kelemahan karyawan...">{{ old('kelemahan', $assessment->kelemahan) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Penilai -->
                        <div class="card mb-4">
                            <div class="card-header bg-transparent border-bottom">
                                <h5 class="mb-0 text-info">
                                    <i class="ti ti-user-check"></i>
                                    Data Penilai
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="yang_menilai" class="form-label">Yang Menilai</label>
                                        <input type="text" class="form-control" id="yang_menilai"
                                            name="yang_menilai"
                                            value="{{ old('yang_menilai', $assessment->yang_menilai) }}"
                                            placeholder="Nama penilai...">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="atasan_yang_menilai" class="form-label">Atasan Yang
                                            Menilai</label>
                                        <input type="text" class="form-control" id="atasan_yang_menilai"
                                            name="atasan_yang_menilai"
                                            value="{{ old('atasan_yang_menilai', $assessment->atasan_yang_menilai) }}"
                                            placeholder="Nama atasan penilai...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Total Nilai -->
                        <div class="card mb-4">
                            <div class="card-header bg-transparent border-bottom">
                                <h5 class="mb-0 text-primary">
                                    <i class="ti ti-calculator"></i>
                                    Total Nilai
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="nilai_total" class="form-label">Nilai Total (A+B+C)</label>
                                        <input type="number" class="form-control" id="nilai_total"
                                            name="nilai_total"
                                            value="{{ old('nilai_total', $assessment->nilai_total) }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="nilai_akhir" class="form-label">Nilai Akhir (Total -
                                            Demerit)</label>
                                        <input type="number" class="form-control" id="nilai_akhir"
                                            name="nilai_akhir"
                                            value="{{ old('nilai_akhir', $assessment->nilai_akhir) }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="nilai_mutu" class="form-label">Nilai Mutu</label>
                                        <input type="text" class="form-control" id="nilai_mutu" name="nilai_mutu"
                                            value="{{ old('nilai_mutu', $assessment->nilai_mutu) }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12 text-end">
                                <a href="{{ route('assessment') }}" class="btn btn-secondary me-2">
                                    <i class="ti ti-x"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check"></i> Update Penilaian
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <x-footer></x-footer>

    <script>
        const BOBOT_CONFIG = {
            'I': {
                'non-mgr': {
                    prestasi: 0.70,
                    non_prestasi: 0.30,
                    man_management: 0.00
                }
            },
            'II': {
                'non-mgr': {
                    prestasi: 0.70,
                    non_prestasi: 0.25,
                    man_management: 0.05
                }
            },
            'III': {
                'non-mgr': {
                    prestasi: 0.60,
                    non_prestasi: 0.35,
                    man_management: 0.05
                },
                'mgr': {
                    prestasi: 0.60,
                    non_prestasi: 0.30,
                    man_management: 0.10
                }
            },
            'IV': {
                'mgr': {
                    prestasi: 0.50,
                    non_prestasi: 0.30,
                    man_management: 0.20
                }
            },
            'V': {
                'mgr': {
                    prestasi: 0.50,
                    non_prestasi: 0.30,
                    man_management: 0.20
                }
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            const userSelect = document.getElementById('user_id');
            const selectedOption = userSelect.options[userSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                document.getElementById('npk').value = selectedOption.getAttribute('data-npk') ||
                    '{{ $assessment->npk }}';
                document.getElementById('nama').value = selectedOption.getAttribute('data-nama') ||
                    '{{ $assessment->nama }}';
                document.getElementById('dept_seksi').value = selectedOption.getAttribute('data-dept') ||
                    '{{ $assessment->dept_seksi }}';
                document.getElementById('jabatan').value = selectedOption.getAttribute('data-jabatan') ||
                    '{{ $assessment->jabatan }}';
                document.getElementById('golongan').value = '{{ $assessment->golongan }}';
            }

            calculateAll();
        });

        document.querySelector('input[name="ijin"]').addEventListener('input', function() {
            const ijintotal = parseInt(this.value) || 0;
            const disiplinInput = document.querySelector('input[name="disiplin"]');
            const disiplinAwal = parseFloat(disiplinInput.value) || 0;
            const disiplinSetelahIjin = Math.max(40, disiplinAwal - (ijintotal * 10));
            console.log(`Ijin: ${ijintotal} | Disiplin: ${disiplinAwal} → ${disiplinSetelahIjin}`);
            calculateAll();
        });

        function isManager(jabatan) {
            if (!jabatan) return false;
            const jabatanLower = jabatan.toLowerCase().trim();
            return jabatanLower === 'mgr';
        }

        function getBobot(golongan, jabatan) {
            const romanGolongan = convertToRoman(golongan);

            if (!romanGolongan || !BOBOT_CONFIG[romanGolongan]) {
                console.log('Golongan tidak ditemukan:', golongan, '->', romanGolongan);
                return {
                    prestasi: 0.60,
                    non_prestasi: 0.35,
                    man_management: 0.05
                };
            }

            console.log('Mencari bobot untuk:', romanGolongan, 'Jabatan:', jabatan);

            if (['I', 'II'].includes(romanGolongan)) {
                return BOBOT_CONFIG[romanGolongan]['non-mgr'];
            }

            if (['IV', 'V'].includes(romanGolongan)) {
                return BOBOT_CONFIG[romanGolongan]['mgr'];
            }

            if (romanGolongan === 'III') {
                const isMgr = isManager(jabatan);
                const jabatanType = isMgr ? 'mgr' : 'non-mgr';
                console.log('Golongan III, jabatan type:', jabatanType);
                return BOBOT_CONFIG[romanGolongan][jabatanType];
            }

            return {
                prestasi: 0.60,
                non_prestasi: 0.35,
                man_management: 0.05
            };
        }

        function calculateAll() {
            const golongan = document.getElementById('golongan').value;
            const jabatan = document.getElementById('jabatan').value;

            if (!golongan) {
                return;
            }

            const bobot = getBobot(golongan, jabatan);

            document.getElementById('bobot-prestasi').textContent = (bobot.prestasi * 100).toFixed(0) + '%';
            document.getElementById('bobot-non-prestasi').textContent = (bobot.non_prestasi * 100).toFixed(0) + '%';
            document.getElementById('bobot-man-management').textContent = (bobot.man_management * 100).toFixed(0) + '%';

            const kualitas = parseFloat(document.querySelector('input[name="kualitas"]').value) || 0;
            const kuantitas = parseFloat(document.querySelector('input[name="kuantitas"]').value) || 0;
            const rataPrestasi = (kualitas + kuantitas) / 2;
            const subTotalPrestasi = rataPrestasi * bobot.prestasi;

            document.getElementById('rata_prestasi').value = rataPrestasi.toFixed(2);
            document.getElementById('sub_total_prestasi').value = subTotalPrestasi.toFixed(2);

            const ijintotal = parseInt(document.querySelector('input[name="ijin"]').value) || 0;
            let nilaiDisiplin = parseFloat(document.querySelector('input[name="disiplin"]').value) || 0;
            const disiplinSetelahIjin = Math.max(40, nilaiDisiplin - (ijintotal * 10));

            const nonPrestasiValues = {
                kerjasama: parseFloat(document.querySelector('input[name="kerjasama"]').value) || 0,
                inisiatif_kreatifitas: parseFloat(document.querySelector('input[name="inisiatif_kreatifitas"]')
                    .value) || 0,
                keandalan_tanggung_jawab: parseFloat(document.querySelector('input[name="keandalan_tanggung_jawab"]')
                    .value) || 0,
                disiplin: disiplinSetelahIjin,
                integritas_loyalitas: parseFloat(document.querySelector('input[name="integritas_loyalitas"]').value) ||
                    0,
                qcc_ss: parseFloat(document.querySelector('input[name="qcc_ss"]').value) || 0
            };

            let totalNonPrestasi = 0;
            Object.values(nonPrestasiValues).forEach(value => totalNonPrestasi += value);

            const rataNonPrestasi = totalNonPrestasi / 6;
            const subTotalNonPrestasi = rataNonPrestasi * bobot.non_prestasi;

            document.getElementById('rata_non_prestasi').value = rataNonPrestasi.toFixed(2);
            document.getElementById('sub_total_non_prestasi').value = subTotalNonPrestasi.toFixed(2);

            const manManagement = parseFloat(document.querySelector('input[name="mengarahkan_menghargai"]').value) || 0;
            const subTotalManManagement = manManagement * bobot.man_management;

            document.getElementById('sub_total_man_management').value = subTotalManManagement.toFixed(2);

            let totalDemerit = 0;
            const demeritConfig = {
                'mangkir': 3,
                'sp1': 4,
                'sp2': 8,
                'sp3': 12
            };

            Object.keys(demeritConfig).forEach(name => {
                const input = document.querySelector(`input[name="${name}"]`);
                const value = parseInt(input.value) || 0;
                totalDemerit += value * demeritConfig[name];
            });

            document.getElementById('demerit').value = totalDemerit;

            const nilaiTotal = subTotalPrestasi + subTotalNonPrestasi + subTotalManManagement;
            const nilaiAkhir = Math.max(0, nilaiTotal - totalDemerit);
            const nilaiMutu = getNilaiMutu(nilaiAkhir);

            document.getElementById('nilai_total').value = nilaiTotal.toFixed(2);
            document.getElementById('nilai_akhir').value = nilaiAkhir.toFixed(2);
            document.getElementById('nilai_mutu').value = nilaiMutu;
        }

        function getNilaiMutu(nilai) {
            if (nilai >= 90) return 'BS';
            if (nilai >= 80) return 'B';
            if (nilai >= 70) return 'C';
            if (nilai >= 60) return 'K';
            return 'KS';
        }

        function updateEmployeeData() {
            const select = document.getElementById('user_id');
            const option = select.options[select.selectedIndex];

            if (option.value) {
                document.getElementById('npk').value = option.getAttribute('data-npk') || '';
                document.getElementById('nama').value = option.getAttribute('data-nama') || '';
                document.getElementById('dept_seksi').value = option.getAttribute('data-dept') || '';
                document.getElementById('jabatan').value = option.getAttribute('data-jabatan') || '';

                const golonganAngka = option.getAttribute('data-golongan') || '';
                const golonganRomawi = convertToRoman(golonganAngka);
                document.getElementById('golongan').value = golonganRomawi;

                calculateAll();
            }
        }

        function convertToRoman(num) {
            const romanMap = {
                1: 'I',
                2: 'II',
                3: 'III',
                4: 'IV',
                5: 'V'
            };
            return romanMap[num] || num;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const allInputs = document.querySelectorAll('input[type="number"], select');
            allInputs.forEach(input => {
                input.addEventListener('input', calculateAll);
                input.addEventListener('change', calculateAll);
            });
        });

        document.getElementById('assessmentForm').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Harap lengkapi semua field yang wajib diisi!');
            }
        });
    </script>
</body>

</html>
