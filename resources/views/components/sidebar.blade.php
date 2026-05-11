<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="mx-auto mb-3 logo-container">
            <img src="{{ asset('images/logo-fakultas-psikologi.jpg') }}" alt="Logo Unisba" class="img-fluid" style="max-width: 70px;">
        </div>
        <h5>Fakultas Psikologi UNISBA</h5>
        <small>Sistem Pelayanan Surat Terpadu</small>
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
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.kategori-surat.*') ? 'active' : '' }}" href="{{ route('admin.kategori-surat.index') }}">
                    <i class="bi bi-tags"></i> Kategori Surat
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