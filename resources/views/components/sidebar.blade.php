{{-- resources/views/components/sidebar.blade.php --}}
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="text-center">
            <div class="mx-auto mb-3 logo-container">
                <img src="{{ asset('images/unisba-logo.png') }}" alt="Logo Unisba" class="img-fluid" 
                     onerror="this.src='https://via.placeholder.com/80?text=UNISBA'" style="max-width: 70px;">
            </div>
            <h5 class="mt-2 mb-0 fw-bold">Universitas Islam Bandung</h5>
            <small class="text-muted">Sistem Informasi Surat</small>
        </div>
    </div>
    
    <hr>
    
    <ul class="nav flex-column">
        @if(auth()->user()->isAdmin())
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Master Data Menu -->
            <li class="nav-section mt-3 mb-2">
                <small class="text-muted px-3">MASTER DATA</small>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people"></i>
                    <span>Data Petugas</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.fakultas.*') ? 'active' : '' }}" href="{{ route('admin.fakultas.index') }}">
                    <i class="bi bi-building"></i>
                    <span>Data Fakultas</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.mahasiswa.*') ? 'active' : '' }}" href="{{ route('admin.mahasiswa.index') }}">
                    <i class="bi bi-mortarboard"></i>
                    <span>Data Mahasiswa</span>
                </a>
            </li>
            
            <!-- Surat Menu -->
            <li class="nav-section mt-3 mb-2">
                <small class="text-muted px-3">SURAT</small>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.jenis-surat.*') ? 'active' : '' }}" href="{{ route('admin.jenis-surat.index') }}">
                    <i class="bi bi-file-text"></i>
                    <span>Jenis Surat</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.surat.*') ? 'active' : '' }}" href="#">
                    <i class="bi bi-envelope"></i>
                    <span>Manajemen Surat</span>
                </a>
            </li>
            
        @elseif(auth()->user()->isPetugas())
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}" href="{{ route('petugas.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Approval Menu -->
            <li class="nav-section mt-3 mb-2">
                <small class="text-muted px-3">APPROVAL</small>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('petugas.approval.index') ? 'active' : '' }}" href="{{ route('petugas.approval.index') }}">
                    <i class="bi bi-check2-circle"></i>
                    <span>Approve Surat</span>
                    @php
                        $pendingCount = \App\Models\Surat::where('status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="badge bg-danger rounded-pill ms-2">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('petugas.history') ? 'active' : '' }}" href="{{ route('petugas.history') }}">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat Approve</span>
                </a>
            </li>
            
        @elseif(auth()->user()->isMahasiswa())
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}" href="{{ route('mahasiswa.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Surat Menu -->
            <li class="nav-section mt-3 mb-2">
                <small class="text-muted px-3">PENGAJUAN SURAT</small>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('mahasiswa.pengajuan.*') ? 'active' : '' }}" href="{{ route('mahasiswa.pengajuan.index') }}">
                    <i class="bi bi-send"></i>
                    <span>Buat Surat Baru</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('mahasiswa.riwayat') ? 'active' : '' }}" href="{{ route('mahasiswa.riwayat') }}">
                    <i class="bi bi-archive"></i>
                    <span>Riwayat Surat</span>
                </a>
            </li>
        @endif
    </ul>
    
    <hr class="mt-auto">
    
    <!-- Bottom Menu -->
    <div class="sidebar-footer p-3">
        <div class="small text-muted mb-2">
            <i class="bi bi-info-circle"></i> Versi 1.0.0
        </div>
        <div class="small text-muted">
            <i class="bi bi-building"></i> © {{ date('Y') }} Unisba
        </div>
    </div>
</div>

@push('styles')
<style>
    .sidebar {
        background: white;
        min-height: 100vh;
        box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        position: fixed;
        width: 260px;
        transition: all 0.3s;
        z-index: 1000;
        display: flex;
        flex-direction: column;
    }
    
    .sidebar-header {
        padding: 20px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .logo-container {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .nav {
        flex: 1;
    }
    
    .nav-section small {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .sidebar .nav-link {
        color: #4a5568;
        padding: 12px 20px;
        border-radius: 10px;
        margin: 4px 12px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
    }
    
    .sidebar .nav-link:hover {
        background: #e9d5ff;
        color: #6f42c1;
        transform: translateX(5px);
    }
    
    .sidebar .nav-link.active {
        background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%);
        color: white;
        box-shadow: 0 4px 10px rgba(111, 66, 193, 0.3);
    }
    
    .sidebar .nav-link i {
        margin-right: 12px;
        width: 20px;
        font-size: 1.1rem;
    }
    
    .sidebar .nav-link span {
        flex: 1;
    }
    
    .sidebar-footer {
        border-top: 1px solid rgba(0,0,0,0.05);
        font-size: 12px;
    }
    
    @media (max-width: 768px) {
        .sidebar {
            margin-left: -260px;
        }
        
        .sidebar.active {
            margin-left: 0;
        }
    }
</style>
@endpush