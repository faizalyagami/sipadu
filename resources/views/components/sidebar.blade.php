<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <img src="{{ asset('images/logo-fakultas-psikologi.jpg') }}" alt="Logo Unisba" class="img-fluid">
        </div>
        <h5>Fakultas Psikologi UNISBA</h5>
        <small>Sistem Pelayanan Surat Terpadu</small>
    </div>

    <ul class="nav flex-column">
        @if (auth()->user()->isAdmin())
            <li class="nav-section">MAIN</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <li class="nav-section">MASTER DATA</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                    href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people"></i> Data Petugas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.fakultas.*') ? 'active' : '' }}"
                    href="{{ route('admin.fakultas.index') }}">
                    <i class="bi bi-building"></i> Data Fakultas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.mahasiswa.*') ? 'active' : '' }}"
                    href="{{ route('admin.mahasiswa.index') }}">
                    <i class="bi bi-mortarboard"></i> Data Mahasiswa
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.kategori-surat.*') ? 'active' : '' }}"
                    href="{{ route('admin.kategori-surat.index') }}">
                    <i class="bi bi-tags"></i> Kategori Surat
                </a>
            </li>

            <li class="nav-section">SURAT</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.jenis-surat.*') ? 'active' : '' }}"
                    href="{{ route('admin.jenis-surat.index') }}">
                    <i class="bi bi-file-text"></i> Jenis Surat
                </a>
            </li>
        @elseif(auth()->user()->isPetugas())
            <li class="nav-section">MAIN</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}"
                    href="{{ route('petugas.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <li class="nav-section">APPROVAL</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('petugas.approval.index') ? 'active' : '' }}"
                    href="{{ route('petugas.approval.index') }}">
                    <i class="bi bi-check2-circle"></i> Approve Surat
                    @php
                        $pendingCount = \App\Models\Surat::where('status', 'pending')->count();
                    @endphp
                    @if ($pendingCount > 0)
                        <span class="badge">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('petugas.history') ? 'active' : '' }}"
                    href="{{ route('petugas.history') }}">
                    <i class="bi bi-clock-history"></i> Riwayat Approve
                </a>
            </li>
        @elseif(auth()->user()->isMahasiswa())
            <li class="nav-section">MAIN</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}"
                    href="{{ route('mahasiswa.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <li class="nav-section">PENGAJUAN</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('mahasiswa.pengajuan.*') ? 'active' : '' }}"
                    href="{{ route('mahasiswa.pengajuan.index') }}">
                    <i class="bi bi-send"></i> Buat Surat Baru
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('mahasiswa.riwayat') ? 'active' : '' }}"
                    href="{{ route('mahasiswa.riwayat') }}">
                    <i class="bi bi-archive"></i> Riwayat Surat
                </a>
            </li>
        @endif
    </ul>

    <div class="sidebar-footer">
        <i class="bi bi-c-circle"></i> {{ date('Y') }} Unisba &nbsp;|&nbsp; <i class="bi bi-code"></i> v1.0
    </div>
</div>

@push('styles')
    <style>
        .sidebar {
            background: linear-gradient(180deg, #ffffff 0%, #f8f4ff 100%);
            border-right: 1px solid rgba(111, 66, 193, 0.08);
        }

        .sidebar-header {
            background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%);
            border-bottom: none;
            padding: 1.5rem 1.25rem;
            position: relative;
            overflow: hidden;
        }

        .sidebar-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            pointer-events: none;
        }

        .sidebar-header .logo-container {
            border: 3px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 20px rgba(111, 66, 193, 0.3);
        }

        .sidebar-header h5 {
            color: white;
            font-weight: 700;
            font-size: 0.95rem;
            margin-top: 10px;
            margin-bottom: 2px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header small {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.7rem;
            font-weight: 400;
        }

        .nav-section {
            color: #8b5cf6;
            font-weight: 700;
            font-size: 0.65rem;
            letter-spacing: 1px;
            padding: 0.8rem 1.25rem 0.3rem;
            position: relative;
        }

        .nav-section::after {
            content: '';
            position: absolute;
            left: 1.25rem;
            right: 1.25rem;
            bottom: -2px;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(111, 66, 193, 0.1), transparent);
        }

        .sidebar .nav-link {
            color: #4a5568;
            padding: 0.65rem 1rem;
            margin: 0.2rem 0.8rem;
            border-radius: 10px;
            transition: all 0.3s ease;
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
            color: #8b5cf6;
        }

        .sidebar .nav-link:hover {
            background: rgba(111, 66, 193, 0.08);
            color: #6f42c1;
            transform: translateX(4px);
        }

        .sidebar .nav-link:hover i {
            color: #6f42c1;
            transform: scale(1.1);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(111, 66, 193, 0.3);
        }

        .sidebar .nav-link.active i {
            color: white;
        }

        .sidebar .nav-link .badge {
            background: #dc3545;
            color: white;
            font-size: 0.6rem;
            padding: 0.15rem 0.5rem;
            border-radius: 20px;
            margin-left: auto;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        }

        .sidebar-footer {
            background: rgba(111, 66, 193, 0.05);
            border-top: 1px solid rgba(111, 66, 193, 0.08);
            padding: 0.8rem;
            text-align: center;
            font-size: 0.6rem;
            color: #8b5cf6;
        }

        .sidebar-footer i {
            color: #6f42c1;
        }
    </style>
@endpush
