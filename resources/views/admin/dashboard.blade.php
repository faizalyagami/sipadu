{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid px-0">
    <!-- Welcome Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Welcome back, {{ Auth::user()->name }}!</h4>
            <p class="text-muted mb-0">Berikut adalah ringkasan data sistem hari ini</p>
        </div>
        <div class="text-muted">
            <i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 text-uppercase small fw-semibold">Total Mahasiswa</p>
                            <h2 class="mb-0 fw-bold" id="totalMahasiswa">0</h2>
                            <small class="text-success mt-2 d-block">
                                <i class="bi bi-arrow-up-short"></i> Aktif: <span id="aktifCount">0</span>
                            </small>
                        </div>
                        <div class="bg-soft-primary rounded-circle p-3">
                            <i class="bi bi-people fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 text-uppercase small fw-semibold">Total Petugas</p>
                            <h2 class="mb-0 fw-bold" id="totalPetugas">0</h2>
                        </div>
                        <div class="bg-soft-success rounded-circle p-3">
                            <i class="bi bi-person-badge fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 text-uppercase small fw-semibold">Total Fakultas</p>
                            <h2 class="mb-0 fw-bold" id="totalFakultas">0</h2>
                        </div>
                        <div class="bg-soft-info rounded-circle p-3">
                            <i class="bi bi-building fs-1 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 text-uppercase small fw-semibold">Surat Pending</p>
                            <h2 class="mb-0 fw-bold" id="totalPending">0</h2>
                        </div>
                        <div class="bg-soft-warning rounded-circle p-3">
                            <i class="bi bi-clock-history fs-1 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row - Ukuran diperkecil -->
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-graph-up me-2 text-primary"></i> Statistik Surat per Bulan
                        </h5>
                        <select id="yearFilter" class="form-select form-select-sm w-auto" style="width: 100px;">
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025" selected>2025</option>
                        </select>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <canvas id="suratChart" height="280" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-pie-chart me-2 text-primary"></i> Kategori Surat
                    </h5>
                </div>
                <div class="card-body pt-0 d-flex flex-column justify-content-center">
                    <canvas id="kategoriChart" height="250" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Surat Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-table me-2 text-primary"></i> Surat Terbaru
                        </h5>
                        <a href="#" class="btn btn-sm btn-primary">
                            <i class="bi bi-eye"></i> Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Surat</th>
                                    <th>Mahasiswa</th>
                                    <th>Jenis Surat</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="recentSuratBody">
                                <tr class="text-center">
                                    <td colspan="6" class="py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Memuat data...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background: rgba(111, 66, 193, 0.1); }
    .bg-soft-success { background: rgba(40, 167, 69, 0.1); }
    .bg-soft-info { background: rgba(23, 162, 184, 0.1); }
    .bg-soft-warning { background: rgba(255, 193, 7, 0.1); }
    .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
    .status-pending { background: #fef3c7; color: #d97706; }
    .status-approved { background: #d1fae5; color: #059669; }
    .status-rejected { background: #fee2e2; color: #dc2626; }
    
    /* Chart container */
    canvas {
        width: 100% !important;
        height: auto !important;
        max-height: 280px;
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let suratChart, kategoriChart;
    
    $(document).ready(function() {
        loadDashboardData();
        
        $('#yearFilter').change(function() {
            loadDashboardData($(this).val());
        });
    });
    
    function loadDashboardData(year = 2025) {
        $.ajax({
            url: '{{ route("admin.dashboard.data") }}',
            method: 'GET',
            data: { year: year },
            success: function(response) {
                console.log('Dashboard data:', response);
                
                $('#totalMahasiswa').text(response.totalMahasiswa || 0);
                $('#totalPetugas').text(response.totalPetugas || 0);
                $('#totalFakultas').text(response.totalFakultas || 0);
                $('#totalPending').text(response.totalPending || 0);
                $('#aktifCount').text(response.aktifCount || 0);
                
                updateCharts(response);
                updateTable(response.latestSurats || []);
            },
            error: function(xhr) {
                console.error('Error loading dashboard data:', xhr);
                loadFallbackData();
            }
        });
    }
    
    function updateCharts(data) {
        if (suratChart) suratChart.destroy();
        if (kategoriChart) kategoriChart.destroy();
        
        // Line Chart - Statistik Surat per Bulan
        const ctx1 = document.getElementById('suratChart').getContext('2d');
        suratChart = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: data.months || ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Jumlah Surat',
                    data: data.suratCounts || [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#6f42c1',
                    backgroundColor: 'rgba(111, 66, 193, 0.05)',
                    borderWidth: 2,
                    pointBackgroundColor: '#6f42c1',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 12, font: { size: 11 } }
                    },
                    tooltip: { 
                        backgroundColor: '#6f42c1',
                        titleFont: { size: 12 },
                        bodyFont: { size: 11 }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        ticks: { stepSize: 1, precision: 0, font: { size: 10 } },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: { 
                        ticks: { font: { size: 10 } },
                        grid: { display: false }
                    }
                }
            }
        });
        
        // Doughnut Chart - Kategori Surat
        const ctx2 = document.getElementById('kategoriChart').getContext('2d');
        kategoriChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: data.kategoriLabels || ['Surat Izin', 'Surat Keterangan', 'Surat Pengajuan'],
                datasets: [{
                    data: data.kategoriData || [0, 0, 0],
                    backgroundColor: ['#6f42c1', '#8b5cf6', '#a78bfa', '#c4b5fd', '#ddd6fe'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '55%',
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: { 
                            boxWidth: 10, 
                            font: { size: 10 },
                            padding: 10
                        }
                    },
                    tooltip: {
                        backgroundColor: '#6f42c1',
                        titleFont: { size: 11 },
                        bodyFont: { size: 10 },
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                return `${context.label}: ${context.raw} surat (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    function updateTable(surats) {
        const tbody = $('#recentSuratBody');
        if (!surats || surats.length === 0) {
            tbody.html('<td><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada data surat</td></tr>');
            return;
        }
        
        let html = '';
        surats.forEach(s => {
            const statusClass = s.status == 'pending' ? 'status-pending' : (s.status == 'approved' ? 'status-approved' : 'status-rejected');
            const statusText = s.status == 'pending' ? 'Pending' : (s.status == 'approved' ? 'Disetujui' : 'Ditolak');
            const statusIcon = s.status == 'pending' ? 'bi-clock' : (s.status == 'approved' ? 'bi-check-circle' : 'bi-x-circle');
            
            html += `
                <tr>
                    <td><span class="fw-semibold text-primary">#${s.id}</span></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-light p-2 me-2">
                                <i class="bi bi-person-circle text-secondary"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">${s.mahasiswa?.nama_lengkap || '-'}</div>
                                <small class="text-muted">${s.mahasiswa?.npm || '-'}</small>
                            </div>
                        </div>
                    </td>
                    <td>${s.jenis_surat?.nama_surat || '-'}</td>
                    <td>${formatDate(s.created_at)}</td>
                    <td><span class="${statusClass}"><i class="bi ${statusIcon} me-1"></i>${statusText}</span></td>
                    <td><button class="btn btn-sm btn-outline-primary" onclick="viewSurat(${s.id})"><i class="bi bi-eye"></i></button></td>
                </tr>
            `;
        });
        tbody.html(html);
    }
    
    function formatDate(date) {
        if (!date) return '-';
        return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    }
    
    function viewSurat(id) {
        Swal.fire({ title: 'Detail Surat', text: `Lihat detail surat #${id}`, icon: 'info', confirmButtonColor: '#6f42c1' });
    }
    
    function loadFallbackData() {
        updateCharts({});
        $('#totalMahasiswa').text('0');
        $('#totalPetugas').text('0');
        $('#totalFakultas').text('0');
        $('#totalPending').text('0');
        $('#aktifCount').text('0');
        updateTable([]);
    }
</script>
@endpush