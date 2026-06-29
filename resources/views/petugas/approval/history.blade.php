@extends('layouts.app')

@section('title', 'Riwayat Approve Surat')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4"
                        style="background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%); border-radius: 15px;">
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
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                        Disetujui</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ request('end_date') }}">
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
                                                @if ($surat->status == 'approved')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i> Disetujui
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle me-1"></i> Ditolak
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $surat->approved_at ? $surat->approved_at->format('d/m/Y H:i') : '-' }}
                                            </td>
                                            <td>{{ $surat->approvedBy->name ?? '-' }}</td>
                                            <td>{{ Str::limit($surat->alasan_reject, 30) ?? '-' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info"
                                                    onclick="openHistoryDetail({{ $surat->id }})">
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
    <div class="modal fade" id="historyDetailModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #6f42c1, #8b5cf6);">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-file-text me-2"></i>Detail Surat
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="historyDetailBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data surat...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Tutup
                    </button>
                    @if (isset($surat) && $surat->status == 'approved' && $surat->nomor_surat)
                        <a href="#" class="btn btn-success" id="downloadHistorySurat">
                            <i class="bi bi-download me-1"></i> Download Surat
                        </a>
                    @endif
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

            /* Preview surat style */
            #historyDetailBody .surat-preview {
                font-family: 'Times New Roman', Times, serif;
                font-size: 12pt;
                line-height: 1.5;
            }

            #historyDetailBody .surat-preview table {
                border-collapse: collapse;
                width: 100%;
            }

            #historyDetailBody .surat-preview table td {
                padding: 4px 0;
                border: none;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Inisialisasi DataTable
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
                        lengthMenu: [
                            [10, 25, 50, -1],
                            [10, 25, 50, "Semua"]
                        ],
                        columnDefs: [{
                            orderable: false,
                            targets: [10]
                        }],
                        ordering: true,
                        order: [
                            [7, 'desc']
                        ]
                    });
                }
            });

            // ============================================
            // OPEN HISTORY DETAIL
            // ============================================
            function openHistoryDetail(id) {
                const modalBody = document.getElementById('historyDetailBody');
                if (!modalBody) return;

                // Tampilkan loading
                modalBody.innerHTML = `
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data surat...</p>
                    </div>
                `;

                // Tampilkan modal
                $('#historyDetailModal').modal('show');

                // Ambil data
                $.ajax({
                    url: `/petugas/history/${id}/detail`,
                    method: 'GET',
                    dataType: 'json',
                    timeout: 10000,
                    success: function(response) {
                        if (response.success) {
                            renderHistoryDetail(response.data);
                        } else {
                            showHistoryError('Gagal memuat data surat: ' + (response.message || ''));
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Terjadi kesalahan saat memuat data';
                        if (xhr.status === 404) {
                            msg = 'Data surat tidak ditemukan';
                        } else if (xhr.status === 0) {
                            msg = 'Koneksi terputus. Periksa koneksi internet Anda.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showHistoryError(msg);
                    }
                });
            }

            // ============================================
            // RENDER HISTORY DETAIL
            // ============================================
            function renderHistoryDetail(data) {
                const hasOrangTua = data.nama_ortu && data.nama_ortu !== '-' && data.nama_ortu !== '';

                // Status badge
                let statusBadge = '';
                if (data.status === 'approved') {
                    statusBadge = '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Disetujui</span>';
                } else {
                    statusBadge = '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Ditolak</span>';
                }

                let html = `
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted small">Mahasiswa</label>
                            <p class="fw-semibold">${data.mahasiswa_nama || '-'}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">NPM</label>
                            <p class="fw-semibold">${data.mahasiswa_npm || '-'}</p>
                        </div>
                        <div class="col-md-5">
                            <label class="text-muted small">Status</label>
                            <p>${statusBadge}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Jenis Surat</label>
                            <p class="fw-semibold">${data.jenis_surat || '-'}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Fakultas</label>
                            <p class="fw-semibold">${data.fakultas || '-'}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Tanggal Pengajuan</label>
                            <p class="fw-semibold">${data.tanggal_pengajuan || '-'}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Tanggal Diproses</label>
                            <p class="fw-semibold">${data.tanggal_diproses || '-'}</p>
                        </div>
                    </div>
                `;

                // Tampilkan nomor surat jika ada
                if (data.nomor_surat) {
                    html += `
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="text-muted small">Nomor Surat</label>
                                <p class="fw-bold text-primary">${data.nomor_surat}</p>
                            </div>
                        </div>
                    `;
                }

                // Tampilkan data orang tua jika ada
                if (hasOrangTua) {
                    html += `
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card border-primary bg-light">
                                    <div class="card-header bg-primary text-white">
                                        <i class="bi bi-people me-2"></i> Data Orang Tua / Wali Mahasiswa
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="text-muted small">Nama Orang Tua</label>
                                                <p class="fw-semibold">${data.nama_ortu || '-'}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">NRP/NIK/NIP</label>
                                                <p class="fw-semibold">${data.nik_ortu || '-'}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">Pangkat/Golongan</label>
                                                <p class="fw-semibold">${data.pangkat_ortu || '-'}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">Instansi</label>
                                                <p class="fw-semibold">${data.instansi_ortu || '-'}</p>
                                            </div>
                                            <div class="col-12">
                                                <label class="text-muted small">Alamat Kantor</label>
                                                <p class="fw-semibold">${data.alamat_kantor_ortu || '-'}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }

                // Keperluan
                html += `
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="text-muted small">Keperluan</label>
                            <div class="p-3 bg-light rounded">${data.keperluan || '-'}</div>
                        </div>
                    </div>
                `;

                // Alasan reject jika ada
                if (data.status === 'rejected' && data.alasan_reject) {
                    html += `
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-danger">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Alasan Ditolak:</strong> ${data.alasan_reject}
                                </div>
                            </div>
                        </div>
                    `;
                }

                // File pendukung
                if (data.file_ktm || data.bukti_pembayaran || data.file_pendukung) {
                    html += `
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="fw-bold"><i class="bi bi-files me-2"></i>File Pendukung</h6>
                                <div class="d-flex gap-2 flex-wrap">
                                    ${data.file_ktm ? `<a href="/storage/${data.file_ktm}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-pdf me-1"></i> KTM</a>` : ''}
                                    ${data.bukti_pembayaran ? `<a href="/storage/${data.bukti_pembayaran}" target="_blank" class="btn btn-sm btn-outline-success"><i class="bi bi-file-pdf me-1"></i> Bukti Pembayaran</a>` : ''}
                                    ${data.file_pendukung ? `<a href="/storage/${data.file_pendukung}" target="_blank" class="btn btn-sm btn-outline-warning"><i class="bi bi-file-pdf me-1"></i> File Pendukung</a>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                }

                // Preview Surat
                html += `
                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-bold"><i class="bi bi-file-text me-2"></i>Preview Surat</h6>
                            <div class="border rounded p-0" style="max-height: 600px; overflow-y: auto; background: #f5f5f5;">
                                <div style="max-width: 210mm; margin: 0 auto; padding: 15mm; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.05); font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5;">
                                    ${data.content || '<p class="text-muted text-center">Tidak ada konten surat</p>'}
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('historyDetailBody').innerHTML = html;

                // Set download button jika ada nomor surat
                if (data.nomor_surat && data.status === 'approved') {
                    const downloadBtn = document.getElementById('downloadHistorySurat');
                    if (downloadBtn) {
                        downloadBtn.href = `/mahasiswa/pengajuan/${data.id}/download`;
                        downloadBtn.style.display = 'inline-flex';
                    }
                }
            }

            // ============================================
            // SHOW ERROR
            // ============================================
            function showHistoryError(message) {
                document.getElementById('historyDetailBody').innerHTML = `
                    <div class="alert alert-danger m-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${message}
                        <br><br>
                        <button class="btn btn-sm btn-primary" onclick="openHistoryDetail(${selectedHistoryId})">
                            <i class="bi bi-arrow-repeat me-1"></i> Coba Lagi
                        </button>
                    </div>
                `;
            }

            // ============================================
            // EXPORT FUNCTIONS
            // ============================================
            function exportExcel() {
                let status = $('select[name="status"]').val() || '';
                let start_date = $('input[name="start_date"]').val() || '';
                let end_date = $('input[name="end_date"]').val() || '';
                window.location.href =
                    `/petugas/history/export?status=${status}&start_date=${start_date}&end_date=${end_date}&format=excel`;
            }

            function exportPDF() {
                let status = $('select[name="status"]').val() || '';
                let start_date = $('input[name="start_date"]').val() || '';
                let end_date = $('input[name="end_date"]').val() || '';
                window.location.href =
                    `/petugas/history/export?status=${status}&start_date=${start_date}&end_date=${end_date}&format=pdf`;
            }

            // ============================================
            // MODAL CLEANUP
            // ============================================
            $('#historyDetailModal').on('hidden.bs.modal', function() {
                // Reset body
                document.getElementById('historyDetailBody').innerHTML = `
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data surat...</p>
                    </div>
                `;

                // Hapus backdrop
                document.querySelectorAll('.modal-backdrop').forEach(function(el) {
                    el.remove();
                });
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });
        </script>
    @endpush
@endsection
