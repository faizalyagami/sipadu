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
                                            {{-- <button type="button" class="btn btn-sm btn-outline-info btn-detail"
                                                data-id="{{ $surat->id }}" data-bs-toggle="modal"
                                                data-bs-target="#detailModal{{ $surat->id }}" title="Detail Surat">
                                                <i class="bi bi-eye"></i>
                                            </button> --}}

                                            @if ($surat->status == 'approved')
                                                <a href="{{ route('mahasiswa.pengajuan.download', $surat->id) }}"
                                                    class="btn btn-sm btn-outline-success download-btn"
                                                    title="Download Surat" data-id="{{ $surat->id }}">
                                                    <i class="bi bi-download me-1"></i> Download
                                                </a>
                                            @endif

                                            @if ($surat->status == 'rejected' && $surat->alasan_reject)
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-alasan"
                                                    data-bs-toggle="popover" data-bs-placement="top"
                                                    data-bs-content="{{ $surat->alasan_reject }}" title="Alasan Ditolak">
                                                    <i class="bi bi-info-circle"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Detail -->
                                {{-- <div class="modal fade" id="detailModal{{ $surat->id }}" tabindex="-1"
                                    aria-labelledby="detailModalLabel{{ $surat->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header"
                                                style="background: linear-gradient(135deg, #6f42c1, #8b5cf6);">
                                                <h5 class="modal-title text-white"
                                                    id="detailModalLabel{{ $surat->id }}">
                                                    <i class="bi bi-file-text me-2"></i>Detail Surat
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="text-muted small">Jenis Surat</label>
                                                            <p class="fw-semibold">
                                                                {{ $surat->jenisSurat->nama_surat ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="text-muted small">Status</label>
                                                            <div>
                                                                @if ($surat->status == 'pending')
                                                                    <span
                                                                        class="badge bg-warning text-dark">Menunggu</span>
                                                                @elseif($surat->status == 'approved')
                                                                    <span class="badge bg-success">Disetujui</span>
                                                                @else
                                                                    <span class="badge bg-danger">Ditolak</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label class="text-muted small">Tanggal Pengajuan</label>
                                                            <p>{{ $surat->created_at->format('d F Y H:i') }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label class="text-muted small">Keperluan</label>
                                                            <div class="p-3 bg-light rounded">{{ $surat->keperluan }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if ($surat->status == 'approved' && $surat->approved_at)
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label class="text-muted small">Tanggal Disetujui</label>
                                                                <p>{{ $surat->approved_at->format('d F Y H:i') }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($surat->status == 'rejected' && $surat->alasan_reject)
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label class="text-muted small text-danger">Alasan
                                                                    Ditolak</label>
                                                                <div
                                                                    class="p-3 bg-danger bg-opacity-10 rounded text-danger">
                                                                    {{ $surat->alasan_reject }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($surat->file_ktm)
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label class="text-muted small">File KTM</label>
                                                                <div>
                                                                    <a href="{{ asset('storage/' . $surat->file_ktm) }}"
                                                                        target="_blank"
                                                                        class="btn btn-sm btn-outline-primary">
                                                                        <i class="bi bi-file-pdf"></i> Lihat File
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($surat->bukti_pembayaran)
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label class="text-muted small">Bukti Pembayaran</label>
                                                                <div>
                                                                    <a href="{{ asset('storage/' . $surat->bukti_pembayaran) }}"
                                                                        target="_blank"
                                                                        class="btn btn-sm btn-outline-primary">
                                                                        <i class="bi bi-file-pdf"></i> Lihat File
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                @if ($surat->status == 'approved')
                                                    <a href="{{ route('mahasiswa.pengajuan.download', $surat->id) }}"
                                                        class="btn btn-sm btn-outline-success download-btn">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
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
@endsection

@push('scripts')
    <script>
        // ============================================
        // VARIABEL GLOBAL - HANYA SATU DEKLARASI
        // ============================================
        // Gunakan let atau const, hindari var untuk menghindari hoisting issues
        let suratTableInstance = null;

        document.addEventListener('DOMContentLoaded', function() {

            // ============================================
            // 2. POPOVER INITIALIZATION
            // ============================================
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.forEach(function(popoverTriggerEl) {
                new bootstrap.Popover(popoverTriggerEl, {
                    trigger: 'hover focus',
                    delay: {
                        show: 200,
                        hide: 100
                    }
                });
            });

            // ============================================
            // 3. FILTER FUNCTION
            // ============================================
            window.filterTable = function() {
                var status = document.getElementById('filterStatus').value;
                var date = document.getElementById('filterDate').value;
                var rows = document.querySelectorAll('#suratTable tbody tr:not(.empty-row)');

                rows.forEach(function(row) {
                    var rowStatus = row.getAttribute('data-status');
                    var rowDate = row.getAttribute('data-date');
                    var show = true;

                    if (status && rowStatus !== status) show = false;
                    if (date && rowDate !== date) show = false;

                    row.style.display = show ? '' : 'none';
                });
            };

            window.resetFilter = function() {
                document.getElementById('filterStatus').value = '';
                document.getElementById('filterDate').value = '';
                filterTable();
            };

            // ============================================
            // 4. MODAL HANDLER - Fix kedip-kedip
            // ============================================
            document.querySelectorAll('[data-bs-toggle="modal"]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    var target = this.getAttribute('data-bs-target');
                    var modalId = target.replace('#', '');
                    var modal = document.getElementById(modalId);

                    if (modal) {
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                    }
                });
            });

            // Bersihkan backdrop saat modal ditutup
            document.querySelectorAll('.modal').forEach(function(modal) {
                modal.addEventListener('hidden.bs.modal', function() {
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                });
            });

            // ============================================
            // 5. FIX: CLOSE MODAL WITH ESC
            // ============================================
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    var openModals = document.querySelectorAll('.modal.show');
                    openModals.forEach(function(modal) {
                        var instance = bootstrap.Modal.getInstance(modal);
                        if (instance) {
                            instance.hide();
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                        }
                    });
                }
            });

            // ============================================
            // 6. CLEANUP BACKDROP SAAT LOAD
            // ============================================
            window.addEventListener('load', function() {
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });
        });

        // ============================================
        // 7. HANDLE DOWNLOAD (global function)
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            // Gunakan event delegation untuk tombol download
            document.addEventListener('click', function(e) {
                var target = e.target.closest('.download-btn');
                if (!target) return;

                e.preventDefault();

                var $this = target;
                var originalText = $this.innerHTML;
                var url = $this.getAttribute('href');
                var suratId = $this.dataset.id || 'surat';

                // Tampilkan loading
                Swal.fire({
                    title: 'Memproses Surat',
                    text: 'Sedang menyiapkan file PDF...',
                    allowOutsideClick: false,
                    didOpen: function() {
                        Swal.showLoading();
                    }
                });

                // Disable button
                $this.innerHTML = `
            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> 
            Memproses...
        `;
                $this.classList.add('disabled');

                // Gunakan XMLHttpRequest untuk download dengan progress
                var xhr = new XMLHttpRequest();
                xhr.open('GET', url, true);
                xhr.responseType = 'blob';

                xhr.onload = function() {
                    Swal.close();

                    if (this.status === 200) {
                        var blob = this.response;
                        var blobUrl = URL.createObjectURL(blob);

                        var link = document.createElement('a');
                        link.href = blobUrl;
                        link.download = 'surat_' + suratId + '.pdf';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        setTimeout(function() {
                            URL.revokeObjectURL(blobUrl);
                        }, 1000);

                        // Reset button
                        $this.innerHTML = originalText;
                        $this.classList.remove('disabled');

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Surat berhasil diunduh',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        // Fallback
                        window.location.href = url;
                        setTimeout(function() {
                            $this.innerHTML = originalText;
                            $this.classList.remove('disabled');
                        }, 3000);
                    }
                };

                xhr.onerror = function() {
                    Swal.close();
                    // Fallback
                    window.location.href = url;
                    setTimeout(function() {
                        $this.innerHTML = originalText;
                        $this.classList.remove('disabled');
                    }, 3000);
                };

                xhr.send();
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        /* ============================================
                                                                       TABLE STYLES
                                                                    ============================================ */
        .table-hover tbody tr:hover {
            background-color: rgba(111, 66, 193, 0.05);
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        /* ============================================
                                                                       BADGE STYLES
                                                                    ============================================ */
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

        /* ============================================
                                                                       BUTTON GROUP STYLES
                                                                    ============================================ */
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

        /* ============================================
                                                                       DOWNLOAD BUTTON
                                                                    ============================================ */
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

        /* ============================================
                                                                       SPINNER
                                                                    ============================================ */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.15em;
        }

        /* ============================================
                                                                       POPOVER
                                                                    ============================================ */
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

        /* ============================================
                                                                       FIX MODAL KEDIP
                                                                    ============================================ */
        .modal {
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out, opacity 0.15s linear;
        }

        .modal.show .modal-dialog {
            transform: none;
        }

        /* Prevent double backdrop */
        .modal-backdrop {
            opacity: 0.5 !important;
        }

        .modal-backdrop.fade {
            opacity: 0;
        }

        .modal-backdrop.show {
            opacity: 0.5 !important;
        }

        /* Fix z-index */
        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1050 !important;
        }

        .modal-dialog {
            z-index: 1060 !important;
        }

        .modal-content {
            z-index: 1070 !important;
        }
    </style>
@endpush
