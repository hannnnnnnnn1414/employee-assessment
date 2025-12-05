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
                                        Buat Penilaian Karya Karyawan
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

                <!-- Form Penilaian -->
                <div class="col-12">
                    <form action="{{ route('assessment.store') }}" method="POST" id="assessmentForm">
                        @csrf

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
                                        <label for="employee_id" class="form-label">Pilih Karyawan *</label>
                                        <select class="form-control" id="employee_id" name="employee_id" required
                                            onchange="updateEmployeeData()">
                                            <option value="">Pilih Karyawan</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}" data-npk="{{ $employee->npk }}"
                                                    data-nama="{{ $employee->nama }}" data-dept="{{ $employee->dept }}"
                                                    data-jabatan="{{ $employee->jabatan }}"
                                                    data-golongan="{{ $employee->golongan }}">
                                                    {{ $employee->npk }} - {{ $employee->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="npk" class="form-label">NPK</label>
                                        <input type="text" class="form-control" id="npk" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="nama" class="form-label">Nama</label>
                                        <input type="text" class="form-control" id="nama" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="jabatan" class="form-label">Jabatan</label>
                                        <input type="text" class="form-control" id="jabatan" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="dept_seksi" class="form-label">Dept / Seksi</label>
                                        <input type="text" class="form-control" id="dept_seksi" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="golongan" class="form-label">Golongan</label>
                                        <input type="text" class="form-control" id="golongan" readonly>
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
                                                <option value="{{ $periode }}">{{ $periode }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal_penilaian" class="form-label">Tanggal Penilaian *</label>
                                        <input type="date" class="form-control" id="tanggal_penilaian"
                                            name="tanggal_penilaian" required value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card A. PRESTASI -->
                        <div class="card mb-4">
                            <div class="card-header bg-transparent border-bottom">
                                <h5 class="mb-0 text-success">
                                    <i class="ti ti-trending-up"></i>
                                    A. PRESTASI (Bobot: <span id="bobot-prestasi">0%</span>)
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
                                                        name="kualitas" min="40" max="100" value="40"
                                                        onchange="calculateAll()">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Kuantitas (Quantity)</td>
                                                <td>
                                                    <input type="number" class="form-control nilai-prestasi"
                                                        name="kuantitas" min="40" max="100"
                                                        value="40" onchange="calculateAll()">
                                                </td>
                                            </tr>
                                            <tr class="table-warning">
                                                <td colspan="2" class="text-end"><strong>Rata-rata
                                                        Prestasi</strong></td>
                                                <td>
                                                    <input type="number" class="form-control" id="rata_prestasi"
                                                        readonly>
                                                </td>
                                            </tr>
                                            <tr class="table-warning">
                                                <td colspan="2" class="text-end"><strong>SUB TOTAL (Bobot x
                                                        Rata-rata)</strong></td>
                                                <td>
                                                    <input type="number" class="form-control"
                                                        id="sub_total_prestasi" name="sub_total_prestasi" readonly>
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
                                    B. NON PRESTASI (Bobot: <span id="bobot-non-prestasi">0%</span>)
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
                                                    ['name' => 'kerjasama', 'label' => 'Kerjasama (Team Work)'],
                                                    [
                                                        'name' => 'inisiatif_kreatifitas',
                                                        'label' => 'Inisiatif dan Kreatifitas',
                                                    ],
                                                    [
                                                        'name' => 'keandalan_tanggung_jawab',
                                                        'label' => 'Keandalan / Tanggung Jawab',
                                                    ],
                                                    ['name' => 'disiplin', 'label' => 'Disiplin'],
                                                    [
                                                        'name' => 'integritas_loyalitas',
                                                        'label' => 'Integritas / Loyalitas',
                                                    ],
                                                    ['name' => 'qcc_ss', 'label' => 'QCC & SS'],
                                                ];
                                            @endphp
                                            @foreach ($nonPrestasiItems as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 3 }}</td>
                                                    <td>{{ $item['label'] }}</td>
                                                    <td>
                                                        <input type="number" class="form-control nilai-non-prestasi"
                                                            name="{{ $item['name'] }}" min="40" max="100"
                                                            value="40" onchange="calculateAll()">
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-warning">
                                                <td colspan="2" class="text-end"><strong>Rata-rata Non
                                                        Prestasi</strong></td>
                                                <td>
                                                    <input type="number" class="form-control" id="rata_non_prestasi"
                                                        readonly>
                                                </td>
                                            </tr>
                                            <tr class="table-warning">
                                                <td colspan="2" class="text-end"><strong>SUB TOTAL (Bobot x
                                                        Rata-rata)</strong></td>
                                                <td>
                                                    <input type="number" class="form-control"
                                                        id="sub_total_non_prestasi" name="sub_total_non_prestasi"
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
                                    C. MAN MANAGEMENT (Bobot: <span id="bobot-man-management">0%</span>)
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
                                                        value="40" onchange="calculateAll()">
                                                </td>
                                            </tr>
                                            <tr class="table-warning">
                                                <td colspan="2" class="text-end"><strong>SUB TOTAL (Bobot x
                                                        Nilai)</strong></td>
                                                <td>
                                                    <input type="number" class="form-control"
                                                        id="sub_total_man_management" name="sub_total_man_management"
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
                                            ['name' => 'ijin', 'label' => 'Ijin', 'points' => 10],
                                            ['name' => 'mangkir', 'label' => 'Mangkir', 'points' => 3],
                                            ['name' => 'sp1', 'label' => 'SP I', 'points' => 4],
                                            ['name' => 'sp2', 'label' => 'SP II', 'points' => 8],
                                            ['name' => 'sp3', 'label' => 'SP III', 'points' => 12],
                                        ];
                                    @endphp
                                    @foreach ($demeritItems as $item)
                                        <div class="col-md-2 mb-3">
                                            <label for="{{ $item['name'] }}" class="form-label">
                                                {{ $item['label'] }} ({{ $item['points'] }})
                                            </label>
                                            <input type="number" class="form-control demerit"
                                                name="{{ $item['name'] }}" value="0" min="0"
                                                onchange="calculateAll()">
                                        </div>
                                    @endforeach
                                    <div class="col-md-2 mb-3">
                                        <label for="demerit" class="form-label">Total Demerit</label>
                                        <input type="number" class="form-control" id="demerit" name="demerit"
                                            readonly>
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
                                            placeholder="Tuliskan kekuatan karyawan..."></textarea>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="kelemahan" class="form-label">Kelemahan</label>
                                        <textarea class="form-control" id="kelemahan" name="kelemahan" rows="4"
                                            placeholder="Tuliskan kelemahan karyawan..."></textarea>
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
                                            name="yang_menilai" placeholder="Nama penilai...">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="atasan_yang_menilai" class="form-label">Atasan Yang
                                            Menilai</label>
                                        <input type="text" class="form-control" id="atasan_yang_menilai"
                                            name="atasan_yang_menilai" placeholder="Nama atasan penilai...">
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
                                            name="nilai_total" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="nilai_akhir" class="form-label">Nilai Akhir (Total -
                                            Demerit)</label>
                                        <input type="number" class="form-control" id="nilai_akhir"
                                            name="nilai_akhir" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="nilai_mutu" class="form-label">Nilai Mutu</label>
                                        <input type="text" class="form-control" id="nilai_mutu" name="nilai_mutu"
                                            readonly>
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
                                    <i class="ti ti-check"></i> Simpan Penilaian
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
        // Konfigurasi bobot lengkap
        const BOBOT_CONFIG = {
            'I': {
                'non-mgr': {
                    prestasi: 0.70,
                    non_prestasi: 0.30,
                    man_management: 0.00
                },
                'mgr': {
                    prestasi: 0.70,
                    non_prestasi: 0.25,
                    man_management: 0.05
                }
            },
            'II': {
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

        // Fungsi untuk menentukan apakah jabatan adalah manager
        function isManager(jabatan) {
            if (!jabatan) return false;

            const jabatanLower = jabatan.toLowerCase();
            const managerKeywords = ['manager', 'mgr', 'kepala', 'head', 'superintendent', 'supervisor'];

            return managerKeywords.some(keyword => jabatanLower.includes(keyword));
        }

        // Fungsi untuk mendapatkan bobot berdasarkan golongan dan jabatan
        function getBobot(golongan, jabatan) {
            if (!golongan || !BOBOT_CONFIG[golongan]) {
                return {
                    prestasi: 0.60,
                    non_prestasi: 0.35,
                    man_management: 0.05
                };
            }

            const isMgr = isManager(jabatan);
            const jabatanType = isMgr ? 'mgr' : 'non-mgr';
            const config = BOBOT_CONFIG[golongan];

            // Untuk golongan IV dan V, hanya ada manager
            if (['IV', 'V'].includes(golongan)) {
                return config.mgr || {
                    prestasi: 0.50,
                    non_prestasi: 0.30,
                    man_management: 0.20
                };
            }

            return config[jabatanType] || config['non-mgr'];
        }

        // Fungsi utama untuk menghitung semua nilai
        function calculateAll() {
            const golongan = document.getElementById('golongan').value;
            const jabatan = document.getElementById('jabatan').value;

            if (!golongan) {
                resetCalculations();
                return;
            }

            // Dapatkan bobot
            const bobot = getBobot(golongan, jabatan);

            // Update display bobot
            document.getElementById('bobot-prestasi').textContent = (bobot.prestasi * 100).toFixed(0) + '%';
            document.getElementById('bobot-non-prestasi').textContent = (bobot.non_prestasi * 100).toFixed(0) + '%';
            document.getElementById('bobot-man-management').textContent = (bobot.man_management * 100).toFixed(0) + '%';

            // 1. Hitung Prestasi
            const kualitas = parseFloat(document.querySelector('input[name="kualitas"]').value) || 0;
            const kuantitas = parseFloat(document.querySelector('input[name="kuantitas"]').value) || 0;
            const rataPrestasi = (kualitas + kuantitas) / 2;
            const subTotalPrestasi = rataPrestasi * bobot.prestasi;

            document.getElementById('rata_prestasi').value = rataPrestasi.toFixed(2);
            document.getElementById('sub_total_prestasi').value = subTotalPrestasi.toFixed(2);

            // 2. Hitung Non Prestasi
            const nonPrestasiInputs = document.querySelectorAll('.nilai-non-prestasi');
            let totalNonPrestasi = 0;
            nonPrestasiInputs.forEach(input => {
                totalNonPrestasi += parseFloat(input.value) || 0;
            });
            const rataNonPrestasi = totalNonPrestasi / nonPrestasiInputs.length;
            const subTotalNonPrestasi = rataNonPrestasi * bobot.non_prestasi;

            document.getElementById('rata_non_prestasi').value = rataNonPrestasi.toFixed(2);
            document.getElementById('sub_total_non_prestasi').value = subTotalNonPrestasi.toFixed(2);

            // 3. Hitung Man Management
            const manManagement = parseFloat(document.querySelector('input[name="mengarahkan_menghargai"]').value) || 0;
            const subTotalManManagement = manManagement * bobot.man_management;

            document.getElementById('sub_total_man_management').value = subTotalManManagement.toFixed(2);

            // 4. Hitung Demerit
            let totalDemerit = 0;
            const demeritConfig = {
                'ijin': 10,
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

            // 5. Hitung Total dan Akhir
            const nilaiTotal = subTotalPrestasi + subTotalNonPrestasi + subTotalManManagement;
            const nilaiAkhir = Math.max(0, nilaiTotal - totalDemerit);
            const nilaiMutu = getNilaiMutu(nilaiAkhir);

            document.getElementById('nilai_total').value = nilaiTotal.toFixed(2);
            document.getElementById('nilai_akhir').value = nilaiAkhir.toFixed(2);
            document.getElementById('nilai_mutu').value = nilaiMutu;
        }

        // Fungsi untuk reset perhitungan
        function resetCalculations() {
            const resetFields = [
                'rata_prestasi', 'sub_total_prestasi',
                'rata_non_prestasi', 'sub_total_non_prestasi',
                'sub_total_man_management', 'demerit',
                'nilai_total', 'nilai_akhir', 'nilai_mutu'
            ];

            resetFields.forEach(id => {
                document.getElementById(id).value = '';
            });

            document.getElementById('bobot-prestasi').textContent = '0%';
            document.getElementById('bobot-non-prestasi').textContent = '0%';
            document.getElementById('bobot-man-management').textContent = '0%';
        }

        // Fungsi untuk menentukan nilai mutu
        function getNilaiMutu(nilai) {
            if (nilai >= 90) return 'BS';
            if (nilai >= 80) return 'B';
            if (nilai >= 70) return 'C';
            if (nilai >= 60) return 'K';
            return 'KS';
        }

        // Fungsi update data karyawan
        function updateEmployeeData() {
            const select = document.getElementById('employee_id');
            const option = select.options[select.selectedIndex];

            if (option.value) {
                document.getElementById('npk').value = option.getAttribute('data-npk') || '';
                document.getElementById('nama').value = option.getAttribute('data-nama') || '';
                document.getElementById('dept_seksi').value = option.getAttribute('data-dept') || '';
                document.getElementById('jabatan').value = option.getAttribute('data-jabatan') || '';
                document.getElementById('golongan').value = option.getAttribute('data-golongan') || '';

                calculateAll();
            } else {
                resetCalculations();
            }
        }

        // Event listener saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Attach event listeners to all input fields
            const allInputs = document.querySelectorAll('input[type="number"], select');
            allInputs.forEach(input => {
                input.addEventListener('input', calculateAll);
                input.addEventListener('change', calculateAll);
            });

            // Initial calculation if employee is selected
            if (document.getElementById('employee_id').value) {
                calculateAll();
            }
        });

        // Validasi form sebelum submit
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
