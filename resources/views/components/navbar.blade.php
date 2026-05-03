{{-- resources/views/components/navbar.blade.php --}}
<nav class="navbar navbar-dark sticky-top">
    <div class="container-fluid">
        <button class="btn btn-link text-white d-md-none" id="sidebarToggle">
            <i class="bi bi-list fs-3"></i>
        </button>
        <a class="navbar-brand" href="{{ url('/') }}">
            <i class="bi bi-envelope-paper-fill me-2"></i> 
            <span class="fw-semibold">Sistem Surat Unisba</span>
        </a>
        
        <div class="d-flex align-items-center gap-3">
            <!-- Notification Bell -->
            <div class="dropdown">
                <button class="btn btn-link text-white position-relative" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                        3
                        <span class="visually-hidden">notifications</span>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                    <li class="dropdown-header bg-light">
                        <strong>Notifikasi</strong>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-check-circle text-success"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <small>Surat #123 telah disetujui</small>
                                    <div class="text-muted small">5 menit lalu</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-clock-history text-warning"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <small>Ada 3 surat pending</small>
                                    <div class="text-muted small">1 jam lalu</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="text-center">
                        <a class="dropdown-item small" href="#">Lihat semua notifikasi</a>
                    </li>
                </ul>
            </div>
            
            <!-- User Dropdown -->
            <div class="dropdown">
                <button class="btn btn-link text-white dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                    <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                        <i class="bi bi-person fs-5"></i>
                    </div>
                    <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <div class="dropdown-header">
                            <strong>{{ auth()->user()->name }}</strong>
                            <div class="small text-muted">{{ ucfirst(auth()->user()->role) }}</div>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.show') }}">
                            <i class="bi bi-person me-2"></i> Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('password.change.form') }}">
                            <i class="bi bi-key me-2"></i> Ubah Password
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

@push('styles')
<style>
    .navbar {
        background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%);
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 12px 20px;
    }
    
    .navbar-brand {
        font-weight: 600;
        font-size: 1.2rem;
    }
    
    .dropdown-item:active {
        background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%);
        color: white;
    }
    
    .dropdown-header {
        padding: 10px 20px;
    }
    
    .btn-link {
        text-decoration: none;
    }
    
    .btn-link:hover {
        opacity: 0.8;
    }
</style>
@endpush