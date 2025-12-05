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
                            <h6 class="mb-2 f-w-400 text-muted">Total Dokumen</h6>
                            {{-- <h4 class="mb-3">{{ number_format($totalDocuments) }}</h4> --}}
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card border border-success">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Dokumen Disetujui</h6>
                            {{-- <h4 class="mb-3">{{ number_format($approvedDocuments) }}</h4> --}}
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card border border-danger">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Dokumen Ditolak</h6>
                            {{-- <h4 class="mb-3">{{ number_format($rejectedDocuments) }}</h4> --}}
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card border border-warning">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Dokumen Menunggu Persetujuan</h6>
                            {{-- <h4 class="mb-3">{{ number_format($pendingDocuments) }}</h4> --}}
                        </div>
                    </div>
                </div>
                <!-- [ Statistik Kartu ] end -->
            </div>

            <!-- Grafik Dokumen & Distribusi -->
            <div class="row mt-4">
                <!-- Grafik Dokumen -->
                <div class="col-md-12 col-xl-8">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">Grafik Dokumen</h5>
                        <ul class="nav nav-pills justify-content-end mb-0" id="chart-tab-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="chart-tab-home-tab" data-bs-toggle="pill"
                                    data-bs-target="#chart-tab-home" type="button" role="tab"
                                    aria-controls="chart-tab-home" aria-selected="true">Bulanan</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="chart-tab-profile-tab" data-bs-toggle="pill"
                                    data-bs-target="#chart-tab-profile" type="button" role="tab"
                                    aria-controls="chart-tab-profile" aria-selected="false">Mingguan</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="tab-content" id="chart-tab-tabContent">
                                <div class="tab-pane" id="chart-tab-home" role="tabpanel"
                                    aria-labelledby="chart-tab-home-tab" tabindex="0">
                                    <canvas id="monthlyChart" style="height:300px;"></canvas>
                                </div>
                                <div class="tab-pane show active" id="chart-tab-profile" role="tabpanel"
                                    aria-labelledby="chart-tab-profile-tab" tabindex="0">
                                    <canvas id="weeklyChart" style="height:300px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Distribusi Dokumen Berdasarkan Departemen -->
                <div class="col-md-12 col-xl-4">
                    <h5 class="mb-3">Distribusi Dokumen per Departemen</h5>
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <canvas id="departmentChart" style="max-height:300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <x-footer></x-footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthlyData.labels,
                datasets: [{
                    label: 'Jumlah Dokumen',
                    data: monthlyData.data,
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

        const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
        new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: weeklyData.labels,
                datasets: [{
                    label: 'Jumlah Dokumen',
                    data: weeklyData.data,
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

        new Chart(document.getElementById("departmentChart"), {
            type: "doughnut",
            data: {
                labels: departmentData.labels,
                datasets: [{
                    data: departmentData.data,
                    backgroundColor: departmentData.colors,
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
                            padding: 20,
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
                                return `${label}: ${value} dokumen (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

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

</body>
<!-- [Body] end -->

</html>
