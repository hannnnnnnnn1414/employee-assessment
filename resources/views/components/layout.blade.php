<!-- [ Pre-loader ] start -->
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>
<!-- [ Pre-loader ] End -->

<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header" style="display: flex; align-items: center; justify-content: center; height: 60px;">
            <a href="{{ route('dashboard') }}" class="pc-link" id="dashboard-link">
                <img src="{{ asset('img/logo-kayaba.png') }}" alt="logo" style="max-width: 80px; height: auto;">
            </a>
        </div>

        <div class="navbar-content">
            <ul class="pc-navbar" style="padding-left: 0; margin-left: 0;">
                <li class="pc-item">
                    <a href="{{ route('dashboard') }}" class="pc-link" id="dashboard-link">
                        <span class="bi bi-kanban pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>
                @if (Auth::guard('lembur')->user()->dept == 'HRD')
                    <li class="pc-item">
                        <a href="{{ route('employee') }}" class="pc-link" id="dashboard-link">
                            <span class="bi bi-person-fill pc-micon"><i class="ti ti-dashboard"></i></span>
                            <span class="pc-mtext">Employee</span>
                        </a>
                    </li>
                @endif
                <li class="pc-item">
                    <a href="{{ route('assessment') }}" class="pc-link" id="dashboard-link">
                        <span class="bi bi-card-list pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">Assessment</span>
                    </a>
                </li>
                @if (Auth::guard('lembur')->user()->dept == 'HRD')
                    <li class="pc-item">
                        <a href="{{ route('import') }}" class="pc-link" id="dashboard-link">
                            <span class="bi bi-upload pc-micon"><i class="ti ti-dashboard"></i></span>
                            <span class="pc-mtext">Import Data</span>
                        </a>
                    </li>
                @endif
                <li class="pc-item">
                    <a href="#" class="pc-link" id="logout-link" style="color: red; cursor: pointer;">
                        <span class="bi bi-box-arrow-left text-danger pc-micon"><i class="ti ti-power"></i></span>
                        <span class="pc-mtext" style="color:red">Logout</span>
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- [ Sidebar Menu ] end -->

<!-- [ Header Topbar ] start -->
<header class="pc-header">
    <div class="header-wrapper">
        <!-- [Mobile Media Block] start -->
        <!-- [ Breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10" id="page-title">Employee Assessment System</h5>
                        </div>
                        {{-- <ul class="breadcrumb" id="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item" id="current-page">Dashboard</li>
                        </ul> --}}
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Breadcrumb ] end -->

        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="list-unstyled">
                <li class="dropdown pc-h-item header-user-profile">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="fas fa-user" style="font-size: 1rem; margin-right: 8px;"></i>
                        <span>{{ Auth::guard('lembur')->user()->full_name ?? 'Guest' }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex mb-1">
                                <div class="flex-grow-1 ms-3">
                                    <h6>{{ Auth::guard('lembur')->user()->full_name ?? 'Guest' }}</h6>
                                    <span>{{ Auth::guard('lembur')->user()->npk ?? '' }} |
                                        {{ Auth::guard('lembur')->user()->dept ?? '' }}</span>
                                </div>
                                <a href="#!" class="pc-head-link bg-transparent"><i
                                        class="ti ti-power text-danger"></i></a>
                            </div>
                        </div>
                        {{-- <div class="tab-content" id="mysrpTabContent">
                            <div class="tab-pane fade show active" id="drp-tab-1" role="tabpanel"
                                aria-labelledby="drp-t1" tabindex="0">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"
                                        style="border: none; background: none; width: 100%; text-align: left;">
                                        <i class="ti ti-power text-danger me-2"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div> --}}
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
<!-- [ Header ] end -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('logout-link').addEventListener('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Yakin mau logout?',
            text: "Sesi kamu akan diakhiri dan harus login lagi nanti.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    });
</script>
