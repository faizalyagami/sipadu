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
                                <tr id="surat-row-{{ $surat->id }}">
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
                                            <button type="button" class="btn btn-sm btn-success btn-approve"
                                                data-id="{{ $surat->id }}" onclick="confirmApprove({{ $surat->id }})">
                                                <i class="bi bi-check-lg"></i> Setujui
                                            </button>

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
    <div class="modal fade" id="approveConfirmModal" tabindex="-1" aria-labelledby="approveConfirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #28a745, #20c997);">
                    <h5 class="modal-title text-white" id="approveConfirmModalLabel">
                        <i class="bi bi-check-circle me-2"></i>Konfirmasi Persetujuan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
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
                    <div id="approveLoading" style="display: none;" class="text-center mt-3">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Memproses...</span>
                        </div>
                        <p class="mt-2 text-muted">Sedang memproses persetujuan...</p>
                    </div>
                    <div id="approveError" style="display: none;" class="alert alert-danger mt-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <span id="approveErrorMessage">Terjadi kesalahan. Silakan coba lagi.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" tabindex="0">Batal</button>
                    <button type="button" class="btn btn-success" id="confirmApproveBtn" tabindex="0">
                        <i class="bi bi-check-lg me-1"></i> Ya, Setujui
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tolak -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="rejectModalLabel">
                            <i class="bi bi-x-circle me-2"></i>Alasan Penolakan
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Silakan berikan alasan penolakan dengan jelas agar mahasiswa dapat memperbaiki pengajuannya.
                        </div>
                        <textarea name="alasan" id="rejectReason" class="form-control" rows="4" required
                            placeholder="Contoh: Data orang tua tidak lengkap, bukti pembayaran tidak jelas, dll."></textarea>
                        <div id="rejectLoading" style="display: none;" class="text-center mt-3">
                            <div class="spinner-border text-danger" role="status">
                                <span class="visually-hidden">Memproses...</span>
                            </div>
                            <p class="mt-2 text-muted">Sedang memproses penolakan...</p>
                        </div>
                        <div id="rejectError" style="display: none;" class="alert alert-danger mt-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <span id="rejectErrorMessage">Terjadi kesalahan. Silakan coba lagi.</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            tabindex="0">Batal</button>
                        <button type="submit" class="btn btn-danger" id="confirmRejectBtn" tabindex="0">
                            <i class="bi bi-x-lg me-1"></i> Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Review Surat -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #6f42c1, #8b5cf6);">
                    <h5 class="modal-title text-white" id="reviewModalLabel">
                        <i class="bi bi-file-text me-2"></i>Review Surat Pengajuan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" tabindex="0">
                        <i class="bi bi-x-circle me-1"></i> Tutup
                    </button>
                    <button type="button" class="btn btn-success" id="reviewApproveBtn" tabindex="0">
                        <i class="bi bi-check-lg me-1"></i> Setujui
                    </button>
                    <button type="button" class="btn btn-danger" id="reviewRejectBtn" tabindex="0">
                        <i class="bi bi-x-lg me-1"></i> Tolak
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
                let isProcessing = false;
                let currentModal = null;

                // ============================================
                // 1. OPEN REJECT MODAL
                // ============================================
                window.openRejectModal = function(id) {
                    if (isProcessing) return;

                    console.log('openRejectModal called with id:', id);
                    selectedSuratId = id;
                    $('#rejectReason').val('');
                    $('#rejectForm').attr('action', `/petugas/approval/${id}/reject`);
                    $('#rejectLoading').hide();
                    $('#rejectError').hide();
                    $('#confirmRejectBtn').prop('disabled', false).html('<i class="bi bi-x-lg me-1"></i> Tolak');
                    currentModal = 'reject';
                    $('#rejectModal').modal('show');

                    setTimeout(function() {
                        $('#rejectReason').focus();
                    }, 500);
                };

                // ============================================
                // 2. CONFIRM APPROVE
                // ============================================
                window.confirmApprove = function(id) {
                    if (isProcessing) return;

                    console.log('confirmApprove called with id:', id);
                    selectedSuratId = id;
                    $('#approveLoading').hide();
                    $('#approveError').hide();
                    $('#confirmApproveBtn').prop('disabled', false).html(
                        '<i class="bi bi-check-lg me-1"></i> Ya, Setujui');
                    currentModal = 'approve';
                    $('#approveConfirmModal').modal('show');
                };

                // ============================================
                // 3. CONFIRM APPROVE BUTTON - AJAX
                // ============================================
                $(document).on('click', '#confirmApproveBtn', function() {
                    console.log('confirmApproveBtn clicked, selectedSuratId:', selectedSuratId);

                    if (isProcessing) return;

                    if (!selectedSuratId) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'ID surat tidak ditemukan. Silakan refresh halaman dan coba lagi.',
                            confirmButtonColor: '#6f42c1'
                        });
                        return;
                    }

                    isProcessing = true;

                    $(this).prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');
                    $('#approveLoading').show();
                    $('#approveError').hide();

                    $.ajax({
                        url: `/petugas/approval/${selectedSuratId}/approve`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        timeout: 30000,
                        success: function(response) {
                            isProcessing = false;

                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message || 'Surat berhasil disetujui',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                $('#approveConfirmModal').modal('hide');

                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Gagal menyetujui surat',
                                    confirmButtonColor: '#6f42c1'
                                });
                                resetApproveButton();
                            }
                        },
                        error: function(xhr) {
                            isProcessing = false;

                            let errorMsg = 'Terjadi kesalahan pada server';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.status === 0) {
                                errorMsg = 'Koneksi terputus. Periksa koneksi internet Anda.';
                            } else if (xhr.status === 404) {
                                errorMsg = 'Data surat tidak ditemukan.';
                            } else if (xhr.status === 500) {
                                errorMsg = 'Terjadi kesalahan pada server. Silakan coba lagi.';
                            }

                            $('#approveErrorMessage').text(errorMsg);
                            $('#approveError').show();
                            resetApproveButton();

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMsg,
                                confirmButtonColor: '#6f42c1'
                            });
                        }
                    });
                });

                function resetApproveButton() {
                    $('#confirmApproveBtn').prop('disabled', false).html(
                        '<i class="bi bi-check-lg me-1"></i> Ya, Setujui');
                    $('#approveLoading').hide();
                }

                // ============================================
                // 4. RESET APPROVE MODAL
                // ============================================
                $('#approveConfirmModal').on('hidden.bs.modal', function() {
                    resetApproveButton();
                    $('#approveError').hide();
                    if (!isProcessing) {
                        selectedSuratId = null;
                    }
                    currentModal = null;
                });

                // ============================================
                // 5. REJECT FORM - AJAX
                // ============================================
                $('#rejectForm').on('submit', function(e) {
                    e.preventDefault();

                    if (isProcessing) return;

                    console.log('rejectForm submitted, selectedSuratId:', selectedSuratId);

                    if (!selectedSuratId) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'ID surat tidak ditemukan. Silakan refresh halaman dan coba lagi.',
                            confirmButtonColor: '#6f42c1'
                        });
                        return;
                    }

                    const alasan = $('#rejectReason').val().trim();
                    if (!alasan) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Alasan penolakan harus diisi',
                            confirmButtonColor: '#6f42c1'
                        });
                        $('#rejectReason').focus();
                        return;
                    }

                    if (alasan.length < 5) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Alasan penolakan minimal 5 karakter',
                            confirmButtonColor: '#6f42c1'
                        });
                        $('#rejectReason').focus();
                        return;
                    }

                    isProcessing = true;

                    $('#confirmRejectBtn').prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');
                    $('#rejectLoading').show();
                    $('#rejectError').hide();

                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            alasan: alasan
                        },
                        dataType: 'json',
                        timeout: 30000,
                        success: function(response) {
                            isProcessing = false;

                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message || 'Surat berhasil ditolak',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                $('#rejectModal').modal('hide');

                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Gagal menolak surat',
                                    confirmButtonColor: '#6f42c1'
                                });
                                resetRejectButton();
                            }
                        },
                        error: function(xhr) {
                            isProcessing = false;

                            let errorMsg = 'Terjadi kesalahan pada server';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.status === 0) {
                                errorMsg = 'Koneksi terputus. Periksa koneksi internet Anda.';
                            }

                            $('#rejectErrorMessage').text(errorMsg);
                            $('#rejectError').show();
                            resetRejectButton();

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMsg,
                                confirmButtonColor: '#6f42c1'
                            });
                        }
                    });
                });

                function resetRejectButton() {
                    $('#confirmRejectBtn').prop('disabled', false).html('<i class="bi bi-x-lg me-1"></i> Tolak');
                    $('#rejectLoading').hide();
                }

                // ============================================
                // 6. RESET REJECT MODAL
                // ============================================
                $('#rejectModal').on('hidden.bs.modal', function() {
                    resetRejectButton();
                    $('#rejectError').hide();
                    if (!isProcessing) {
                        selectedSuratId = null;
                    }
                    currentModal = null;
                });

                // ============================================
                // 7. REVIEW MODAL
                // ============================================
                window.openReviewModal = function(id) {
                    if (isProcessing) return;

                    console.log('openReviewModal called with id:', id);
                    selectedSuratId = id;

                    $('#reviewModalBody').html(`
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data surat...</p>
                        <p class="text-muted small">ID: ${id}</p>
                    </div>
                `);

                    currentModal = 'review';
                    $('#reviewModal').modal('show');
                    loadSuratData(id);
                };

                // ============================================
                // 8. LOAD SURAT DATA - DIPERBAIKI
                // ============================================
                function loadSuratData(id) {
                    console.log('=== loadSuratData called with id:', id);

                    $.ajax({
                        url: `/petugas/approval/${id}/data`,
                        method: 'GET',
                        dataType: 'json',
                        timeout: 15000,
                        success: function(response) {
                            console.log('=== loadSuratData success ===');
                            console.log('Response:', response);

                            if (response.success && response.data) {
                                console.log('Data keys:', Object.keys(response.data));
                                console.log('Nama mahasiswa:', response.data.mahasiswa_nama);
                                console.log('Nama orang tua:', response.data.nama_ortu);

                                // Simpan data
                                selectedSuratData = response.data;

                                // Render modal
                                renderReviewModal(response.data);
                            } else {
                                console.error('Response tidak valid:', response);
                                showError('Gagal memuat data surat: Data tidak lengkap');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('=== loadSuratData error ===');
                            console.error('Status:', status);
                            console.error('Error:', error);
                            console.error('XHR:', xhr);

                            let msg = 'Terjadi kesalahan saat memuat data';
                            if (xhr.status === 404) {
                                msg = 'Data surat tidak ditemukan (ID: ' + id + ')';
                            } else if (xhr.status === 0) {
                                msg = 'Koneksi terputus. Periksa koneksi internet Anda.';
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            showError(msg);
                        }
                    });
                }

                // ============================================
                // 9. RENDER REVIEW MODAL - DIPERBAIKI
                // ============================================
                function renderReviewModal(data) {
                    console.log('=== renderReviewModal called ===');
                    console.log('Data:', data);

                    if (!data || typeof data !== 'object') {
                        console.error('Data invalid:', data);
                        showError('Data surat tidak valid');
                        return;
                    }

                    // Cek data orang tua
                    const hasNamaOrtu = data.nama_ortu &&
                        data.nama_ortu !== '-' &&
                        data.nama_ortu !== '' &&
                        data.nama_ortu !== 'null' &&
                        data.nama_ortu !== 'undefined';

                    const hasInstansiOrtu = data.instansi_ortu &&
                        data.instansi_ortu !== '-' &&
                        data.instansi_ortu !== '' &&
                        data.instansi_ortu !== 'null' &&
                        data.instansi_ortu !== 'undefined';

                    const hasOrangTua = hasNamaOrtu && hasInstansiOrtu;

                    console.log('Hasil pengecekan data orang tua:', {
                        hasNamaOrtu: hasNamaOrtu,
                        hasInstansiOrtu: hasInstansiOrtu,
                        hasOrangTua: hasOrangTua,
                        nama_ortu: data.nama_ortu,
                        instansi_ortu: data.instansi_ortu
                    });

                    // ============================================
                    // BUILD HTML
                    // ============================================
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
                            <label class="text-muted small">Tanggal Pengajuan</label>
                            <p class="fw-semibold">${data.tanggal_pengajuan || '-'}</p>
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
                `;

                    // Data Orang Tua
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
                    } else {
                        html += `
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Perhatian:</strong> Data orang tua/wali mahasiswa belum lengkap.
                                    <br><small class="text-muted">Nama: ${data.nama_ortu || 'Kosong'} | Instansi: ${data.instansi_ortu || 'Kosong'}</small>
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

                    // File Pendukung
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

                    // Masukkan ke modal
                    $('#reviewModalBody').html(html);
                    $('#reviewModalBody').scrollTop(0);

                    console.log('=== renderReviewModal selesai ===');
                }

                function showError(message) {
                    $('#reviewModalBody').html(`
                    <div class="alert alert-danger m-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${message}
                        <br><br>
                        <button class="btn btn-sm btn-primary" onclick="openReviewModal(${selectedSuratId})">
                            <i class="bi bi-arrow-repeat me-1"></i> Coba Lagi
                        </button>
                    </div>
                `);
                }

                // ============================================
                // 10. REVIEW APPROVE
                // ============================================
                $('#reviewApproveBtn').on('click', function() {

                    if (!selectedSuratId) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'ID surat tidak ditemukan'
                        });
                        return;
                    }

                    const suratId = selectedSuratId;

                    $('#reviewModal').modal('hide');

                    setTimeout(function() {
                        confirmApprove(suratId);
                    }, 300);
                });

                // ============================================
                // 11. REVIEW REJECT
                // ============================================
                $('#reviewRejectBtn').on('click', function() {
                    console.log('reviewRejectBtn clicked, selectedSuratId:', selectedSuratId);

                    if (selectedSuratId) {
                        $('#reviewModal').modal('hide');
                        setTimeout(function() {
                            openRejectModal(selectedSuratId);
                        }, 300);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'ID surat tidak ditemukan. Silakan buka ulang review.',
                            confirmButtonColor: '#6f42c1'
                        });
                    }
                });

                // ============================================
                // 12. RESET REVIEW MODAL
                // ============================================
                $('#reviewModal').on('hidden.bs.modal', function() {

                    if (currentModal !== 'approve' && currentModal !== 'reject') {
                        selectedSuratId = null;
                        selectedSuratData = null;
                    }

                });

                // ============================================
                // 13. PREVENT MODAL CLOSE ON BACKGROUND CLICK
                // ============================================
                $('.modal').on('click', function(e) {
                    if ($(e.target).hasClass('modal')) {
                        e.preventDefault();
                    }
                });

                // ============================================
                // 14. FIX: Cleanup backdrop
                // ============================================
                $(document).on('hidden.bs.modal', function() {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                    $('body').css('overflow', '');
                    $('body').css('padding-right', '');
                });
            });
        </script>
    @endpush
@endsection
