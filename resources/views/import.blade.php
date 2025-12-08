<!DOCTYPE html>
<html lang="en">
<x-head></x-head>

<body>
    @include('components.layout')

    <div class="pc-container">
        <div class="pc-content">
            <div class="row">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body py-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-0">
                                        <i class="ti ti-upload"></i>
                                        Import Data dari Excel
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

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Upload File Excel</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="ti ti-info-circle"></i> Format Excel Baru:</h6>
                                <p class="mb-2">Pastikan file Excel memiliki kolom dengan urutan berikut:</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>NO</th>
                                                <th>PERIODE</th>
                                                <th>NPK</th>
                                                <th>NAMA</th>
                                                <th>GOL</th>
                                                <th>DEPT</th>
                                                <th>NILAI</th>
                                                <th>GRADE</th>
                                                <th>SD + I</th>
                                                <th>M+ST</th>
                                                <th>SP I</th>
                                                <th>SP II</th>
                                                <th>SP III</th>
                                                <th>LATE</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Periode 1 | Okt 2024 - Mar 2025</td>
                                                <td>1592</td>
                                                <td>Saputra</td>
                                                <td>2</td>
                                                <td>MIS</td>
                                                <td></td>
                                                <td></td>
                                                <td>1</td>
                                                <td>1</td>
                                                <td>1</td>
                                                <td>1</td>
                                                <td>1</td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="mt-2 mb-0">
                                    <strong>Keterangan:</strong><br>
                                    - PERIODE: Pilih dari dropdown (Periode 1 atau Periode 2)<br>
                                    - SD + I = Ijin<br>
                                    - M+ST = Mangkir<br>
                                    - SP I, SP II, SP III = Surat Peringatan<br>
                                    - LATE = Keterlambatan
                                </p>
                            </div>

                            <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label for="excel_file" class="form-label">File Excel</label>
                                    <input type="file" class="form-control" id="excel_file" name="excel_file"
                                        accept=".xlsx,.xls,.csv" required>
                                    <div class="form-text">
                                        Gunakan template yang sudah disediakan atau format sesuai tabel di atas
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    <h6><i class="ti ti-alert-triangle"></i> Perhatian:</h6>
                                    <ul class="mb-0">
                                        <li>Kolom PERIODE harus diisi dengan pilihan dari dropdown</li>
                                        <li>Data karyawan yang sudah ada akan diupdate</li>
                                        <li>Data assessment akan dibuat/diupdate sesuai periode</li>
                                        <li>Nilai penilaian akan diisi dengan default 40</li>
                                    </ul>
                                </div>

                                <div class="text-end">
                                    <a href="{{ route('assessment') }}" class="btn btn-secondary me-2">
                                        <i class="ti ti-x"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-upload"></i> Import Data
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Template Download -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5>Download Template</h5>
                        </div>
                        <div class="card-body">
                            <p>Download template Excel dengan format yang benar dan dropdown untuk PERIODE:</p>
                            <a href="{{ route('import.template') }}" class="btn btn-success">
                                <i class="ti ti-download"></i> Download Template Excel Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-footer></x-footer>
</body>

</html>
