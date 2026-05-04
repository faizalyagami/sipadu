{{-- resources/views/mahasiswa/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%); border-radius: 15px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="text-white mb-1">Welcome back, {{ Auth::user()->name }}! 👋</h3>
                            <p class="text-white-50 mb-0">Semangat menempuh pendidikan di Unisba!</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-white rounded-circle p-3 d-inline-block">
                                <i class="bi bi-mortarboard fs-1 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($error))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $error }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @else

    <!-- Info Mahasiswa -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-primary rounded-circle p-3 me-3">
                            <i class="bi bi-person fs-4 text-primary"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase">Nama Mahasiswa</small>
                            <h6 class="mb-0">{{ Auth::user()->mahasiswa->nama_lengkap ?? '-' }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-info rounded-circle p-3 me-3">
                            <i class="bi bi-upc-scan fs-4 text-info"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase">NPM</small>
                            <h6 class="mb-0">{{ Auth::user()->mahasiswa->npm ?? '-' }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-success rounded-circle p-3 me-3">
                            <i class="bi bi-building fs-4 text-success"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase">Fakultas</small>
                            <h6 class="mb-0">{{ Auth::user()->mahasiswa->prodi->fakultas->nama_fakultas ?? '-' }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-warning rounded-circle p-3 me-3">
                            <i class="bi bi-star fs-4 text-warning"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase">Program Studi</small>
                            <h6 class="mb-0">{{ Auth::user()->mahasiswa->prodi->nama_prodi ?? '-' }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Surat -->
    <div class="row mb-4">
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Surat</h6>
                            <h2 class="mb-0 fw-bold">{{ $totalSurat }}</h2>
                            <small class="text-success mt-2 d-block">
                                <i class="bi bi-arrow-up"></i> Semua pengajuan
                            </small>
                        </div>
                        <div class="bg-soft-primary rounded-circle p-3">
                            <i class="bi bi-envelope-paper fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Menunggu</h6>
                            <h2 class="mb-0 fw-bold">{{ $pendingSurat }}</h2>
                            <small class="text-warning mt-2 d-block">
                                <i class="bi bi-clock"></i> Sedang diproses
                            </small>
                        </div>
                        <div class="bg-soft-warning rounded-circle p-3">
                            <i class="bi bi-hourglass-split fs-1 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Disetujui</h6>
                            <h2 class="mb-0 fw-bold">{{ $approvedSurat }}</h2>
                            <small class="text-success mt-2 d-block">
                                <i class="bi bi-check-circle"></i> Selesai
                            </small>
                        </div>
                        <div class="bg-soft-success rounded-circle p-3">
                            <i class="bi bi-check2-circle fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Ditolak</h6>
                            <h2 class="mb-0 fw-bold">{{ $rejectedSurat }}</h2>
                            <small class="text-danger mt-2 d-block">
                                <i class="bi bi-x-circle"></i> Perlu perbaikan
                            </small>
                        </div>
                        <div class="bg-soft-danger rounded-circle p-3">
                            <i class="bi bi-x-octagon fs-1 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-lightning-charge text-primary me-2"></i>Aksi Cepat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('mahasiswa.pengajuan.index') }}" class="text-decoration-none">
                                <div class="card bg-primary bg-opacity-10 border-0 text-center p-3 h-100">
                                    <i class="bi bi-send fs-1 text-primary"></i>
                                    <h6 class="mt-2 mb-0">Buat Surat Baru</h6>
                                    <small class="text-muted">Ajukan permohonan surat</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('mahasiswa.riwayat') }}" class="text-decoration-none">
                                <div class="card bg-info bg-opacity-10 border-0 text-center p-3 h-100">
                                    <i class="bi bi-clock-history fs-1 text-info"></i>
                                    <h6 class="mt-2 mb-0">Riwayat Surat</h6>
                                    <small class="text-muted">Lihat status pengajuan</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('profile.show') }}" class="text-decoration-none">
                                <div class="card bg-success bg-opacity-10 border-0 text-center p-3 h-100">
                                    <i class="bi bi-person-circle fs-1 text-success"></i>
                                    <h6 class="mt-2 mb-0">Profil Saya</h6>
                                    <small class="text-muted">Perbarui data diri</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Surat -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-table text-primary me-2"></i>Riwayat Surat Terbaru
                        </h5>
                        <a href="{{ route('mahasiswa.riwayat') }}" class="btn btn-sm btn-primary">
                            Lihat Semua <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Jenis Surat</th>
                                    <th>Keperluan</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestSurats as $index => $surat)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-semibold">{{ $surat->jenisSurat->nama_surat ?? '-' }}</td>
                                    <td>{{ Str::limit($surat->keperluan, 50) }}</td>
                                    <td>{{ $surat->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($surat->status == 'pending')
                                            <span class="badge bg-warning">
                                                <i class="bi bi-clock me-1"></i> Menunggu
                                            </span>
                                        @elseif($surat->status == 'approved')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i> Disetujui
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i> Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($surat->status == 'approved')
                                            <a href="{{ route('mahasiswa.pengajuan.download', $surat->id) }}" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                        <p class="text-muted mb-0">Belum ada pengajuan surat</p>
                                        <a href="{{ route('mahasiswa.pengajuan.index') }}" class="btn btn-primary mt-3">
                                            <i class="bi bi-plus-circle"></i> Buat Surat Pertama
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .bg-soft-primary {
        background: rgba(111, 66, 193, 0.1);
    }
    .bg-soft-info {
        background: rgba(23, 162, 184, 0.1);
    }
    .bg-soft-success {
        background: rgba(40, 167, 69, 0.1);
    }
    .bg-soft-warning {
        background: rgba(255, 193, 7, 0.1);
    }
    .bg-soft-danger {
        background: rgba(220, 53, 69, 0.1);
    }
    .bg-opacity-10 {
        opacity: 0.8;
    }
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
    a .card:hover {
        transform: translateY(-3px);
    }
    .table-hover tbody tr:hover {
        background-color: rgba(111, 66, 193, 0.05);
        cursor: pointer;
    }
</style>
@endsection