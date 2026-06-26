{{-- resources/views/petugas/approval/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Approve Surat')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Surat Menunggu Persetujuan</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Mahasiswa</th>
                                <th>Jenis Surat</th>
                                <th>Keperluan</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($surats as $index => $surat)
                                <tr>
                                    <td>{{ $surats->firstItem() + $index }}</td>
                                    <td>
                                        {{ $surat->mahasiswa->nama_lengkap }}
                                        <br>
                                        <small class="text-muted">{{ $surat->mahasiswa->npm }}</small>
                                    </td>
                                    <td>{{ $surat->jenisSurat->nama_surat }}</td>
                                    <td>{{ Str::limit($surat->keperluan, 50) }}</td>
                                    <td>{{ $surat->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- Tombol Review -->
                                            <button type="button" class="btn btn-sm btn-info"
                                                onclick="openReviewModal({{ $surat->id }})">
                                                <i class="bi bi-eye"></i> Review
                                            </button>

                                            <!-- Approve -->
                                            <form action="{{ route('petugas.approval.approve', $surat->id) }}"
                                                method="POST" style="display: inline-block;"
                                                id="approveForm{{ $surat->id }}">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-success"
                                                    onclick="confirmApprove({{ $surat->id }})">
                                                    <i class="bi bi-check-lg"></i> Setujui
                                                </button>
                                            </form>

                                            <!-- Tolak -->
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="openRejectModal({{ $surat->id }})">
                                                <i class="bi bi-x-lg"></i> Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                        <p class="text-muted mb-0">Tidak ada surat yang menunggu persetujuan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $surats->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Approve -->
    <div class="modal fade" id="approveConfirmModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #28a745, #20c997);">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-check-circle me-2"></i>Konfirmasi Persetujuan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-shield-check fs-1 text-success"></i>
                    </div>
                    <p>Surat akan disetujui dan ditandatangani secara elektronik oleh:</p>
                    <ul>
                        <li><strong>Nama</strong> : Dr. Oki Mardiawan, M.Psi., Psikolog.</li>
                        <li><strong>NIP</strong> : D.07.0.464</li>
                        <li><strong>Jabatan</strong> : Wakil Dekan Bidang Pembelajaran dan Kemahasiswaan</li>
                    </ul>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Tanda tangan elektronik akan otomatis ditambahkan ke surat.
                    </div>
                    <div class="text-center mt-2">
                        <img src="{{ asset('images/ttd_dekan_cap.jpeg') }}" alt="Contoh TTD"
                            style="max-width: 150px; height: auto; border: 1px solid #ddd; border-radius: 5px; padding: 5px;">
                        <br>
                        <small class="text-muted">Preview TTD &amp; Cap</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="confirmApproveBtn">Ya, Setujui</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tolak -->
    <div class="modal fade" id="rejectModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-x-circle me-2"></i>Alasan Penolakan
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Silakan berikan alasan penolakan dengan jelas agar mahasiswa dapat memperbaiki pengajuannya.
                        </div>
                        <textarea name="alasan" id="rejectReason" class="form-control" rows="4" required
                            placeholder="Contoh: Data orang tua tidak lengkap, bukti pembayaran tidak jelas, dll."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger" id="confirmRejectBtn">
                            <i class="bi bi-x-lg"></i> Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Review Surat -->
    <div class="modal fade" id="reviewModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #6f42c1, #8b5cf6);">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-file-text me-2"></i>Review Surat Pengajuan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="reviewModalBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data surat...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success" id="reviewApproveBtn">
                        <i class="bi bi-check-lg"></i> Setujui
                    </button>
                    <button type="button" class="btn btn-danger" id="reviewRejectBtn">
                        <i class="bi bi-x-lg"></i> Tolak
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let selectedSuratId = null;
                let selectedSuratData = null;

                // ============================================
                // 1. OPEN REJECT MODAL
                // ============================================
                window.openRejectModal = function(id) {
                    selectedSuratId = id;
                    $('#rejectReason').val('');
                    $('#rejectForm').attr('action', `/petugas/approval/${id}/reject`);
                    $('#rejectModal').modal('show');
                };

                // ============================================
                // 2. CONFIRM APPROVE
                // ============================================
                window.confirmApprove = function(id) {
                    selectedSuratId = id;
                    $('#approveConfirmModal').modal('show');
                };

                // ============================================
                // 3. CONFIRM APPROVE BUTTON
                // ============================================
                $(document).on('click', '#confirmApproveBtn', function() {
                    if (selectedSuratId) {
                        $(this).prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');
                        $('#approveForm' + selectedSuratId).submit();
                    } else {
                        Swal.fire('Error', 'ID surat tidak ditemukan', 'error');
                    }
                });

                // ============================================
                // 4. RESET APPROVE MODAL
                // ============================================
                $('#approveConfirmModal').on('hidden.bs.modal', function() {
                    selectedSuratId = null;
                    $('#confirmApproveBtn').prop('disabled', false).html('Ya, Setujui');
                });

                // ============================================
                // 5. RESET REJECT MODAL
                // ============================================
                $('#rejectModal').on('hidden.bs.modal', function() {
                    selectedSuratId = null;
                    $('#rejectReason').val('');
                    $('#confirmRejectBtn').prop('disabled', false).html('<i class="bi bi-x-lg"></i> Tolak');
                });

                // ============================================
                // 6. REVIEW MODAL
                // ============================================
                window.openReviewModal = function(id) {
                    selectedSuratId = id;

                    $('#reviewModalBody').html(`
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Memuat data surat...</p>
                        </div>
                    `);

                    $('#reviewModal').modal('show');
                    loadSuratData(id);
                };

                function loadSuratData(id) {
                    $.ajax({
                        url: `/petugas/approval/${id}/data`,
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                selectedSuratData = response.data;
                                renderReviewModal(response.data);
                            } else {
                                showError('Gagal memuat data surat');
                            }
                        },
                        error: function() {
                            showError('Terjadi kesalahan saat memuat data');
                        }
                    });
                }

                function renderReviewModal(data) {
                    // Informasi dasar surat
                    let html = `
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Mahasiswa</label>
                                <p class="fw-semibold">${data.mahasiswa_nama}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small">NPM</label>
                                <p class="fw-semibold">${data.mahasiswa_npm}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small">Tanggal Pengajuan</label>
                                <p class="fw-semibold">${data.tanggal_pengajuan}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Jenis Surat</label>
                                <p class="fw-semibold">${data.jenis_surat}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Fakultas</label>
                                <p class="fw-semibold">${data.fakultas || '-'}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="text-muted small">Keperluan</label>
                                <div class="p-3 bg-light rounded">${data.keperluan}</div>
                            </div>
                        </div>
                    `;

                    // File Pendukung
                    if (data.file_ktm || data.bukti_pembayaran || data.file_pendukung) {
                        html += `
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h6 class="fw-bold"><i class="bi bi-files me-2"></i>File Pendukung</h6>
                                    <div class="d-flex gap-2 flex-wrap">
                                        ${data.file_ktm ? `<a href="/storage/${data.file_ktm}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-pdf"></i> KTM</a>` : ''}
                                        ${data.bukti_pembayaran ? `<a href="/storage/${data.bukti_pembayaran}" target="_blank" class="btn btn-sm btn-outline-success"><i class="bi bi-file-pdf"></i> Bukti Pembayaran</a>` : ''}
                                        ${data.file_pendukung ? `<a href="/storage/${data.file_pendukung}" target="_blank" class="btn btn-sm btn-outline-warning"><i class="bi bi-file-pdf"></i> File Pendukung</a>` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    // Preview Surat - Data orang tua sudah ada di dalam content surat
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

                    $('#reviewModalBody').html(html);
                }

                function showError(message) {
                    $('#reviewModalBody').html(`
                        <div class="alert alert-danger m-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            ${message}
                        </div>
                    `);
                }

                // ============================================
                // 7. REVIEW APPROVE
                // ============================================
                $('#reviewApproveBtn').on('click', function() {
                    if (selectedSuratId) {
                        $('#reviewModal').modal('hide');
                        setTimeout(function() {
                            confirmApprove(selectedSuratId);
                        }, 300);
                    }
                });

                // ============================================
                // 8. REVIEW REJECT
                // ============================================
                $('#reviewRejectBtn').on('click', function() {
                    if (selectedSuratId) {
                        $('#reviewModal').modal('hide');
                        setTimeout(function() {
                            openRejectModal(selectedSuratId);
                        }, 300);
                    }
                });

                // ============================================
                // 9. RESET REVIEW MODAL
                // ============================================
                $('#reviewModal').on('hidden.bs.modal', function() {
                    selectedSuratId = null;
                    selectedSuratData = null;
                });

                // ============================================
                // 10. PREVENT MODAL CLOSE ON BACKGROUND CLICK
                // ============================================
                $('.modal').on('click', function(e) {
                    if ($(e.target).hasClass('modal')) {
                        e.preventDefault();
                    }
                });
            });
        </script>
    @endpush
@endsection
