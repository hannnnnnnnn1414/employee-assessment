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
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-filter"></i> Filter Periode
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('assessment') }}">Semua</a>
                                        <div class="dropdown-divider"></div>
                                        <h6 class="dropdown-header">Periode</h6>
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
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($assessments as $index => $assessment)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td class="text-center">{{ $assessment->npk }}</td>
                                                <td class="text-center">
                                                    <strong>{{ $assessment->nama }}</strong>
                                                </td>
                                                <td class="text-center">{{ $assessment->jabatan }}</td>
                                                <td class="text-center">{{ $assessment->dept_seksi }}</td>
                                                <td class="text-center">{{ $assessment->periode_penilaian }}</td>
                                                <td class="text-center">{{ $assessment->tanggal_penilaian }}</td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-primary">{{ $assessment->nilai_akhir }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-{{ $assessment->nilai_mutu == 'BS' ? 'success' : ($assessment->nilai_mutu == 'B' ? 'info' : ($assessment->nilai_mutu == 'C' ? 'warning' : 'danger')) }}">
                                                        {{ $assessment->nilai_mutu }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center flex-wrap gap-2">
                                                        <!-- View -->
                                                        <div class="text-center action-item" style="width:50px;">
                                                            <a href="{{ route('assessment.show', $assessment->id) }}"
                                                                class="btn p-0 border-0 bg-transparent"
                                                                title="Lihat Detail">
                                                                <i class="bi bi-eye fs-4 text-secondary"></i>
                                                            </a>
                                                            <div class="small text-muted" style="font-size: 11px;">View
                                                            </div>
                                                        </div>

                                                        <!-- Edit -->
                                                        <div class="text-center action-item" style="width:50px;">
                                                            <a href="{{ route('assessment.edit', $assessment->id) }}"
                                                                class="btn p-0 border-0 bg-transparent" title="Edit">
                                                                <i class="bi bi-pencil fs-4 text-warning"></i>
                                                            </a>
                                                            <div class="small text-muted" style="font-size: 11px;">Edit
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
                                                            <div class="small text-muted" style="font-size: 11px;">Hapus
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-4">
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
            // === DELETE ASSESSMENT ===
            $(document).on('click', '.delete-assessment', function() {
                const assessmentId = $(this).data('assessment-id');
                const assessmentNama = $(this).data('assessment-nama');
                $('#delete-assessment-nama').text(assessmentNama);
                $('#delete-assessment-form').attr('action', `/assessment/${assessmentId}`);
                new bootstrap.Modal('#deleteAssessmentModal').show();
            });
        });
    </script>
</body>

</html>
