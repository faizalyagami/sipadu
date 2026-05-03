{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Surat Unisba - @yield('title')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary-purple: #6f42c1;
            --secondary-purple: #8b5cf6;
            --dark-purple: #5a32a3;
            --light-purple: #e9d5ff;
            --bg-purple: #f3e8ff;
        }
        
        body {
            background: #f0f2f5;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Navbar Styles - Lebih ringkas */
        .navbar {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 0.75rem 1.5rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 1.1rem;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .navbar-brand i {
            font-size: 1.3rem;
        }
        
        .navbar .dropdown-toggle::after {
            display: none;
        }
        
        .user-avatar {
            width: 34px;
            height: 34px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .user-avatar i {
            font-size: 1.1rem;
            color: white;
        }
        
        /* Sidebar Styles - Lebih rapi */
        .sidebar {
            background: white;
            width: 260px;
            position: fixed;
            top: 56px;
            left: 0;
            bottom: 0;
            box-shadow: 1px 0 10px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
            z-index: 1020;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 1.25rem;
            text-align: center;
            border-bottom: 1px solid #edf2f7;
        }
        
        .logo-wrapper {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, var(--primary-purple), var(--secondary-purple));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
        }
        
        .logo-wrapper i {
            font-size: 2rem;
            color: white;
        }
        
        .sidebar-header h5 {
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 3px;
            color: #1a202c;
        }
        
        .sidebar-header small {
            font-size: 0.65rem;
            color: #718096;
        }
        
        /* Navigation */
        .nav-section {
            padding: 0.75rem 1.25rem 0.25rem;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #a0aec0;
        }
        
        .sidebar .nav-link {
            padding: 0.6rem 1rem;
            margin: 0.2rem 0.75rem;
            border-radius: 10px;
            color: #4a5568;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .sidebar .nav-link i {
            width: 22px;
            margin-right: 10px;
            font-size: 1rem;
        }
        
        .sidebar .nav-link:hover {
            background: var(--light-purple);
            color: var(--primary-purple);
        }
        
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary-purple), var(--secondary-purple));
            color: white;
        }
        
        .sidebar-footer {
            padding: 0.75rem;
            border-top: 1px solid #edf2f7;
            text-align: center;
            font-size: 0.65rem;
            color: #a0aec0;
        }
        
        /* Main Content - Lebih lega */
        .main-content {
            margin-left: 260px;
            margin-top: 56px;
            padding: 1.25rem 1.5rem;
            min-height: calc(100vh - 56px);
            transition: all 0.3s ease;
        }
        
        /* Card Styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
            background: white;
            margin-bottom: 1.25rem;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #edf2f7;
            padding: 1rem 1.25rem;
            border-radius: 12px 12px 0 0 !important;
        }
        
        .card-header h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1rem;
            color: #2d3748;
        }
        
        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-purple), var(--secondary-purple));
            border: none;
            padding: 0.4rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(111, 66, 193, 0.3);
        }
        
        .btn-sm {
            padding: 0.3rem 0.7rem;
            font-size: 0.75rem;
        }
        
        /* Table Styles - Lebih rapi */
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background: #f8fafc;
            color: #4a5568;
            font-weight: 600;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #edf2f7;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .table tbody td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            border-color: #edf2f7;
            font-size: 0.85rem;
        }
        
        .table tbody tr:hover {
            background: #faf5ff;
        }
        
        /* Status Badge */
        .badge {
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 500;
        }
        
        /* Page Title */
        .page-title {
            margin-bottom: 1.25rem;
        }
        
        .page-title h4 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.25rem;
        }
        
        .page-title p {
            font-size: 0.85rem;
            color: #718096;
            margin-bottom: 0;
        }
        
        /* Alert */
        .alert {
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 0.85rem;
        }
        
        /* Modal */
        .modal-content {
            border: none;
            border-radius: 16px;
        }
        
        .modal-header {
            border-bottom-color: #edf2f7;
            padding: 1rem 1.25rem;
        }
        
        .modal-body {
            padding: 1.25rem;
        }
        
        .modal-footer {
            border-top-color: #edf2f7;
            padding: 1rem 1.25rem;
        }
        
        /* Form */
        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.4rem;
            color: #4a5568;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border-color: #e2e8f0;
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 3px rgba(111, 66, 193, 0.1);
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                top: 52px;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                margin-top: 52px;
                padding: 1rem;
            }
            
            .navbar {
                padding: 0.5rem 1rem;
            }
            
            .navbar-brand span {
                display: none;
            }
        }
        
        /* Animations */
        .fade-in-up {
            animation: fadeInUp 0.3s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
                    <i class="bi bi-list fs-4"></i>
                </button>
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="bi bi-envelope-paper-fill"></i>
                    <span>Sistem Surat Unisba</span>
                </a>
            </div>
            <div class="dropdown">
                <button class="btn btn-link text-white p-0 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                    <div class="user-avatar">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <span class="d-none d-md-inline small">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                    <li><a class="dropdown-item py-2" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i> Profile</a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('password.change.form') }}"><i class="bi bi-key me-2"></i> Ubah Password</a></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-wrapper">
                <i class="bi bi-building"></i>
            </div>
            <h5>Universitas Islam Bandung</h5>
            <small>Sistem Informasi Surat</small>
        </div>
        
        <ul class="nav flex-column">
            @if(auth()->user()->isAdmin())
                <li class="nav-section">MAIN</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-section">MASTER DATA</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people"></i> Data Petugas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.fakultas.*') ? 'active' : '' }}" href="{{ route('admin.fakultas.index') }}">
                        <i class="bi bi-building"></i> Data Fakultas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.mahasiswa.*') ? 'active' : '' }}" href="{{ route('admin.mahasiswa.index') }}">
                        <i class="bi bi-mortarboard"></i> Data Mahasiswa
                    </a>
                </li>
                <li class="nav-section">SURAT</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.jenis-surat.*') ? 'active' : '' }}" href="{{ route('admin.jenis-surat.index') }}">
                        <i class="bi bi-file-text"></i> Jenis Surat
                    </a>
                </li>
            @elseif(auth()->user()->isPetugas())
                <li class="nav-section">MAIN</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}" href="{{ route('petugas.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-section">APPROVAL</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('petugas.approval.index') ? 'active' : '' }}" href="{{ route('petugas.approval.index') }}">
                        <i class="bi bi-check2-circle"></i> Approve Surat
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('petugas.history') ? 'active' : '' }}" href="{{ route('petugas.history') }}">
                        <i class="bi bi-clock-history"></i> Riwayat Approve
                    </a>
                </li>
            @elseif(auth()->user()->isMahasiswa())
                <li class="nav-section">MAIN</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}" href="{{ route('mahasiswa.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-section">PENGAJUAN</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('mahasiswa.pengajuan.*') ? 'active' : '' }}" href="{{ route('mahasiswa.pengajuan.index') }}">
                        <i class="bi bi-send"></i> Buat Surat Baru
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('mahasiswa.riwayat') ? 'active' : '' }}" href="{{ route('mahasiswa.riwayat') }}">
                        <i class="bi bi-archive"></i> Riwayat Surat
                    </a>
                </li>
            @endif
        </ul>
        
        <div class="sidebar-footer">
            <small>© {{ date('Y') }} Unisba | v1.0</small>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content fade-in-up">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('warning'))
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
            
            // Close sidebar when clicking outside on mobile
            $(document).click(function(event) {
                if ($(window).width() <= 768) {
                    if (!$(event.target).closest('#sidebar').length && 
                        !$(event.target).closest('#sidebarToggle').length && 
                        $('#sidebar').hasClass('active')) {
                        $('#sidebar').removeClass('active');
                    }
                }
            });
            
            // Initialize DataTables
            if ($('.datatable').length) {
                $('.datatable').DataTable({
                    responsive: true,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                    },
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
                });
            }
            
            // Auto hide alerts after 4 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 4000);
        });
        
        // SweetAlert delete confirmation
        function confirmDelete(url, title = 'Yakin hapus?') {
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
        }
        
        // Show loading
        function showLoading() {
            Swal.fire({
                title: 'Loading...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        
        // Show toast notification
        function showToast(message, type = 'success') {
            Swal.fire({
                icon: type,
                title: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
    </script>
    
    @stack('scripts')
</body>
</html>