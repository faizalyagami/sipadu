<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Pelayanan Surat Terpadu - @yield('title')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-purple: #6f42c1;
            --secondary-purple: #8b5cf6;
            --dark-purple: #5a32a3;
            --light-purple: #e9d5ff;
            --bg-purple: #f8f4ff;
            --gradient-purple: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            --shadow-purple: 0 4px 15px rgba(111, 66, 193, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--bg-purple);
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ========== NAVBAR ========== */
        .navbar {
            background: var(--gradient-purple);
            box-shadow: var(--shadow-purple);
            padding: 0.7rem 1.5rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.1rem;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .navbar-brand i {
            font-size: 1.4rem;
        }

        .navbar .dropdown-toggle::after {
            display: none;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }

        .user-avatar i {
            font-size: 1.2rem;
            color: white;
        }

        .navbar .btn-link {
            color: white !important;
        }

        .navbar .btn-link:hover {
            opacity: 0.8;
        }

        /* ========== SIDEBAR ========== */
        .sidebar {
            background: white;
            width: 270px;
            position: fixed;
            top: 60px;
            left: 0;
            bottom: 0;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            z-index: 1020;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(111, 66, 193, 0.08);
        }

        .sidebar-header {
            padding: 1.5rem 1.25rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(111, 66, 193, 0.1);
        }

        .logo-container {
            width: 75px;
            height: 75px;
            margin: 0 auto 12px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--light-purple);
            padding: 5px;
            background: white;
            box-shadow: var(--shadow-purple);
        }

        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .sidebar-header h5 {
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 2px;
            color: var(--dark-purple);
        }

        .sidebar-header small {
            font-size: 0.65rem;
            color: #718096;
            font-weight: 500;
        }

        .nav-section {
            padding: 0.8rem 1.25rem 0.3rem;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #a0aec0;
        }

        .sidebar .nav-link {
            padding: 0.6rem 1rem;
            margin: 0.2rem 0.8rem;
            border-radius: 10px;
            color: #4a5568;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            font-weight: 500;
            font-size: 0.85rem;
            position: relative;
        }

        .sidebar .nav-link i {
            width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover {
            background: var(--light-purple);
            color: var(--primary-purple);
            transform: translateX(4px);
        }

        .sidebar .nav-link:hover i {
            transform: scale(1.1);
        }

        .sidebar .nav-link.active {
            background: var(--gradient-purple);
            color: white;
            box-shadow: var(--shadow-purple);
        }

        .sidebar .nav-link.active i {
            color: white;
        }

        .sidebar .nav-link .badge {
            margin-left: auto;
            background: #dc3545;
            color: white;
            font-size: 0.6rem;
            padding: 0.2rem 0.5rem;
            border-radius: 20px;
        }

        .sidebar-footer {
            padding: 0.8rem;
            border-top: 1px solid rgba(111, 66, 193, 0.08);
            text-align: center;
            font-size: 0.6rem;
            color: #a0aec0;
            margin-top: auto;
        }

        .sidebar-footer i {
            color: var(--primary-purple);
        }

        /* Scrollbar Sidebar */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--light-purple);
            border-radius: 10px;
        }

        /* ========== MAIN CONTENT ========== */
        .main-content {
            margin-left: 270px;
            margin-top: 60px;
            padding: 1.5rem 2rem;
            min-height: calc(100vh - 60px);
            transition: all 0.3s ease;
        }

        /* ========== CARDS ========== */
        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: all 0.25s ease;
            background: white;
            margin-bottom: 1.25rem;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid rgba(111, 66, 193, 0.08);
            padding: 1rem 1.25rem;
            border-radius: 14px 14px 0 0 !important;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
            font-size: 0.95rem;
            color: #2d3748;
        }

        .card-header h5 i {
            color: var(--primary-purple);
        }

        /* ========== BUTTONS ========== */
        .btn-primary {
            background: var(--gradient-purple);
            border: none;
            padding: 0.45rem 1.2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-purple);
            color: white;
        }

        .btn-primary:active {
            transform: scale(0.96);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            border: none;
            color: #212529;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .btn-info {
            background: linear-gradient(135deg, #17a2b8, #0dcaf0);
            border: none;
            color: white;
        }

        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
            color: white;
        }

        .btn-sm {
            padding: 0.3rem 0.7rem;
            font-size: 0.75rem;
            border-radius: 6px;
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-purple);
            color: var(--primary-purple);
        }

        .btn-outline-primary:hover {
            background: var(--gradient-purple);
            color: white;
            border-color: transparent;
        }

        /* ========== TABLES ========== */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--bg-purple);
            color: var(--dark-purple);
            font-weight: 600;
            padding: 0.8rem 1rem;
            border-bottom: 2px solid rgba(111, 66, 193, 0.1);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 0.8rem 1rem;
            vertical-align: middle;
            border-color: rgba(0, 0, 0, 0.04);
            font-size: 0.85rem;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: var(--bg-purple);
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(111, 66, 193, 0.02);
        }

        /* ========== BADGES ========== */
        .badge {
            padding: 0.3rem 0.7rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .badge.bg-success {
            background: linear-gradient(135deg, #28a745, #20c997) !important;
        }

        .badge.bg-warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14) !important;
            color: #212529;
        }

        .badge.bg-danger {
            background: linear-gradient(135deg, #dc3545, #c82333) !important;
        }

        .badge.bg-info {
            background: linear-gradient(135deg, #17a2b8, #0dcaf0) !important;
        }

        .badge.bg-primary {
            background: var(--gradient-purple) !important;
        }

        /* ========== ALERTS ========== */
        .alert {
            border: none;
            border-radius: 10px;
            padding: 0.8rem 1.2rem;
            margin-bottom: 1.25rem;
            font-size: 0.85rem;
            border-left: 4px solid;
        }

        .alert-success {
            background: #e8f5e9;
            border-left-color: #28a745;
            color: #1e7e34;
        }

        .alert-danger {
            background: #fce4ec;
            border-left-color: #dc3545;
            color: #9a1a2e;
        }

        .alert-warning {
            background: #fff3e0;
            border-left-color: #ffc107;
            color: #856404;
        }

        .alert-info {
            background: #e3f2fd;
            border-left-color: #17a2b8;
            color: #0c5460;
        }

        /* ========== FORMS ========== */
        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
            color: #4a5568;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 4px rgba(111, 66, 193, 0.1);
        }

        .form-control-sm {
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
        }

        /* ========== MODALS ========== */
        .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: 1px solid rgba(111, 66, 193, 0.1);
            padding: 1.25rem 1.5rem;
            border-radius: 16px 16px 0 0;
        }

        .modal-header.bg-primary {
            background: var(--gradient-purple) !important;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid rgba(111, 66, 193, 0.1);
            padding: 1rem 1.5rem;
            border-radius: 0 0 16px 16px;
        }

        /* ========== PAGINATION ========== */
        .pagination .page-link {
            color: var(--primary-purple);
            border: none;
            border-radius: 8px;
            margin: 0 3px;
            padding: 0.5rem 0.9rem;
            font-weight: 500;
        }

        .pagination .page-link:hover {
            background: var(--light-purple);
            color: var(--dark-purple);
        }

        .pagination .page-item.active .page-link {
            background: var(--gradient-purple);
            color: white;
            box-shadow: var(--shadow-purple);
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                top: 56px;
                width: 280px;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                margin-top: 56px;
                padding: 1rem;
            }

            .navbar {
                padding: 0.5rem 1rem;
            }

            .navbar-brand span {
                display: none;
            }

            .navbar-brand i {
                font-size: 1.2rem;
            }
        }

        /* ========== ANIMATIONS ========== */
        .fade-in-up {
            animation: fadeInUp 0.4s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        /* ========== UTILITY ========== */
        .bg-soft-primary {
            background: rgba(111, 66, 193, 0.08) !important;
        }

        .bg-soft-success {
            background: rgba(40, 167, 69, 0.08) !important;
        }

        .bg-soft-danger {
            background: rgba(220, 53, 69, 0.08) !important;
        }

        .bg-soft-warning {
            background: rgba(255, 193, 7, 0.08) !important;
        }

        .bg-soft-info {
            background: rgba(23, 162, 184, 0.08) !important;
        }

        .text-primary {
            color: var(--primary-purple) !important;
        }

        .text-secondary-purple {
            color: var(--secondary-purple) !important;
        }

        .fw-600 {
            font-weight: 600;
        }

        .fw-700 {
            font-weight: 700;
        }

        .shadow-hover {
            transition: all 0.3s ease;
        }

        .shadow-hover:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08) !important;
        }

        .rounded-12 {
            border-radius: 12px;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .gap-3 {
            gap: 1rem;
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container-fluid px-0">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-link text-white p-0 d-md-none" id="sidebarToggle">
                    <i class="bi bi-list fs-3"></i>
                </button>
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="bi bi-envelope-paper-fill"></i>
                    <span>Sistem Pelayanan Surat Terpadu</span>
                </a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <!-- Notification Bell -->
                <div class="dropdown d-none d-md-block">
                    <button class="btn btn-link text-white position-relative p-0" type="button"
                        data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 9px;">
                            3
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="width: 300px;">
                        <li class="dropdown-header bg-light py-2">
                            <strong>Notifikasi</strong>
                        </li>
                        <li><a class="dropdown-item py-2" href="#"><i
                                    class="bi bi-check-circle text-success me-2"></i> Surat #123 disetujui</a></li>
                        <li><a class="dropdown-item py-2" href="#"><i
                                    class="bi bi-clock-history text-warning me-2"></i> 3 surat pending</a></li>
                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>
                        <li class="text-center"><a class="dropdown-item small" href="#">Lihat semua</a></li>
                    </ul>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link text-white p-0 d-flex align-items-center gap-2" type="button"
                        data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <span class="d-none d-md-inline small fw-500">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                        <li>
                            <div class="dropdown-header py-2">
                                <strong>{{ auth()->user()->name }}</strong>
                                <div class="small text-muted">{{ ucfirst(auth()->user()->role) }}</div>
                            </div>
                        </li>
                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>
                        <li><a class="dropdown-item py-2" href="{{ route('profile.show') }}"><i
                                    class="bi bi-person me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('password.change.form') }}"><i
                                    class="bi bi-key me-2"></i> Ubah Password</a></li>
                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger"><i
                                        class="bi bi-box-arrow-right me-2"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Main Content -->
    <div class="main-content fade-in-up">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            // Sidebar toggle
            $('#sidebarToggle').click(function(e) {
                e.stopPropagation();
                $('#sidebar').toggleClass('active');
            });

            // Close sidebar on outside click (mobile)
            $(document).click(function(event) {
                if ($(window).width() <= 768) {
                    if (!$(event.target).closest('#sidebar').length &&
                        !$(event.target).closest('#sidebarToggle').length &&
                        $('#sidebar').hasClass('active')) {
                        $('#sidebar').removeClass('active');
                    }
                }
            });

            // DataTables
            if ($('.datatable').length) {
                $('.datatable').DataTable({
                    responsive: true,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                    },
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "Semua"]
                    ]
                });
            }

            // Auto hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });

        // SweetAlert delete confirmation
        window.confirmDelete = function(url, title = 'Yakin hapus?') {
            Swal.fire({
                title: title,
                text: "Data akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6f42c1',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        };

        // Toast notification
        window.showToast = function(message, type = 'success') {
            Swal.fire({
                icon: type,
                title: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        };
    </script>

    @stack('scripts')
</body>

</html>
