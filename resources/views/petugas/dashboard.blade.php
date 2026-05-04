{{-- resources/views/petugas/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Petugas')

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
                            <p class="text-white-50 mb-0">Kelola dan approve surat mahasiswa dengan cepat</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-white rounded-circle p-3 d-inline-block">
                                <i class="bi bi-check2-circle fs-1 text-primary"></i>
                            </div>
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
                            <small class="text-muted mt-2 d-block">
                                <i class="bi bi-envelope"></i> Semua pengajuan
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
                            <h2 class="mb-0 fw-bold text-warning">{{ $totalPending }}</h2>
                            <small class="text-warning mt-2 d-block">
                                <i class="bi bi-clock"></i> Perlu persetujuan
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
                            <h2 class="mb-0 fw-bold text-success">{{ $totalApproved }}</h2>
                            <small class="text-success mt-2 d-block">
                                <i class="bi bi-check-circle"></i> Surat selesai
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
                            <h2 class="mb-0 fw-bold text-danger">{{ $totalRejected }}</h2>
                            <small class="text-danger mt-2 d-block">
                                <i class="bi bi-x-circle"></i> Perlu revisi
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

    <!-- Chart & Statistik -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-4">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-graph-up text-primary me-2"></i>Statistik Surat per Bulan
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="suratChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-4">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-pie-chart text-primary me-2"></i>Surat per Jenis
                    </h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <canvas id="jenisChart" height="200"></canvas>
                    <div class="mt-3" id="jenisLegend"></div>
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
                            <a href="{{ route('petugas.approval.index') }}" class="text-decoration-none">
                                <div class="card bg-warning bg-opacity-10 border-0 text-center p-3 h-100">
                                    <i class="bi bi-check2-circle fs-1 text-warning"></i>
                                    <h6 class="mt-2 mb-0">Approve Surat</h6>
                                    <small class="text-muted">Proses surat menunggu</small>
                                    @if($totalPending > 0)
                                        <span class="badge bg-danger mt-2">{{ $totalPending }} Menunggu</span>
                                    @endif
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('petugas.history') }}" class="text-decoration-none">
                                <div class="card bg-info bg-opacity-10 border-0 text-center p-3 h-100">
                                    <i class="bi bi-clock-history fs-1 text-info"></i>
                                    <h6 class="mt-2 mb-0">Riwayat Approve</h6>
                                    <small class="text-muted">Lihat riwayat persetujuan</small>
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

    <!-- Surat Menunggu Persetujuan -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-clock-history text-warning me-2"></i>Surat Menunggu Persetujuan
                        </h5>
                        <a href="{{ route('petugas.approval.index') }}" class="btn btn-sm btn-warning">
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
                                    <th>Mahasiswa</th>
                                    <th>NPM</th>
                                    <th>Jenis Surat</th>
                                    <th>Keperluan</th>
                                    <th>Tgl Pengajuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingSurats as $index => $surat)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-semibold">{{ $surat->mahasiswa->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $surat->mahasiswa->npm ?? '-' }}</td>
                                    <td>{{ $surat->jenisSurat->nama_surat ?? '-' }}</td>
                                    <td>{{ Str::limit($surat->keperluan, 40) }}</td>
                                    <td>{{ $surat->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-success" onclick="approveSurat({{ $surat->id }})">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $surat->id }}">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                            <a href="#" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Modal Tolak -->
                                <div class="modal fade" id="rejectModal{{ $surat->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('petugas.approval.reject', $surat->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Alasan Penolakan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <textarea name="alasan" class="form-control" rows="4" required placeholder="Masukkan alasan penolakan..."></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Tolak</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                                        <p class="text-muted mb-0">Tidak ada surat yang menunggu persetujuan</p>
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

    <!-- Surat Terbaru Disetujui -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-check2-circle text-success me-2"></i>Surat Terbaru Disetujui
                        </h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Mahasiswa</th>
                                    <th>Jenis Surat</th>
                                    <th>Tanggal Disetujui</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($approvedSurats as $index => $surat)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-semibold">{{ $surat->mahasiswa->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $surat->jenisSurat->nama_surat ?? '-' }}</td>
                                    <td>{{ $surat->approved_at ? $surat->approved_at->format('d/m/Y H:i') : '-' }}</td>
                                    <td><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Disetujui</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                        <p class="text-muted mb-0">Belum ada surat yang disetujui</p>
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
    .table-hover tbody tr:hover {
        background-color: rgba(111, 66, 193, 0.05);
        cursor: pointer;
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart Surat per Bulan
    const ctx = document.getElementById('suratChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Jumlah Surat',
                data: {!! json_encode($suratCounts) !!},
                borderColor: '#6f42c1',
                backgroundColor: 'rgba(111, 66, 193, 0.05)',
                borderWidth: 3,
                pointBackgroundColor: '#6f42c1',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { backgroundColor: '#6f42c1' }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 5, precision: 0 } }
            }
        }
    });
    
    // Chart Surat per Jenis
    const jenisData = {!! json_encode($suratPerJenis->map(function($item) { 
        return ['label' => $item->nama_surat, 'count' => $item->surats_count]; 
    })) !!};
    
    const jenisCtx = document.getElementById('jenisChart').getContext('2d');
    new Chart(jenisCtx, {
        type: 'doughnut',
        data: {
            labels: jenisData.map(item => item.label),
            datasets: [{
                data: jenisData.map(item => item.count),
                backgroundColor: ['#6f42c1', '#8b5cf6', '#a78bfa', '#c4b5fd', '#ddd6fe'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return `${context.label}: ${context.raw} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    
    function approveSurat(id) {
        Swal.fire({
            title: 'Setujui Surat?',
            text: "Surat akan disetujui dan ditandatangani elektronik",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/petugas/approval/${id}/approve`;
            }
        });
    }
</script>
@endpush