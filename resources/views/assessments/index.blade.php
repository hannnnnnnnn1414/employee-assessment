<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<style>
    .text-purple {
        color: #6f42c1 !important;
    }

    .nilai-mutu-BS {
        color: #198754;
    }

    .nilai-mutu-B {
        color: #0dcaf0;
    }

    .nilai-mutu-C {
        color: #fd7e14;
    }

    .nilai-mutu-K {
        color: #ffc107;
    }

    .nilai-mutu-KS {
        color: #dc3545;
    }

    .text-purple {
        color: #6f42c1 !important;
    }

    .table-row-prestasi {
        background-color: rgba(25, 135, 84, 0.05);
    }

    .table-row-nonprestasi {
        background-color: rgba(253, 126, 20, 0.05);
    }

    .table-row-man {
        background-color: rgba(220, 53, 69, 0.05);
    }

    .table-total {
        background-color: rgba(13, 110, 253, 0.05);
    }

    .table-row-prestasi td:first-child {
        border-left: 4px solid #198754 !important;
    }

    .table-row-nonprestasi td:first-child {
        border-left: 4px solid #fd7e14 !important;
    }

    .table-row-man td:first-child {
        border-left: 4px solid #dc3545 !important;
    }

    .table-total td:first-child {
        border-left: 4px solid #0d6efd !important;
    }

    .badge-nilai-mutu-BS {
        color: #198754;
        font-weight: bold;
    }

    .badge-nilai-mutu-B {
        color: #0dcaf0;
        font-weight: bold;
    }

    .badge-nilai-mutu-C {
        color: #fd7e14;
        font-weight: bold;
    }

    .badge-nilai-mutu-K {
        color: #ffc107;
        font-weight: bold;
    }

    .badge-nilai-mutu-KS {
        color: #dc3545;
        font-weight: bold;
    }

    .bg-orange {
        background-color: #fd7e14 !important;
        color: white !important;
    }
</style>

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
                                        Penilaian Karyawan
                                    </h5>
                                </div>
                                <div class="col-md-6 text-end">
                                    <a href="{{ route('assessment.create') }}" class="btn btn-primary btn-sm">
                                        <i class="ti ti-plus"></i> Buat Penilaian Baru
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assessments Table -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Daftar Penilaian Karyawan
                                @if (request()->has('periode'))
                                    <small class="text-muted">(Hasil Filter)</small>
                                @endif
                            </h5>
                            <div>
                                @if (request()->has('periode'))
                                    <span class="badge bg-info me-2">Data Difilter</span>
                                @endif
                                <div class="btn-group ms-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-filter"></i> Filter Status
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('assessment') }}">All Status</a>
                                        <div class="dropdown-divider"></div>
                                        <h6 class="dropdown-header">Filter By Status</h6>
                                        <a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['status' => 'draft']) }}">
                                            <span class="badge bg-warning me-2">●</span> Not Assessed
                                        </a>
                                        <a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['status' => 'completed']) }}">
                                            <span class="badge bg-success me-2">●</span> Assessed
                                        </a>
                                    </div>
                                </div>

                                <div class="btn-group ms-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-filter"></i> Filter Period
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('assessment') }}">All</a>
                                        <div class="dropdown-divider"></div>
                                        <h6 class="dropdown-header">Period</h6>
                                        @foreach ($periodes as $periode)
                                            <a class="dropdown-item"
                                                href="{{ request()->fullUrlWithQuery(['periode' => $periode]) }}">
                                                {{ $periode }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">NPK</th>
                                            <th class="text-center">Nama</th>
                                            <th class="text-center">Jabatan</th>
                                            <th class="text-center">Dept/Seksi</th>
                                            <th class="text-center">Periode</th>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Nilai Akhir</th>
                                            <th class="text-center">Nilai Mutu</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($assessments as $index => $assessment)
                                            <tr class="{{ $assessment->status === 'draft' ?: '' }}">
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td class="text-center">{{ $assessment->npk }}</td>
                                                <td class="text-center">
                                                    <strong>{{ $assessment->nama }}</strong>
                                                    @if ($assessment->status === 'draft')
                                                        <br>
                                                        <small class="text-muted">Not assessed yet</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $assessment->jabatan }}</td>
                                                <td class="text-center">
                                                    {{ $assessment->dept }}
                                                    @if ($assessment->seksi)
                                                        <br><small class="text-muted">{{ $assessment->seksi }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $assessment->periode_penilaian }}</td>
                                                <td class="text-center">{{ $assessment->tanggal_penilaian }}</td>
                                                <td class="text-center">
                                                    @if ($assessment->status === 'completed')
                                                        <span
                                                            class="badge bg-primary">{{ $assessment->nilai_akhir }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($assessment->status === 'completed')
                                                        <span
                                                            class="badge bg-{{ $assessment->nilai_mutu == 'BS' ? 'success' : ($assessment->nilai_mutu == 'B' ? 'info' : ($assessment->nilai_mutu == 'C' ? 'warning' : 'danger')) }}">
                                                            {{ $assessment->nilai_mutu }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light text-dark">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ $assessment->status_color }}">
                                                        {{ $assessment->status_label }}
                                                        @if ($assessment->is_imported && $assessment->status === 'draft')
                                                            <i class="ms-1" title="Imported Data"></i>
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center flex gap-2">
                                                        <!-- Detail -->
                                                        <div class="text-center action-item" style="width:50px;">
                                                            <a href="{{ route('assessment.show', $assessment->id) }}"
                                                                class="btn p-0 border-0 bg-transparent"
                                                                title="Lihat Detail">
                                                                <i class="bi bi-eye fs-4 text-primary"></i>
                                                            </a>
                                                            <div class="small text-muted" style="font-size: 11px;">
                                                                Detail
                                                            </div>
                                                        </div>

                                                        <!-- Edit -->
                                                        <div class="text-center action-item" style="width:50px;">
                                                            <a href="{{ route('assessment.edit', $assessment->id) }}"
                                                                class="btn p-0 border-0 bg-transparent"
                                                                title="{{ $assessment->status === 'draft' ? 'Beri Nilai' : 'Edit Nilai' }}">
                                                                @if ($assessment->status === 'draft')
                                                                    <i
                                                                        class="bi bi-pencil-square fs-4 text-success"></i>
                                                                @else
                                                                    <i class="bi bi-pencil fs-4 text-warning"></i>
                                                                @endif
                                                            </a>
                                                            <div class="small text-muted" style="font-size: 11px;">
                                                                {{ $assessment->status === 'draft' ? 'Nilai' : 'Edit' }}
                                                            </div>
                                                        </div>

                                                        <!-- Delete -->
                                                        <div class="text-center action-item" style="width:50px;">
                                                            <button type="button"
                                                                class="btn p-0 border-0 bg-transparent delete-assessment"
                                                                data-assessment-id="{{ $assessment->id }}"
                                                                data-assessment-nama="{{ $assessment->nama }}"
                                                                title="Hapus">
                                                                <i class="bi bi-trash fs-4 text-danger"></i>
                                                            </button>
                                                            <div class="small text-muted" style="font-size: 11px;">
                                                                Hapus</div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center py-4">
                                                    <i class="bi bi-clipboard"
                                                        style="font-size: 48px; color: #6c757d;"></i>
                                                    <p class="mt-2 text-muted">Belum ada data penilaian.</p>
                                                    <a href="{{ route('assessment.create') }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="bi bi-plus"></i> Buat Penilaian Pertama
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- View Detail Modal -->
    <div class="modal fade" id="viewAssessmentModal" tabindex="-1" aria-labelledby="viewAssessmentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-clipboard me-2"></i>
                        Detail Penilaian Karyawan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <!-- Informasi Karyawan -->
                    <div class="card mb-4 border">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <div class="text-muted small">NPK</div>
                                    <div class="fw-bold text-primary" id="view-npk">-</div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="text-muted small">Nama</div>
                                    <div class="fw-bold text-dark" id="view-nama">-</div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="text-muted small">Jabatan</div>
                                    <div class="fw-bold text-info" id="view-jabatan">-</div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="text-muted small">Golongan</div>
                                    <div class="fw-bold text-warning" id="view-golongan">-</div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="text-muted small">Dept</div>
                                    <div class="fw-bold text-secondary" id="view-dept">-</div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="text-muted small">Seksi</div>
                                    <div class="fw-bold text-secondary" id="view-seksi">-</div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="text-muted small">Sub Seksi</div>
                                    <div class="fw-bold text-secondary" id="view-sub-seksi">-</div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="text-muted small">Periode</div>
                                    <div class="fw-bold text-purple" id="view-periode">-</div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="text-muted small">Tanggal</div>
                                    <div class="fw-bold text-dark" id="view-tanggal">-</div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="text-muted small">Status</div>
                                    <div class="fw-bold" id="view-status">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prestasi -->
                    <div class="card mb-4 border">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <span class="text-success fw-bold">A. PRESTASI</span>
                                <span class="text-muted float-end">Bobot: <span id="view-bobot-prestasi"
                                        class="fw-bold">0%</span></span>
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <td width="50%">Kualitas (Quality)</td>
                                            <td width="50%" class="text-end">
                                                <span class="fw-bold text-success" id="view-kualitas">0</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Kuantitas (Quantity)</td>
                                            <td class="text-end">
                                                <span class="fw-bold text-success" id="view-kuantitas">0</span>
                                            </td>
                                        </tr>
                                        <tr class="table-light">
                                            <td>Rata-rata Prestasi</td>
                                            <td class="text-end">
                                                <span class="fw-bold text-dark" id="view-rata-prestasi">0</span>
                                            </td>
                                        </tr>
                                        <tr class="table-light">
                                            <td>
                                                <span class="fw-bold">SUB TOTAL</span>
                                                <small class="text-muted d-block">(Bobot x Rata-rata)</small>
                                            </td>
                                            <td class="text-end">
                                                <h5 class="fw-bold text-success mb-0" id="view-sub-prestasi">0</h5>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Non Prestasi -->
                    <div class="card mb-4 border">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <span class="text-warning fw-bold">B. NON PRESTASI</span>
                                <span class="text-muted float-end">Bobot: <span id="view-bobot-non-prestasi"
                                        class="fw-bold">0%</span></span>
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <td width="50%">Kerjasama (Team Work)</td>
                                            <td width="50%" class="text-end">
                                                <span class="fw-bold text-warning" id="view-kerjasama">0</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Inisiatif dan Kreatifitas</td>
                                            <td class="text-end">
                                                <span class="fw-bold text-warning" id="view-inisiatif">0</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Keandalan / Tanggung Jawab</td>
                                            <td class="text-end">
                                                <span class="fw-bold text-warning" id="view-keandalan">0</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Disiplin</td>
                                            <td class="text-end">
                                                <span class="fw-bold text-warning" id="view-disiplin">0</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Integritas / Loyalitas</td>
                                            <td class="text-end">
                                                <span class="fw-bold text-warning" id="view-integritas">0</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>QCC & SS</td>
                                            <td class="text-end">
                                                <span class="fw-bold text-warning" id="view-qcc">0</span>
                                            </td>
                                        </tr>
                                        <tr class="table-light">
                                            <td>Rata-rata Non Prestasi</td>
                                            <td class="text-end">
                                                <span class="fw-bold text-dark" id="view-rata-nonprestasi">0</span>
                                            </td>
                                        </tr>
                                        <tr class="table-light">
                                            <td>
                                                <span class="fw-bold">SUB TOTAL</span>
                                                <small class="text-muted d-block">(Bobot x Rata-rata)</small>
                                            </td>
                                            <td class="text-end">
                                                <h5 class="fw-bold text-warning mb-0" id="view-sub-nonprestasi">0</h5>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Man Management -->
                    <div class="card mb-4 border">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <span class="text-danger fw-bold">C. MAN MANAGEMENT</span>
                                <span class="text-muted float-end">Bobot: <span id="view-bobot-man"
                                        class="fw-bold">0%</span></span>
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <td width="50%">Mengarahkan dan Menghargai</td>
                                            <td width="50%" class="text-end">
                                                <span class="fw-bold text-danger" id="view-man-management">0</span>
                                            </td>
                                        </tr>
                                        <tr class="table-light">
                                            <td>
                                                <span class="fw-bold">SUB TOTAL</span>
                                                <small class="text-muted d-block">(Bobot x Nilai)</small>
                                            </td>
                                            <td class="text-end">
                                                <h5 class="fw-bold text-danger mb-0" id="view-sub-man">0</h5>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Demerit dan Total -->
                    <div class="row mb-4">
                        <!-- Demerit -->
                        <div class="col-md-6">
                            <div class="card h-100 border">
                                <div class="card-body">
                                    <h6 class="card-title mb-3 text-secondary">
                                        <span class="fw-bold">Data Demerit</span>
                                    </h6>
                                    <div class="row text-center">
                                        <div class="col-md-4 mb-3">
                                            <div class="p-2">
                                                <div class="text-muted small">Ijin (x10)</div>
                                                <div class="fw-bold text-dark" id="view-ijin">0</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="p-2">
                                                <div class="text-muted small">Mangkir (x3)</div>
                                                <div class="fw-bold text-dark" id="view-mangkir">0</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="p-2">
                                                <div class="text-muted small">SP I (x4)</div>
                                                <div class="fw-bold text-dark" id="view-sp1">0</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="p-2">
                                                <div class="text-muted small">SP II (x8)</div>
                                                <div class="fw-bold text-dark" id="view-sp2">0</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="p-2">
                                                <div class="text-muted small">SP III (x12)</div>
                                                <div class="fw-bold text-dark" id="view-sp3">0</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="p-2">
                                                <div class="text-muted small">Total Demerit</div>
                                                <div class="fw-bold text-secondary" id="view-demerit">0</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Nilai -->
                        <div class="col-md-6">
                            <div class="card h-100 border">
                                <div class="card-body">
                                    <h6 class="card-title mb-3 text-primary">
                                        <span class="fw-bold">Total Nilai</span>
                                    </h6>
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="p-3 border rounded mb-2">
                                                <div class="text-muted small">Nilai Total (A+B+C)</div>
                                                <div class="fw-bold text-primary" id="view-nilai-total">0</div>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <div class="p-3 border rounded mb-2">
                                                <div class="text-muted small">Nilai Akhir (Total - Demerit)</div>
                                                <div class="fw-bold text-success" id="view-nilai-akhir">0</div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="p-3 border rounded">
                                                <div class="text-muted small">Nilai Mutu</div>
                                                <div class="fw-bold" id="view-nilai-mutu" style="font-size: 1.8rem;">
                                                    -</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kekuatan & Kelemahan -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100 border">
                                <div class="card-body">
                                    <h6 class="card-title mb-3 text-success">
                                        <span class="fw-bold">Kekuatan</span>
                                    </h6>
                                    <div id="view-kekuatan" class="text-muted">
                                        <i>Tidak ada data</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border">
                                <div class="card-body">
                                    <h6 class="card-title mb-3 text-warning">
                                        <span class="fw-bold">Kelemahan</span>
                                    </h6>
                                    <div id="view-kelemahan" class="text-muted">
                                        <i>Tidak ada data</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Penilai -->
                    <div class="card border">
                        <div class="card-body">
                            <h6 class="card-title mb-3 text-info">
                                <span class="fw-bold">Data Penilai</span>
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="text-muted small">Yang Menilai</div>
                                    <div class="fw-bold" id="view-penilai">-</div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="text-muted small">Atasan Yang Menilai</div>
                                    <div class="fw-bold" id="view-atasan-penilai">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteAssessmentModal" tabindex="-1" aria-labelledby="deleteAssessmentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus Penilaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus penilaian untuk <strong id="delete-assessment-nama"></strong>?
                    </p>
                    <p class="text-danger">Tindakan ini tidak dapat dibatalkan!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="delete-assessment-form" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-footer></x-footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).on('click', '.delete-assessment', function() {
                const assessmentId = $(this).data('assessment-id');
                const assessmentNama = $(this).data('assessment-nama');
                $('#delete-assessment-nama').text(assessmentNama);
                $('#delete-assessment-form').attr('action', `/assessment/${assessmentId}`);
                new bootstrap.Modal('#deleteAssessmentModal').show();
            });

            $(document).on('click', '.view-assessment', function() {
                const assessmentData = $(this).data('assessment-data');
                populateViewModal(assessmentData);
                new bootstrap.Modal('#viewAssessmentModal').show();
            });

            function populateViewModal(data) {
                $('#view-npk').text(data.npk);
                $('#view-nama').text(data.nama);
                $('#view-jabatan').text(data.jabatan);
                $('#view-golongan').text(data.golongan);
                $('#view-dept').text(data.dept);
                $('#view-seksi').text(data.seksi || '-');
                $('#view-sub-seksi').text(data.sub_seksi || '-');
                $('#view-periode').text(data.periode_penilaian);
                let tanggal = data.tanggal_penilaian;
                if (tanggal) {
                    if (tanggal.includes('T')) {
                        tanggal = tanggal.split('T')[0];
                    }
                    const [year, month, day] = tanggal.split('-');
                    $('#view-tanggal').text(`${day}/${month}/${year}`);
                } else {
                    $('#view-tanggal').text('-');
                }

                const nilaiMutu = data.nilai_mutu;
                const nilaiMutuClass = getNilaiMutuClass(nilaiMutu);
                $('#view-status').text(nilaiMutu).removeClass().addClass('fw-bold ' + nilaiMutuClass);

                $('#view-bobot-prestasi').text((data.bobot_prestasi * 100).toFixed(0) + '%');
                $('#view-bobot-non-prestasi').text((data.bobot_non_prestasi * 100).toFixed(0) + '%');
                $('#view-bobot-man').text((data.bobot_man_management * 100).toFixed(0) + '%');

                $('#view-kualitas').text(data.kualitas.toFixed(2));
                $('#view-kuantitas').text(data.kuantitas.toFixed(2));
                $('#view-rata-prestasi').text(data.rata_prestasi.toFixed(2));
                $('#view-sub-prestasi').text(data.sub_total_prestasi.toFixed(2));

                $('#view-kerjasama').text(data.kerjasama.toFixed(2));
                $('#view-inisiatif').text(data.inisiatif_kreatifitas.toFixed(2));
                $('#view-keandalan').text(data.keandalan_tanggung_jawab.toFixed(2));
                $('#view-disiplin').text(data.disiplin.toFixed(2));
                $('#view-integritas').text(data.integritas_loyalitas.toFixed(2));
                $('#view-qcc').text(data.qcc_ss.toFixed(2));
                $('#view-rata-nonprestasi').text(data.rata_non_prestasi.toFixed(2));
                $('#view-sub-nonprestasi').text(data.sub_total_non_prestasi.toFixed(2));

                $('#view-man-management').text(data.mengarahkan_menghargai.toFixed(2));
                $('#view-sub-man').text(data.sub_total_man_management.toFixed(2));

                $('#view-ijin').text(data.ijin || 0);
                $('#view-mangkir').text(data.mangkir || 0);
                $('#view-sp1').text(data.sp1 || 0);
                $('#view-sp2').text(data.sp2 || 0);
                $('#view-sp3').text(data.sp3 || 0);
                $('#view-demerit').text(data.demerit || 0);

                $('#view-nilai-total').text(data.nilai_total.toFixed(2));
                $('#view-nilai-akhir').text(data.nilai_akhir.toFixed(2));

                const nilaiMutuBadgeClass = getNilaiMutuClass(data.nilai_mutu);
                $('#view-nilai-mutu').text(data.nilai_mutu)
                    .removeClass()
                    .addClass('fw-bold ' + nilaiMutuBadgeClass);

                $('#view-kekuatan').html(data.kekuatan ?
                    '<div class="text-dark">' + data.kekuatan.replace(/\n/g, '<br>') + '</div>' :
                    '<i class="text-muted">Tidak ada data</i>');

                $('#view-kelemahan').html(data.kelemahan ?
                    '<div class="text-dark">' + data.kelemahan.replace(/\n/g, '<br>') + '</div>' :
                    '<i class="text-muted">Tidak ada data</i>');

                $('#view-penilai').text(data.yang_menilai || '-');
                $('#view-atasan-penilai').text(data.atasan_yang_menilai || '-');
            }

            function getNilaiMutuClass(nilaiMutu) {
                switch (nilaiMutu) {
                    case 'BS':
                        return 'nilai-mutu-BS';
                    case 'B':
                        return 'nilai-mutu-B';
                    case 'C':
                        return 'nilai-mutu-C';
                    case 'K':
                        return 'nilai-mutu-K';
                    case 'KS':
                        return 'nilai-mutu-KS';
                    default:
                        return 'text-secondary';
                }
            }
        });
    </script>
</body>

</html>
