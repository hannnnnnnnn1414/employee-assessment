<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<style>
    .text-purple { color: #6f42c1 !important; }
    .nilai-mutu-BS { color: #198754; font-weight: bold; }
    .nilai-mutu-B  { color: #0dcaf0; font-weight: bold; }
    .nilai-mutu-C  { color: #fd7e14;  font-weight: bold; }
    .nilai-mutu-K  { color: #ffc107; font-weight: bold; }
    .nilai-mutu-KS { color: #dc3545; font-weight: bold; }

    .section-prestasi    { background-color: rgba(25, 135, 84, 0.05); border-left: 5px solid #198754; }
    .section-nonprestasi { background-color: rgba(253, 126, 20, 0.05); border-left: 5px solid #fd7e14; }
    .section-man         { background-color: rgba(220, 53, 69, 0.05); border-left: 5px solid #dc3545; }
    .section-total       { background-color: rgba(13, 110, 253, 0.08); border-left: 5px solid #0d6efd; }

    .badge-nilai-mutu-BS { background-color: #d4edda; color: #155724; font-size: 1.2rem; padding: 0.5rem 1.2rem; }
    .badge-nilai-mutu-B  { background-color: #cff4fc; color: #055160; font-size: 1.2rem; padding: 0.5rem 1.2rem; }
    .badge-nilai-mutu-C  { background-color: #fff3cd; color: #856404; font-size: 1.2rem; padding: 0.5rem 1.2rem; }
    .badge-nilai-mutu-K  { background-color: #ffeeba; color: #856404; font-size: 1.2rem; padding: 0.5rem 1.2rem; }
    .badge-nilai-mutu-KS { background-color: #f8d7da; color: #721c24; font-size: 1.2rem; padding: 0.5rem 1.2rem; }

    @media (max-width: 992px) {
        .card-employee, .card-assessment {
            margin-bottom: 1.5rem !important;
        }
    }
</style>

<body>
    @include('components.layout')

    <div class="pc-container">
        <div class="pc-content">

            <div class="row">
                <div class="col-12">

                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                        <div>
                            <h4 class="mb-1">
                                <i class="ti ti-user-check text-primary me-2"></i>
                                Detail Penilaian Karyawan
                            </h4>
                            <small class="text-muted">{{ $assessment->periode_penilaian }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('assessment') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-arrow-back-up me-1"></i> Kembali
                            </a>
                            <a href="{{ route('assessment.edit', $assessment->id) }}" class="btn btn-warning">
                                <i class="ti ti-pencil me-1"></i> Edit
                            </a>
                        </div>
                    </div>

                    <div class="row g-4">

                        <!-- Data Karyawan -->
                        <div class="col-lg-5">
                            <div class="card border-0 shadow-sm h-100 card-employee">
                                <div class="card-header bg-light border-bottom">
                                    <h5 class="mb-0 text-dark">
                                        <i class="ti ti-id-badge me-2 text-primary"></i>
                                        Informasi Karyawan
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="text-muted small">NPK</div>
                                            <div class="fw-bold fs-5">{{ $assessment->npk }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Golongan</div>
                                            <div class="fw-bold fs-5 text-warning">{{ $assessment->golongan }}</div>
                                        </div>

                                        <div class="col-12">
                                            <div class="text-muted small">Nama Lengkap</div>
                                            <div class="fw-bold fs-4">{{ $assessment->nama }}</div>
                                        </div>

                                        <div class="col-12">
                                            <div class="text-muted small">Jabatan</div>
                                            <div class="fw-bold fs-5 text-info">{{ $assessment->jabatan }}</div>
                                        </div>

                                        <div class="col-6">
                                            <div class="text-muted small">Departemen</div>
                                            <div class="fw-bold">{{ $assessment->dept }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Seksi</div>
                                            <div class="fw-bold">{{ $assessment->seksi ?? '-' }}</div>
                                        </div>

                                        <div class="col-6">
                                            <div class="text-muted small">Sub Seksi</div>
                                            <div class="fw-bold">{{ $assessment->sub_seksi ?? '-' }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Tanggal Penilaian</div>
                                            <div class="fw-bold">
                                                {{ $assessment->tanggal_penilaian 
                                                    ? \Carbon\Carbon::parse($assessment->tanggal_penilaian)->format('d F Y') 
                                                    : '-' }}
                                            </div>
                                        </div>

                                        <div class="col-12 mt-3">
                                            <div class="text-muted small">Status Penilaian</div>
                                            @if($assessment->status === 'completed')
                                                <span class="badge bg-success px-3 py-2 fs-6">Selesai Dinilai</span>
                                            @else
                                                <span class="badge bg-warning px-3 py-2 fs-6">Draft / Belum Dinilai</span>
                                            @endif
                                        </div>

                                        <div class="col-12 mt-3 border-top pt-3">
                                            <div class="text-muted small">Yang Menilai</div>
                                            <div class="fw-bold">{{ $assessment->yang_menilai ?? '-' }}</div>
                                        </div>
                                        <div class="col-12">
                                            <div class="text-muted small">Atasan yang Menilai</div>
                                            <div class="fw-bold">{{ $assessment->atasan_yang_menilai ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Penilaian -->
                        <div class="col-lg-7">
                            <div class="card border-0 shadow-sm h-100 card-assessment">
                                <div class="card-header bg-light border-bottom">
                                    <h5 class="mb-0 text-dark">
                                        <i class="ti ti-chart-bar me-2 text-primary"></i>
                                        Hasil Penilaian
                                    </h5>
                                </div>
                                <div class="card-body">

                                    <!-- Prestasi -->
                                    <div class="section-prestasi p-3 rounded mb-4">
                                        <h6 class="text-success fw-bold mb-3">
                                            A. PRESTASI
                                            <small class="float-end text-muted">Bobot: {{ number_format($assessment->bobot_prestasi * 100) }}%</small>
                                        </h6>
                                        <div class="row g-2 small">
                                            <div class="col-6">Kualitas</div><div class="col-6 text-end fw-bold text-success">{{ number_format($assessment->kualitas, 2) }}</div>
                                            <div class="col-6">Kuantitas</div><div class="col-6 text-end fw-bold text-success">{{ number_format($assessment->kuantitas, 2) }}</div>
                                            <div class="col-12"><hr class="my-1"></div>
                                            <div class="col-6 fw-bold">Rata-rata Prestasi</div><div class="col-6 text-end fw-bold">{{ number_format($assessment->rata_prestasi, 2) }}</div>
                                            <div class="col-6 fw-bold">Sub Total</div><div class="col-6 text-end"><h6 class="text-success mb-0">{{ number_format($assessment->sub_total_prestasi, 2) }}</h6></div>
                                        </div>
                                    </div>

                                    <!-- Non Prestasi -->
                                    <div class="section-nonprestasi p-3 rounded mb-4">
                                        <h6 class="text-warning fw-bold mb-3">
                                            B. NON PRESTASI
                                            <small class="float-end text-muted">Bobot: {{ number_format($assessment->bobot_non_prestasi * 100) }}%</small>
                                        </h6>
                                        <div class="row g-2 small">
                                            <div class="col-6">Kerjasama</div><div class="col-6 text-end fw-bold text-warning">{{ number_format($assessment->kerjasama, 2) }}</div>
                                            <div class="col-6">Inisiatif & Kreativitas</div><div class="col-6 text-end fw-bold text-warning">{{ number_format($assessment->inisiatif_kreatifitas, 2) }}</div>
                                            <div class="col-6">Keandalan / Tanggung Jawab</div><div class="col-6 text-end fw-bold text-warning">{{ number_format($assessment->keandalan_tanggung_jawab, 2) }}</div>
                                            <div class="col-6">Disiplin</div><div class="col-6 text-end fw-bold text-warning">{{ number_format($assessment->disiplin, 2) }}</div>
                                            <div class="col-6">Integritas / Loyalitas</div><div class="col-6 text-end fw-bold text-warning">{{ number_format($assessment->integritas_loyalitas, 2) }}</div>
                                            <div class="col-6">QCC & SS</div><div class="col-6 text-end fw-bold text-warning">{{ number_format($assessment->qcc_ss, 2) }}</div>
                                            <div class="col-12"><hr class="my-1"></div>
                                            <div class="col-6 fw-bold">Rata-rata</div><div class="col-6 text-end fw-bold">{{ number_format($assessment->rata_non_prestasi, 2) }}</div>
                                            <div class="col-6 fw-bold">Sub Total</div><div class="col-6 text-end"><h6 class="text-warning mb-0">{{ number_format($assessment->sub_total_non_prestasi, 2) }}</h6></div>
                                        </div>
                                    </div>

                                    <!-- Man Management -->
                                    <div class="section-man p-3 rounded mb-4">
                                        <h6 class="text-danger fw-bold mb-3">
                                            C. MAN MANAGEMENT
                                            <small class="float-end text-muted">Bobot: {{ number_format($assessment->bobot_man_management * 100) }}%</small>
                                        </h6>
                                        <div class="row g-2 small">
                                            <div class="col-6">Mengarahkan & Menghargai</div>
                                            <div class="col-6 text-end fw-bold text-danger">{{ number_format($assessment->mengarahkan_menghargai, 2) }}</div>
                                            <div class="col-12"><hr class="my-1"></div>
                                            <div class="col-6 fw-bold">Sub Total</div>
                                            <div class="col-6 text-end"><h6 class="text-danger mb-0">{{ number_format($assessment->sub_total_man_management, 2) }}</h6></div>
                                        </div>
                                    </div>

                                    <!-- Demerit & Total -->
                                    <div class="section-total p-3 rounded mb-4">
                                        <h6 class="text-primary fw-bold mb-3">TOTAL & DEMERIT</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Nilai Total (A+B+C)</span>
                                                    <strong class="text-primary">{{ number_format($assessment->nilai_total, 2) }}</strong>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Demerit</span>
                                                    <strong class="text-danger">{{ $assessment->demerit }}</strong>
                                                </div>
                                                <hr class="my-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold fs-5">Nilai Akhir</span>
                                                    <h4 class="text-success mb-0">{{ number_format($assessment->nilai_akhir, 2) }}</h4>
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-center">
                                                <div class="mt-2">
                                                    <span class="badge {{ match($assessment->nilai_mutu) {
                                                        'BS' => 'badge-nilai-mutu-BS',
                                                        'B'  => 'badge-nilai-mutu-B',
                                                        'C'  => 'badge-nilai-mutu-C',
                                                        'K'  => 'badge-nilai-mutu-K',
                                                        'KS' => 'badge-nilai-mutu-KS',
                                                        default => 'bg-secondary'
                                                    } }}">
                                                        {{ $assessment->nilai_mutu }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kekuatan & Kelemahan -->
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 bg-white">
                                                <h6 class="text-success fw-bold mb-2">Kekuatan</h6>
                                                <div class="small text-dark">
                                                    {!! nl2br(e($assessment->kekuatan ?? 'Tidak ada catatan')) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 bg-white">
                                                <h6 class="text-warning fw-bold mb-2">Kelemahan</h6>
                                                <div class="small text-dark">
                                                    {!! nl2br(e($assessment->kelemahan ?? 'Tidak ada catatan')) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-footer></x-footer>
</body>
</html>