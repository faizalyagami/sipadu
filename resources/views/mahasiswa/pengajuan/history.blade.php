{{-- resources/views/mahasiswa/pengajuan/history.blade.php --}}
@extends('layouts.app')

@section('title', 'Riwayat Surat')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4"
                        style="background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%); border-radius: 16px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-white mb-1 fw-bold">
                                    <i class="bi bi-archive me-2"></i>Riwayat Surat
                                </h4>
                                <p class="text-white-50 mb-0">Daftar seluruh pengajuan surat yang telah Anda buat</p>
                            </div>
                            <a href="{{ route('mahasiswa.pengajuan.index') }}" class="btn btn-light">
                                <i class="bi bi-plus-circle me-2"></i>Buat Surat Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Total</h6>
                        <h3 class="mb-0 fw-bold text-primary">
                            {{ $surats->count() }}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Menunggu</h6>
                        <h3 class="mb-0 fw-bold text-warning">
                            {{ $surats->where('status', 'pending')->count() }}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Disetujui</h6>
                        <h3 class="mb-0 fw-bold text-success">
                            {{ $surats->where('status', 'approved')->count() }}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Ditolak</h6>
                        <h3 class="mb-0 fw-bold text-danger">
                            {{ $surats->where('status', 'rejected')->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <select id="filterStatus" class="form-select form-select-sm">
                                    <option value="">Semua Status</option>
                                    <option value="pending">Menunggu</option>
                                    <option value="approved">Disetujui</option>
                                    <option value="rejected">Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" id="filterDate" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-sm btn-primary" onclick="filterTable()">
                                    <i class="bi bi-filter"></i> Filter
                                </button>
                                <button class="btn btn-sm btn-secondary" onclick="resetFilter()">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-table me-2 text-primary"></i>Data Pengajuan Surat
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="suratTable">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Jenis Surat</th>
                                <th>Keperluan</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Status</th>
                                <th width="180">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($surats as $index => $surat)
                                <tr data-status="{{ $surat->status }}"
                                    data-date="{{ $surat->created_at->format('Y-m-d') }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="fw-semibold">{{ $surat->jenisSurat->nama_surat ?? '-' }}</span>
                                        <br>
                                        <small
                                            class="text-muted">{{ $surat->jenisSurat->kategoriSurat->nama_kategori ?? '-' }}</small>
                                    </td>
                                    <td>{{ Str::limit($surat->keperluan, 60) }}</td>
                                    <td>
                                        {{ $surat->created_at->format('d/m/Y H:i') }}
                                        <br>
                                        <small class="text-muted">{{ $surat->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        @if ($surat->status == 'pending')
                                            <span class="badge bg-warning text-dark">
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
                                        <div class="btn-group" role="group">
                                            <!-- Tombol Detail - menggunakan onclick -->
                                            <button type="button" class="btn btn-sm btn-outline-info"
                                                onclick="openDetailModal({{ $surat->id }})" title="Detail Surat">
                                                <i class="bi bi-eye"></i>
                                            </button>

                                            @if ($surat->status == 'approved')
                                                <a href="{{ route('mahasiswa.pengajuan.download', $surat->id) }}"
                                                    class="btn btn-sm btn-outline-success download-btn"
                                                    title="Download Surat" onclick="return handleDownload(event, this)">
                                                    <i class="bi bi-download me-1"></i> Download
                                                </a>
                                            @endif

                                            @if ($surat->status == 'rejected' && $surat->alasan_reject)
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="popover" data-bs-placement="top"
                                                    data-bs-content="{{ $surat->alasan_reject }}" title="Alasan Ditolak">
                                                    <i class="bi bi-info-circle"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                        <h6 class="text-muted">Belum ada pengajuan surat</h6>
                                        <p class="text-muted small">Silakan buat surat baru melalui menu "Buat Surat Baru"
                                        </p>
                                        <a href="{{ route('mahasiswa.pengajuan.index') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-2"></i>Buat Surat Baru
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Menampilkan {{ $surats->count() }} data
                        @if (method_exists($surats, 'total'))
                            dari {{ $surats->total() }} data
                        @endif
                    </div>
                    @if (method_exists($surats, 'links'))
                        {{ $surats->links('pagination::bootstrap-5') }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL DETAIL - SATU MODAL UNTUK SEMUA -->
    <!-- ============================================ -->
    <div class="modal fade" id="detailModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #6f42c1, #8b5cf6);">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-file-text me-2"></i>Detail Surat
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailModalBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data surat...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="#" id="detailDownloadBtn" class="btn btn-success" style="display: none;">
                        <i class="bi bi-download"></i> Download Surat
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // ============================================
            // 1. OPEN DETAIL MODAL
            // ============================================
            let currentDetailSuratId = null;

            function openDetailModal(id) {
                currentDetailSuratId = id;

                // Tampilkan loading
                document.getElementById('detailModalBody').innerHTML = `
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data surat...</p>
                    </div>
                `;

                // Sembunyikan tombol download
                document.getElementById('detailDownloadBtn').style.display = 'none';

                // Buka modal
                var modal = new bootstrap.Modal(document.getElementById('detailModal'));
                modal.show();

                // Load data surat
                loadDetailSurat(id);
            }

            // ============================================
            // 2. LOAD DETAIL SURAT
            // ============================================
            function loadDetailSurat(id) {
                // Cari data dari tabel (data sudah ada di DOM)
                var rows = document.querySelectorAll('#suratTable tbody tr');
                var foundRow = null;

                rows.forEach(function(row) {
                    // Cari berdasarkan tombol detail di row
                    var detailBtn = row.querySelector('button[onclick*="openDetailModal(' + id + ')"]');
                    if (detailBtn) {
                        foundRow = row;
                    }
                });

                if (foundRow) {
                    // Ekstrak data dari row
                    var cells = foundRow.querySelectorAll('td');
                    var jenisSurat = cells[1].textContent.trim();
                    var keperluan = cells[2].textContent.trim();
                    var tanggal = cells[3].textContent.trim();
                    var statusBadge = cells[4].querySelector('.badge');
                    var statusText = statusBadge ? statusBadge.textContent.trim() : '-';
                    var statusClass = statusBadge ? statusBadge.className : '';

                    // Cari alasan reject di popover
                    var rejectBtn = foundRow.querySelector('[data-bs-toggle="popover"]');
                    var alasanReject = rejectBtn ? rejectBtn.getAttribute('data-bs-content') : null;

                    // Cari file KTM
                    var fileKtm = null;
                    var fileKtmLink = foundRow.querySelector('a[href*="storage"][href*="ktm"]');
                    if (fileKtmLink) {
                        fileKtm = fileKtmLink.getAttribute('href');
                    }

                    // Cari bukti pembayaran
                    var fileBukti = null;
                    var fileBuktiLink = foundRow.querySelector('a[href*="storage"][href*="pembayaran"]');
                    if (fileBuktiLink) {
                        fileBukti = fileBuktiLink.getAttribute('href');
                    }

                    // Cek apakah status approved
                    var isApproved = statusText.toLowerCase().includes('disetujui');

                    // Render detail
                    renderDetailModal(jenisSurat, keperluan, tanggal, statusText, statusClass, alasanReject, isApproved, id,
                        fileKtm, fileBukti);

                } else {
                    // Fallback: gunakan AJAX
                    $.ajax({
                        url: '/mahasiswa/pengajuan/detail/' + id,
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                renderDetailModal(
                                    response.data.jenis_surat,
                                    response.data.keperluan,
                                    response.data.tanggal,
                                    response.data.status,
                                    response.data.status_class,
                                    response.data.alasan_reject,
                                    response.data.is_approved,
                                    id,
                                    response.data.file_ktm,
                                    response.data.bukti_pembayaran
                                );
                            } else {
                                showDetailError('Gagal memuat data surat');
                            }
                        },
                        error: function() {
                            showDetailError('Terjadi kesalahan saat memuat data');
                        }
                    });
                }
            }

            // ============================================
            // 3. RENDER DETAIL MODAL
            // ============================================
            function renderDetailModal(jenisSurat, keperluan, tanggal, statusText, statusClass, alasanReject, isApproved, id,
                fileKtm, fileBukti) {
                var statusIcon = '';
                var statusBadgeColor = '';

                if (statusText.toLowerCase().includes('menunggu')) {
                    statusIcon = 'bi-clock';
                    statusBadgeColor = 'bg-warning text-dark';
                } else if (statusText.toLowerCase().includes('disetujui')) {
                    statusIcon = 'bi-check-circle';
                    statusBadgeColor = 'bg-success';
                } else {
                    statusIcon = 'bi-x-circle';
                    statusBadgeColor = 'bg-danger';
                }

                var html = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Jenis Surat</label>
                                <p class="fw-semibold">${jenisSurat || '-'}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Status</label>
                                <div>
                                    <span class="badge ${statusBadgeColor}">
                                        <i class="bi ${statusIcon} me-1"></i> ${statusText}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="text-muted small">Tanggal Pengajuan</label>
                                <p>${tanggal || '-'}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="text-muted small">Keperluan</label>
                                <div class="p-3 bg-light rounded">${keperluan || '-'}</div>
                            </div>
                        </div>
                `;

                if (alasanReject) {
                    html += `
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="text-muted small text-danger">Alasan Ditolak</label>
                                <div class="p-3 bg-danger bg-opacity-10 rounded text-danger">
                                    ${alasanReject}
                                </div>
                            </div>
                        </div>
                    `;
                }

                if (fileKtm) {
                    html += `
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="text-muted small">File KTM</label>
                                <div>
                                    <a href="${fileKtm}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-pdf"></i> Lihat File
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                }

                if (fileBukti) {
                    html += `
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="text-muted small">Bukti Pembayaran</label>
                                <div>
                                    <a href="${fileBukti}" target="_blank" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-file-pdf"></i> Lihat File
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                }

                html += `</div>`;

                document.getElementById('detailModalBody').innerHTML = html;

                // Tampilkan tombol download jika status approved
                var downloadBtn = document.getElementById('detailDownloadBtn');
                if (isApproved) {
                    downloadBtn.style.display = 'inline-block';
                    downloadBtn.href = '/mahasiswa/pengajuan/' + id + '/download';
                } else {
                    downloadBtn.style.display = 'none';
                }
            }

            // ============================================
            // 4. SHOW DETAIL ERROR
            // ============================================
            function showDetailError(message) {
                document.getElementById('detailModalBody').innerHTML = `
                    <div class="alert alert-danger m-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${message}
                    </div>
                `;
            }

            // ============================================
            // 5. DOWNLOAD BUTTON HANDLER
            // ============================================
            $(document).ready(function() {
                $(document).on('click', '.download-btn', function(e) {
                    e.preventDefault();

                    var $this = $(this);
                    var originalText = $this.html();
                    var url = $this.attr('href');

                    $this.html(
                        '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...'
                    );
                    $this.addClass('disabled');

                    window.location.href = url;

                    setTimeout(function() {
                        $this.html(originalText);
                        $this.removeClass('disabled');
                    }, 5000);
                });
            });

            // ============================================
            // 6. POPOVER INITIALIZATION
            // ============================================
            document.addEventListener('DOMContentLoaded', function() {
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl, {
                        trigger: 'hover focus',
                        delay: {
                            show: 200,
                            hide: 100
                        }
                    });
                });
            });

            // ============================================
            // 7. FILTER FUNCTION
            // ============================================
            function filterTable() {
                var status = document.getElementById('filterStatus').value;
                var date = document.getElementById('filterDate').value;
                var rows = document.querySelectorAll('#suratTable tbody tr');

                rows.forEach(function(row) {
                    var rowStatus = row.getAttribute('data-status');
                    var rowDate = row.getAttribute('data-date');
                    var show = true;

                    if (status && rowStatus !== status) show = false;
                    if (date && rowDate !== date) show = false;

                    row.style.display = show ? '' : 'none';
                });
            }

            function resetFilter() {
                document.getElementById('filterStatus').value = '';
                document.getElementById('filterDate').value = '';
                filterTable();
            }

            // ============================================
            // 8. PREVENT MODAL CLOSE ON BACKGROUND CLICK
            // ============================================
            document.addEventListener('DOMContentLoaded', function() {
                var modals = document.querySelectorAll('.modal');
                modals.forEach(function(modal) {
                    modal.addEventListener('click', function(e) {
                        if (e.target === this) {
                            // Biarkan modal tetap terbuka karena data-bs-backdrop="static"
                            // Tidak ada aksi
                        }
                    });
                });
            });

            // ============================================
            // 9. RESET DETAIL MODAL
            // ============================================
            document.getElementById('detailModal').addEventListener('hidden.bs.modal', function() {
                currentDetailSuratId = null;
            });
        </script>
    @endpush

    @push('styles')
        <style>
            /* TABLE STYLES */
            .table-hover tbody tr:hover {
                background-color: rgba(111, 66, 193, 0.05);
                cursor: pointer;
                transition: background-color 0.2s ease;
            }

            /* BADGE STYLES */
            .badge {
                font-size: 0.75rem;
                padding: 0.35rem 0.65rem;
                font-weight: 600;
                border-radius: 20px;
            }

            .badge.bg-warning {
                background: #ffc107 !important;
                color: #212529;
            }

            .badge.bg-success {
                background: #28a745 !important;
                color: white;
            }

            .badge.bg-danger {
                background: #dc3545 !important;
                color: white;
            }

            /* BUTTON GROUP STYLES */
            .btn-group .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
                border-radius: 6px;
            }

            .btn-group .btn i {
                font-size: 0.9rem;
            }

            .btn-group .btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }

            /* DOWNLOAD BUTTON */
            .download-btn {
                transition: all 0.3s ease;
            }

            .download-btn:hover:not(.disabled) {
                transform: translateY(-1px);
                box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
            }

            .download-btn.disabled {
                opacity: 0.7;
                cursor: not-allowed;
            }

            /* SPINNER */
            .spinner-border-sm {
                width: 1rem;
                height: 1rem;
                border-width: 0.15em;
            }

            /* POPOVER */
            .popover {
                max-width: 300px;
                border: none;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
            }

            .popover-header {
                background: #dc3545;
                color: white;
                border-radius: 10px 10px 0 0;
                font-weight: 600;
                padding: 8px 14px;
            }

            .popover-body {
                padding: 10px 14px;
                font-size: 0.85rem;
                color: #333;
            }

            /* MODAL */
            .modal.fade .modal-dialog {
                transform: scale(0.95);
                transition: transform 0.2s ease;
            }

            .modal.show .modal-dialog {
                transform: scale(1);
            }
        </style>
    @endpush
@endsection
