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
                                        <i class="ti ti-users"></i>
                                        Employee Management System
                                    </h5>
                                </div>
                                <div class="col-md-6 text-end">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#addEmployeeModal">
                                        <i class="ti ti-user-plus"></i> Tambah Employee Baru
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employees Table -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Daftar Employee
                                @if (request()->has('dept'))
                                    <small class="text-muted">(Hasil Filter)</small>
                                @endif
                            </h5>
                            <div>
                                @if (request()->has('dept'))
                                    <span class="badge bg-info me-2">Data Difilter</span>
                                @endif
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-filter"></i> Filter Department
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('employee') }}">Semua</a>
                                        <div class="dropdown-divider"></div>
                                        <h6 class="dropdown-header">Department</h6>
                                        @foreach ($departments as $dept)
                                            <a class="dropdown-item"
                                                href="{{ request()->fullUrlWithQuery(['dept' => $dept]) }}">
                                                {{ $dept }}
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

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
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
                                            <th class="text-center">Department</th>
                                            <th class="text-center">Seksi</th> 
                                            <th class="text-center">Sub_Seksi</th> 
                                            <th class="text-center">Jabatan</th>
                                            <th class="text-center">Golongan</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $index => $user)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td class="text-center">{{ $user->npk }}</td>
                                                <td class="text-center">
                                                    <strong>{{ $user->nama }}</strong>
                                                </td>
                                                <td class="text-center">{{ $user->dept }}</span>
                                                </td>
                                                <td class="text-center">{{ $user->seksi }}</span>
                                                </td>
                                                <td class="text-center">{{ $user->sub_seksi }}</span>
                                                </td>
                                                <td class="text-center">{{ $user->jabatan ?? '-' }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary">{{ $user->golongan ?? '-' }}</td> </span>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center flex-wrap gap-2">
                                                        <!-- Edit -->
                                                        <div class="text-center action-item" style="width:50px;">
                                                            <button type="button"
                                                                class="btn p-0 border-0 bg-transparent edit-employee"
                                                                data-employee-id="{{ $user->id }}"
                                                                data-employee-npk="{{ $user->npk }}"
                                                                data-employee-nama="{{ $user->nama }}"
                                                                data-employee-dept="{{ $user->dept }}"
                                                                data-employee-seksi="{{ $user->seksi }}"
                                                                data-employee-sub_seksi="{{ $user->sub_seksi }}"
                                                                data-employee-jabatan="{{ $user->jabatan }}"
                                                                data-employee-golongan="{{ $user->golongan }}"
                                                                title="Edit">
                                                                <i class="bi bi-pencil fs-4 text-warning"></i>
                                                            </button>
                                                            <div class="small text-muted" style="font-size: 11px;">Edit
                                                            </div>
                                                        </div>

                                                        <!-- Delete -->
                                                        <div class="text-center action-item" style="width:50px;">
                                                            <button type="button"
                                                                class="btn p-0 border-0 bg-transparent delete-employee"
                                                                data-employee-id="{{ $user->id }}"
                                                                data-employee-nama="{{ $user->nama }}"
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
                                                <td colspan="5" class="text-center">Tidak ada data employee.</td>
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
    <div class="modal fade" id="deleteEmployeeModal" tabindex="-1" aria-labelledby="deleteEmployeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus employee <strong id="delete-employee-nama"></strong>?</p>
                    <p class="text-danger">Tindakan ini tidak dapat dibatalkan!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="delete-employee-form" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('employee.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Employee Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="npk" class="form-label">NPK *</label>
                            <input type="text" class="form-control" id="npk" name="npk" required
                                placeholder="Masukkan NPK">
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama *</label>
                            <input type="text" class="form-control" id="nama" name="nama" required
                                placeholder="Masukkan nama lengkap">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required
                                placeholder="example@company.com">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control" id="password" name="password" required
                                placeholder="Minimal 6 karakter">
                        </div>
                        <div class="mb-3">
                            <label for="dept" class="form-label">Department *</label>
                            <select class="form-control" id="dept" name="dept" required>
                                <option value="">Pilih Department</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="seksi" class="form-label">Seksi *</label>
                            <input type="text" class="form-control" id="seksi" name="seksi" required
                                placeholder="Masukkan seksi">
                        </div>
                        <div class="mb-3">
                            <label for="sub_seksi" class="form-label">Sub Seksi *</label>
                            <input type="text" class="form-control" id="sub_seksi" name="sub_seksi" required
                                placeholder="Masukkan sub seksi">
                        </div>
                        <div class="mb-3">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" class="form-control" id="jabatan" name="jabatan"
                                placeholder="Masukkan jabatan">
                        </div>
                        <div class="mb-3">
                            <label for="golongan" class="form-label">Golongan</label>
                            <input type="text" class="form-control" id="golongan" name="golongan"
                                placeholder="Masukkan golongan">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="edit-employee-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_npk" class="form-label">NPK *</label>
                            <input type="text" class="form-control" id="edit_npk" name="npk" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nama" class="form-label">Nama *</label>
                            <input type="text" class="form-control" id="edit_nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_dept" class="form-label">Department *</label>
                            <select class="form-control" id="edit_dept" name="dept" required>
                                <option value="">Pilih Department</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_seksi" class="form-label">Seksi</label>
                            <input type="text" class="form-control" id="edit_seksi" name="seksi"
                                placeholder="Masukkan jabatan">
                        </div>
                        <div class="mb-3">
                            <label for="edit_sub_seksi" class="form-label">Sub Seksi</label>
                            <input type="text" class="form-control" id="edit_sub_seksi" name="sub_seksi"
                                placeholder="Masukkan jabatan">
                        </div>
                        <div class="mb-3">
                            <label for="edit_jabatan" class="form-label">Jabatan</label>
                            <input type="text" class="form-control" id="edit_jabatan" name="jabatan"
                                placeholder="Masukkan jabatan">
                        </div>
                        <div class="mb-3">
                            <label for="edit_golongan" class="form-label">Golongan</label>
                            <input type="text" class="form-control" id="edit_golongan" name="golongan"
                                placeholder="Masukkan golongan">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-footer></x-footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).on('click', '.delete-employee', function() {
                const employeeId = $(this).data('employee-id');
                const employeeNama = $(this).data('employee-nama');
                $('#delete-employee-nama').text(employeeNama);
                $('#delete-employee-form').attr('action', `/employee/${employeeId}`);
                new bootstrap.Modal('#deleteEmployeeModal').show();
            });

            $(document).on('click', '.edit-employee', function() {
                const employeeId = $(this).data('employee-id');
                const npk = $(this).data('employee-npk');
                const nama = $(this).data('employee-nama');
                const dept = $(this).data('employee-dept');
                const seksi = $(this).data('employee-seksi');
                const sub_seksi = $(this).data('employee-sub_seksi');
                const jabatan = $(this).data('employee-jabatan') || '';
                const golongan = $(this).data('employee-golongan') || '';

                $('#edit_npk').val(npk);
                $('#edit_nama').val(nama);
                $('#edit_dept').val(dept);
                $('#edit_seksi').val(seksi);
                $('#edit_sub_seksi').val(sub_seksi);
                $('#edit_jabatan').val(jabatan);
                $('#edit_golongan').val(golongan);
                $('#edit-employee-form').attr('action', `/employee/${employeeId}`);

                new bootstrap.Modal('#editEmployeeModal').show();
            });


            ['deleteEmployeeModal', 'editEmployeeModal', 'addEmployeeModal'].forEach(id => {
                const modalEl = document.getElementById(id);
                if (modalEl) {
                    modalEl.addEventListener('hidden.bs.modal', function() {
                        if (id === 'editEmployeeModal') {
                            $('#edit-employee-form').attr('action', '');
                        }
                        if (id === 'deleteEmployeeModal') {
                            $('#delete-employee-form').attr('action', '');
                        }
                        if (id === 'addEmployeeModal') {
                            modalEl.querySelector('form')?.reset();
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>
