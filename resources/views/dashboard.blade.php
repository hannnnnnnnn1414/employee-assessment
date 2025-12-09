<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<!-- [Body] Start -->

<body>
    <!-- [ Layout ] start -->
    @include('components.layout')
    <!-- [ Layout ] end -->

    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">

            <!-- [ Main Content ] start -->
            <div class="row">
                <!-- [ Statistik Kartu ] start -->
                <div class="col-md-6 col-xl-3">
                    <div class="card border border-primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="ti ti-clipboard text-primary" style="font-size: 2.5rem;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 f-w-400 text-muted">Total Penilaian</h6>
                                    <h4 class="mb-0">{{ number_format($totalAssessments) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card border border-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="ti ti-users text-success" style="font-size: 2.5rem;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 f-w-400 text-muted">Total Karyawan</h6>
                                    <h4 class="mb-0">{{ number_format($totalEmployees) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card border border-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="ti ti-chart-bar text-info" style="font-size: 2.5rem;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 f-w-400 text-muted">Rata-rata Nilai</h6>
                                    <h4 class="mb-0">{{ $avgNilai }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card border border-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="ti ti-calendar text-warning" style="font-size: 2.5rem;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 f-w-400 text-muted">Penilaian Terbaru</h6>
                                    <h4 class="mb-0">{{ $recentAssessments->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ Statistik Kartu ] end -->
            </div>

            <!-- Grafik & Distribusi -->
            <div class="row mt-4">
                <!-- Grafik Bulanan -->
                <div class="col-md-12 col-xl-8">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">Statistik Penilaian {{ date('Y') }}</h5>
                        <ul class="nav nav-pills justify-content-end mb-0" id="chart-tab-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="chart-tab-home-tab" data-bs-toggle="pill"
                                    data-bs-target="#chart-tab-home" type="button" role="tab"
                                    aria-controls="chart-tab-home" aria-selected="true">Bulanan</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="chart-tab-profile-tab" data-bs-toggle="pill"
                                    data-bs-target="#chart-tab-profile" type="button" role="tab"
                                    aria-controls="chart-tab-profile" aria-selected="false">Mingguan</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="tab-content" id="chart-tab-tabContent">
                                <div class="tab-pane show active" id="chart-tab-home" role="tabpanel"
                                    aria-labelledby="chart-tab-home-tab" tabindex="0">
                                    <canvas id="monthlyChart" style="height:300px;"></canvas>
                                </div>
                                <div class="tab-pane" id="chart-tab-profile" role="tabpanel"
                                    aria-labelledby="chart-tab-profile-tab" tabindex="0">
                                    <canvas id="weeklyChart" style="height:300px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Distribusi Nilai Mutu -->
                <div class="col-md-12 col-xl-4">
                    <h5 class="mb-3">Distribusi Nilai Mutu</h5>
                    <div class="card h-100">
                        <div class="card-body">
                            @if (!empty($nilaiMutuDistribution['labels']))
                                <canvas id="nilaiMutuChart" style="max-height:250px;"></canvas>
                                <div class="mt-3">
                                    @foreach ($nilaiMutuDistribution['labels'] as $index => $label)
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge me-2"
                                                style="background-color: {{ $nilaiMutuDistribution['colors'][$index] }}; width: 15px; height: 15px;"></span>
                                            <span class="small">{{ $label }}:
                                                {{ $nilaiMutuDistribution['data'][$index] }} penilaian</span>
                                            <span
                                                class="ms-auto small text-muted">{{ $nilaiMutuDistribution['descriptions'][$label] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="ti ti-chart-pie" style="font-size: 48px; color: #6c757d;"></i>
                                    <p class="mt-2 text-muted">Belum ada data penilaian</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel & Informasi Tambahan -->
            <div class="row mt-5">
                <!-- Penilaian Terbaru -->
                <div class="col-md-12 col-xl-6">
                    <h5 class="mb-3">Penilaian Terbaru</h5>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Nama</th>
                                            <th class="text-center">Nilai</th>
                                            <th class="text-center">Mutu</th>
                                            <th class="text-center">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentAssessments as $assessment)
                                            <tr>
                                                <td class="text-center">
                                                    <strong>{{ $assessment->nama }}</strong>
                                                    <div class="small text-muted">{{ $assessment->dept_seksi }}</div>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-primary">{{ $assessment->nilai_akhir }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $mutuColor =
                                                            [
                                                                'BS' => 'success',
                                                                'B' => 'info',
                                                                'C' => 'warning',
                                                                'K' => 'warning',
                                                                'KS' => 'danger',
                                                            ][$assessment->nilai_mutu] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge bg-{{ $mutuColor }}">
                                                        {{ $assessment->nilai_mutu }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($assessment->tanggal_penilaian)->format('d/m/Y') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    Belum ada data penilaian
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Performers & Perlu Perbaikan -->
                <div class="col-md-12 col-xl-6">
                    <div class="row">
                        <!-- Top Performers -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Top Performers</h5>
                            <div class="card">
                                <div class="card-body">
                                    @forelse($topPerformers as $performer)
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm">
                                                    <div
                                                        class="avatar-title bg-light-primary text-primary rounded-circle">
                                                        {{ substr($performer->nama, 0, 1) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0">{{ $performer->nama }}</h6>
                                                <p class="mb-0 small text-muted">{{ $performer->jabatan }}</p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <span class="badge bg-success">{{ $performer->nilai_akhir }}</span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-3 text-muted">
                                            Belum ada data
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Perlu Perbaikan -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Perlu Perbaikan</h5>
                            <div class="card">
                                <div class="card-body">
                                    @forelse($needImprovement as $employee)
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm">
                                                    <div
                                                        class="avatar-title bg-light-warning text-warning rounded-circle">
                                                        {{ substr($employee->nama, 0, 1) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0">{{ $employee->nama }}</h6>
                                                <p class="mb-0 small text-muted">{{ $employee->jabatan }}</p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                @php
                                                    $badgeColor = $employee->nilai_mutu == 'KS' ? 'danger' : 'warning';
                                                @endphp
                                                <span
                                                    class="badge bg-{{ $badgeColor }}">{{ $employee->nilai_akhir }}</span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-3 text-muted">
                                            Tidak ada data
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Departemen Distribution (HR only) -->
                    @if ($userDept === 'HR' && !empty($departmentChartData))
                        <div class="mt-4">
                            <h5 class="mb-3">Distribusi per Departemen</h5>
                            <div class="card">
                                <div class="card-body">
                                    <canvas id="departmentChart" style="max-height:200px;"></canvas>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <x-footer></x-footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Monthly Chart
        @if (!empty($monthlyChartData['labels']))
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: @json($monthlyChartData['labels']),
                    datasets: [{
                        label: 'Jumlah Penilaian',
                        data: @json($monthlyChartData['data']),
                        backgroundColor: 'rgba(13, 110, 253, 0.8)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        @endif

        // Weekly Chart
        @if (!empty($weeklyChartData['labels']))
            const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
            new Chart(weeklyCtx, {
                type: 'line',
                data: {
                    labels: @json($weeklyChartData['labels']),
                    datasets: [{
                        label: 'Jumlah Penilaian',
                        data: @json($weeklyChartData['data']),
                        backgroundColor: 'rgba(25, 135, 84, 0.2)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        @endif

        // Nilai Mutu Chart
        @if (!empty($nilaiMutuDistribution['labels']))
            new Chart(document.getElementById("nilaiMutuChart"), {
                type: "doughnut",
                data: {
                    labels: @json($nilaiMutuDistribution['labels']),
                    datasets: [{
                        data: @json($nilaiMutuDistribution['data']),
                        backgroundColor: @json($nilaiMutuDistribution['colors']),
                        borderColor: "transparent",
                        borderWidth: 2
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: {
                                padding: 15,
                                boxWidth: 15,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        @endif

        // Department Chart (HR only)
        @if ($userDept === 'HR' && !empty($departmentChartData))
            new Chart(document.getElementById("departmentChart"), {
                type: "pie",
                data: {
                    labels: @json($departmentChartData['labels']),
                    datasets: [{
                        data: @json($departmentChartData['data']),
                        backgroundColor: @json($departmentChartData['colors']),
                        borderColor: "transparent",
                        borderWidth: 2
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: {
                                padding: 15,
                                boxWidth: 15,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} penilaian (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        @endif

        // Tab switching
        document.addEventListener('DOMContentLoaded', function() {
            const triggerTabList = [].slice.call(document.querySelectorAll('#chart-tab-tab button'))
            triggerTabList.forEach(function(triggerEl) {
                const tabTrigger = new bootstrap.Tab(triggerEl)
                triggerEl.addEventListener('click', function(event) {
                    event.preventDefault()
                    tabTrigger.show()
                })
            })
        });
    </script>

    <style>
        .avatar-sm {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-title {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
    </style>

</body>
<!-- [Body] end -->

</html>
