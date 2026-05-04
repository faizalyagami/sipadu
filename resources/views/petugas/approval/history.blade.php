{{-- resources/views/petugas/approval/history.blade.php --}}
@extends('layouts.app')

@section('title', 'Riwayat Approve Surat')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%); border-radius: 15px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="text-white mb-1">Riwayat Persetujuan Surat</h3>
                            <p class="text-white-50 mb-0">Riwayat surat yang telah diproses (disetujui/ditolak)</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-white rounded-circle p-3 d-inline-block">
                                <i class="bi bi-clock-history fs-1 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('petugas.history') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Filter Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                <a href="{{ route('petugas.history') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-repeat"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-table text-primary me-2"></i>Data Riwayat Persetujuan
                        </h5>
                        <div>
                            <button class="btn btn-sm btn-success" onclick="exportExcel()">
                                <i class="bi bi-file-excel"></i> Export Excel
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="exportPDF()">
                                <i class="bi bi-file-pdf"></i> Export PDF
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="historyTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Mahasiswa</th>
                                    <th>NPM</th>
                                    <th>Jenis Surat</th>
                                    <th>Keperluan</th>
                                    <th>Status</th>
                                    <th>Tanggal Diproses</th>
                                    <th>Diproses Oleh</th>
                                    <th>Alasan (Jika Ditolak)</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($surats as $index => $surat)
                                <tr>
                                    <td>{{ $surats->firstItem() + $index }}</td>
                                    <td>{{ $surat->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="fw-semibold">{{ $surat->mahasiswa->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $surat->mahasiswa->npm ?? '-' }}</td>
                                    <td>{{ $surat->jenisSurat->nama_surat ?? '-' }}</td>
                                    <td>{{ Str::limit($surat->keperluan, 40) }}</td>
                                    <td>
                                        @if($surat->status == 'approved')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i> Disetujui
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i> Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $surat->approved_at ? $surat->approved_at->format('d/m/Y H:i') : '-' }}</td>
                                    <td>{{ $surat->approvedBy->name ?? '-' }}</td>
                                    <td>{{ Str::limit($surat->alasan_reject, 30) ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" onclick="detailSurat({{ $surat->id }})" data-bs-toggle="modal" data-bs-target="#detailModal">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                        <p class="text-muted mb-0">Belum ada riwayat persetujuan</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    {{ $surats->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Surat -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #6f42c1, #8b5cf6);">
                <h5 class="modal-title text-white">
                    <i class="bi bi-file-text me-2"></i>Detail Surat
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Export -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" id="exportContent">
        </div>
    </div>
</div>

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(111, 66, 193, 0.05);
        cursor: pointer;
    }
    .badge {
        font-size: 11px;
        padding: 5px 10px;
    }
</style>
@endpush

@push('scripts')
<script>
    function detailSurat(id) {
        $('#detailContent').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
        
        $.get(`/admin/surat/${id}/pdf`, function(response) {
            $('#detailContent').html(`
                <div style="max-height: 500px; overflow-y: auto;">
                    <div class="p-3">
                        <h6>Informasi Surat</h6>
                        <table class="table table-sm">
                            <tr><td width="150">ID Surat</td><td>: #${response.id}</td></tr>
                            <tr><td>Mahasiswa</td><td>: ${response.mahasiswa}</td></tr>
                            <tr><td>NPM</td><td>: ${response.npm}</td></tr>
                            <tr><td>Jenis Surat</td><td>: ${response.jenis_surat}</td></tr>
                            <tr><td>Keperluan</td><td>: ${response.keperluan}</td></tr>
                            <tr><td>Status</td><td>: ${response.status}</td></tr>
                            <tr><td>Tanggal Pengajuan</td><td>: ${response.created_at}</td></tr>
                            ${response.approved_at ? `<tr><td>Tanggal Diproses</td><td>: ${response.approved_at}</td></tr>` : ''}
                            ${response.alasan_reject ? `<tr><td>Alasan Ditolak</td><td>: ${response.alasan_reject}</td></tr>` : ''}
                        </table>
                        <hr>
                        <h6>Isi Surat</h6>
                        <div class="border p-3" style="background: #f8f9fa;">${response.content || 'Tidak ada konten'}</div>
                    </div>
                </div>
            `);
        }).fail(function() {
            $('#detailContent').html(`
                <div class="alert alert-danger m-3">
                    <i class="bi bi-exclamation-triangle"></i> Gagal memuat detail surat
                </div>
            `);
        });
    }
    
    function exportExcel() {
        let status = $('select[name="status"]').val();
        let start_date = $('input[name="start_date"]').val();
        let end_date = $('input[name="end_date"]').val();
        window.location.href = `/petugas/history/export?status=${status}&start_date=${start_date}&end_date=${end_date}&format=excel`;
    }
    
    function exportPDF() {
        let status = $('select[name="status"]').val();
        let start_date = $('input[name="start_date"]').val();
        let end_date = $('input[name="end_date"]').val();
        window.location.href = `/petugas/history/export?status=${status}&start_date=${start_date}&end_date=${end_date}&format=pdf`;
    }
    
    // Inisialisasi DataTable
    $(document).ready(function() {
        if ($('#historyTable').length && $.fn.DataTable) {
            if ($.fn.DataTable.isDataTable('#historyTable')) {
                $('#historyTable').DataTable().destroy();
            }
            $('#historyTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                columnDefs: [
                    { orderable: false, targets: [10] }
                ],
                ordering: true,
                order: [[7, 'desc']] // Sort by tanggal diproses descending
            });
        }
    });
</script>
@endpush
@endsection