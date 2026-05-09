@extends('layouts.app')

@section('title', 'Data Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Data Mahasiswa</h4>
            <p class="text-muted mb-0">Kelola data mahasiswa aktif, cuti, dan lulus</p>
        </div>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-2"></i>Export Data
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.mahasiswa.export', ['status' => 'semua']) }}">
                        <i class="bi bi-database"></i> Semua Mahasiswa
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.mahasiswa.export', ['status' => 'Aktif']) }}">
                        <i class="bi bi-check-circle text-success"></i> Mahasiswa Aktif
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.mahasiswa.export', ['status' => 'Cuti']) }}">
                        <i class="bi bi-clock-history text-warning"></i> Mahasiswa Cuti
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.mahasiswa.export', ['status' => 'Lulus']) }}">
                        <i class="bi bi-award text-info"></i> Mahasiswa Lulus
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('admin.mahasiswa.export-template') }}">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Download Template
                    </a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMahasiswaModal">
                <i class="bi bi-plus-circle me-2"></i>Tambah Mahasiswa
            </button>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importMahasiswaModal">
                <i class="bi bi-file-excel me-2"></i>Import Excel
            </button>
        </div>
    </div>

    <!-- Alert Section -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tabel Mahasiswa -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="mahasiswaTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NPM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Dosen Wali</th>
                            <th>Fakultas</th>
                            <th>Program Studi</th>
                            <th>Jenjang</th>
                            <th>Status</th>
                            <th>IPK</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mahasiswas as $mhs)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $mhs->npm }}</td>
                            <td>{{ $mhs->nama_lengkap }}</td>
                            <td>
                                @if($mhs->dosen_wali)
                                    <strong>{{ $mhs->dosen_wali }}</strong><br>
                                    <small class="text-muted">NIK: {{ $mhs->dosen_wali_nik ?? '-' }}</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $mhs->prodi->fakultas->nama_fakultas ?? '-' }}</td>
                            <td>{{ $mhs->prodi->nama_prodi ?? '-' }}</td>
                            <td>{{ $mhs->prodi->jenjang ?? '-' }}</td>
                            <td>
                                @if($mhs->status_mahasiswa == 'Aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @elseif($mhs->status_mahasiswa == 'Cuti')
                                    <span class="badge bg-warning">Cuti</span>
                                @else
                                    <span class="badge bg-secondary">Lulus</span>
                                @endif
                            </td>
                            <td>{{ $mhs->ipk ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.mahasiswa.edit', $mhs->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-danger delete-mahasiswa" data-id="{{ $mhs->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr class="text-center">
                            <td colspan="9" class="text-center py-4">Belum ada data mahasiswa</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <div class="text-muted small">
            Menampilkan 
            {{ $mahasiswas->firstItem() ?? 0 }} 
            sampai 
            {{ $mahasiswas->lastItem() ?? 0 }} 
            dari 
            {{ $mahasiswas->total() }} data mahasiswa
        </div>

        <div>
            {{ $mahasiswas->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- MODAL TAMBAH MAHASISWA --}}
<div class="modal fade" id="addMahasiswaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.mahasiswa.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NPM</label>
                            <input type="text" name="npm" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fakultas</label>
                            <select name="fakultas_id" id="fakultas_id" class="form-select" required>
                                <option value="">Pilih Fakultas</option>
                                @foreach($fakultas as $fak)
                                <option value="{{ $fak->id }}">{{ $fak->nama_fakultas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Program Studi</label>
                            <select name="prodi_id" id="prodi_id" class="form-select" required>
                                <option value="">Pilih Fakultas Terlebih Dahulu</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status Mahasiswa</label>
                            <select name="status_mahasiswa" class="form-select" required>
                                <option value="Aktif">Aktif</option>
                                <option value="Cuti">Cuti</option>
                                <option value="Lulus">Lulus</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No HP</label>
                            <input type="text" name="no_hp" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">IPK</label>
                            <input type="number" name="ipk" step="0.01" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL IMPORT EXCEL dengan PROGRESS BAR --}}
<div class="modal fade" id="importMahasiswaModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-file-excel me-2"></i>Import Data Mahasiswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeImportModal"></button>
            </div>
            <div class="modal-body">
                {{-- Form Upload File --}}
                <div id="uploadForm">
                    <div class="mb-3">
                        <label class="form-label">File Excel</label>
                        <input type="file" id="importFile" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Format: .xlsx, .xls, atau .csv (Max 10MB)</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-download"></i> 
                        <a href="{{ route('admin.mahasiswa.export-template') }}" class="alert-link">Download template Excel</a>
                        <hr class="my-2">
                        <small>Pastikan format file sesuai dengan template yang disediakan.</small>
                    </div>
                </div>

                {{-- Progress Bar Area --}}
                <div id="progressArea" style="display: none;">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <strong id="fileName"></strong>
                            <strong id="percentageText">0%</strong>
                        </div>
                        <div class="progress" style="height: 30px;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%">
                                <span id="progressLabel" class="fw-bold">0%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center p-2">
                                    <small class="text-muted">Total Data</small>
                                    <h4 id="totalRows" class="mb-0">0</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success bg-opacity-10">
                                <div class="card-body text-center p-2">
                                    <small class="text-muted">Berhasil</small>
                                    <h4 id="successRows" class="mb-0 text-success">0</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger bg-opacity-10">
                                <div class="card-body text-center p-2">
                                    <small class="text-muted">Gagal</small>
                                    <h4 id="failedRows" class="mb-0 text-danger">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="statusInfo" class="mt-3">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            <span id="statusMessage">Memproses data...</span>
                        </div>
                    </div>

                    <div id="errorDetails" style="display: none;" class="mt-3">
                        <div class="card">
                            <div class="card-header bg-danger text-white">
                                <i class="bi bi-exclamation-triangle me-2"></i>Detail Error
                            </div>
                            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                <pre id="errorList" class="mb-0 small"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeBtnFooter">Tutup</button>
                <button type="button" id="startImportBtn" class="btn btn-success">
                    <i class="bi bi-play-circle me-2"></i>Mulai Import
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Progress Bar Custom Styles */
    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-bar {
        transition: width 0.5s ease;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(90deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
    }
    
    .progress-bar.bg-success {
        background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
    }
    
    .progress-bar.bg-warning {
        background: linear-gradient(90deg, #ffc107 0%, #fd7e14 100%);
    }
    
    .progress-bar.bg-danger {
        background: linear-gradient(90deg, #dc3545 0%, #c82333 100%);
    }
    
    .card.bg-light, .card.bg-success, .card.bg-danger {
        transition: transform 0.2s ease;
    }
    
    .card.bg-light:hover, .card.bg-success.bg-opacity-10:hover, .card.bg-danger.bg-opacity-10:hover {
        transform: translateY(-2px);
    }
    
    #errorDetails .card {
        border-left: 4px solid #dc3545;
    }
    
    #errorList {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        font-size: 12px;
        line-height: 1.5;
        color: #721c24;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    
    .modal.fade .modal-dialog {
        transform: scale(0.95);
        transition: transform 0.2s ease;
    }
    
    .modal.show .modal-dialog {
        transform: scale(1);
    }
    
    input[type="file"] {
        cursor: pointer;
    }
    
    input[type="file"]::file-selector-button {
        background: linear-gradient(135deg, var(--primary-purple), var(--secondary-purple));
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    input[type="file"]::file-selector-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(111, 66, 193, 0.3);
    }
    
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.2em;
    }
</style>
@endpush

@push('scripts')
<script>
// Siapkan data prodi dari server
const allProdis = @json($allProdis ?? []);
let progressInterval = null;
let currentBatchId = null;

$(document).ready(function() {
    console.log("Document ready - Initializing...");
    
    // ========== NONAKTIFKAN DATATABLES DULU ==========
    // Hanya styling biasa tanpa DataTables
    $('#mahasiswaTable').addClass('table-striped');
    console.log("Table styled without DataTables");
    
    // Filter prodi berdasarkan fakultas
    $('#fakultas_id').change(function() {
        let fakultasId = $(this).val();
        let prodiSelect = $('#prodi_id');
        prodiSelect.empty().append('<option value="">Pilih Program Studi</option>');
        
        if (fakultasId) {
            let filteredProdis = allProdis.filter(prodi => prodi.fakultas_id == fakultasId);
            filteredProdis.forEach(prodi => {
                prodiSelect.append(`<option value="${prodi.id}">${prodi.nama_prodi} (${prodi.jenjang})</option>`);
            });
        }
    });
    
    // Delete mahasiswa
    $(document).on('click', '.delete-mahasiswa', function() {
        let id = $(this).data('id');
        confirmDelete(`/admin/mahasiswa/${id}`, 'Yakin hapus mahasiswa ini?');
    });
    
    // Reset modal ketika ditutup
    $('#importMahasiswaModal').on('hidden.bs.modal', function() {
        resetImportModal();
        if (progressInterval) {
            clearInterval(progressInterval);
        }
    });
    
    // ========== START IMPORT BUTTON ==========
    // Pastikan event handler terdaftar dengan benar
    $('#startImportBtn').off('click').on('click', function(e) {
        e.preventDefault();
        console.log("Start Import button clicked!");
        
        const fileInput = $('#importFile')[0];
        const file = fileInput.files[0];
        
        console.log("File selected:", file ? file.name : "No file");
        
        if (!file) {
            Swal.fire('Error', 'Pilih file Excel terlebih dahulu!', 'error');
            return;
        }
        
        const allowedExtensions = ['xlsx', 'xls', 'csv'];
        const fileExt = file.name.split('.').pop().toLowerCase();
        
        if (!allowedExtensions.includes(fileExt)) {
            Swal.fire('Error', 'Format file harus .xlsx, .xls, atau .csv', 'error');
            return;
        }
        
        if (file.size > 10 * 1024 * 1024) {
            Swal.fire('Error', 'Ukuran file maksimal 10MB', 'error');
            return;
        }
        
        startImport(file);
    });
    
    // Juga coba dengan event delegation sebagai fallback
    $(document).on('click', '#startImportBtn', function(e) {
        e.preventDefault();
        console.log("Start Import button clicked via delegation!");
        
        // Cegah double execution
        if ($(this).hasClass('processing')) return;
        $(this).addClass('processing');
        
        const fileInput = $('#importFile')[0];
        const file = fileInput.files[0];
        
        if (!file) {
            Swal.fire('Error', 'Pilih file Excel terlebih dahulu!', 'error');
            $(this).removeClass('processing');
            return;
        }
        
        const allowedExtensions = ['xlsx', 'xls', 'csv'];
        const fileExt = file.name.split('.').pop().toLowerCase();
        
        if (!allowedExtensions.includes(fileExt)) {
            Swal.fire('Error', 'Format file harus .xlsx, .xls, atau .csv', 'error');
            $(this).removeClass('processing');
            return;
        }
        
        if (file.size > 10 * 1024 * 1024) {
            Swal.fire('Error', 'Ukuran file maksimal 10MB', 'error');
            $(this).removeClass('processing');
            return;
        }
        
        startImport(file);
        
        setTimeout(() => {
            $(this).removeClass('processing');
        }, 1000);
    });
});

// ========== FUNCTIONS ==========
function startImport(file) {
    console.log("startImport called with file:", file.name);
    
    $('#uploadForm').hide();
    $('#progressArea').show();
    $('#startImportBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');
    
    updateProgressDisplay({
        total_rows: 0,
        processed_rows: 0,
        success_rows: 0,
        failed_rows: 0,
        percentage: 0
    });
    
    let formData = new FormData();
    formData.append('file', file);
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    
    console.log("Sending AJAX to:", "{{ route('admin.mahasiswa.import') }}");
    
    $.ajax({
        url: "{{ route('admin.mahasiswa.import') }}",
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        timeout: 30000,
        success: function(response) {
            console.log("AJAX Success:", response);
            if (response.success) {
                currentBatchId = response.batch_id;
                $('#fileName').text('File: ' + file.name);
                startPollingProgress(currentBatchId);
            } else {
                showError(response.message);
                $('#startImportBtn').prop('disabled', false).html('<i class="bi bi-play-circle me-2"></i>Mulai Import');
            }
        },
        error: function(xhr) {
            console.error("AJAX Error:", xhr);
            let errorMsg = 'Gagal memulai import';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.status === 419) {
                errorMsg = 'Session expired. Silahkan refresh halaman.';
            } else if (xhr.status === 0) {
                errorMsg = 'Network error. Periksa koneksi internet.';
            }
            showError(errorMsg);
            $('#startImportBtn').prop('disabled', false).html('<i class="bi bi-play-circle me-2"></i>Mulai Import');
        }
    });
}

function startPollingProgress(batchId) {
    console.log("Start polling for batch:", batchId);
    
    progressInterval = setInterval(function() {
        $.ajax({
            url: `/admin/mahasiswa/import-progress/${batchId}`,
            type: 'GET',
            success: function(response) {
                console.log("Polling response:", response);
                if (response.success) {
                    updateProgressDisplay(response.data);
                    
                    if (response.data.status === 'completed') {
                        clearInterval(progressInterval);
                        onImportComplete(response.data);
                    } else if (response.data.status === 'failed') {
                        clearInterval(progressInterval);
                        onImportFailed(response.data);
                    }
                }
            },
            error: function(xhr) {
                console.error("Polling error:", xhr);
            }
        });
    }, 2000);
}

function updateProgressDisplay(data) {
    const percentage = data.percentage || 0;
    const totalRows = data.total_rows || 0;
    const processedRows = data.processed_rows || 0;
    const successRows = data.success_rows || 0;
    const failedRows = data.failed_rows || 0;
    
    $('#progressBar').css('width', percentage + '%');
    $('#progressLabel').text(percentage + '%');
    $('#percentageText').text(percentage + '%');
    
    $('#totalRows').text(totalRows.toLocaleString());
    $('#successRows').text(successRows.toLocaleString());
    $('#failedRows').text(failedRows.toLocaleString());
    
    let statusMsg = '';
    if (totalRows === 0) {
        statusMsg = 'Menghitung total data...';
    } else if (processedRows < totalRows) {
        statusMsg = `Memproses data ${processedRows.toLocaleString()} dari ${totalRows.toLocaleString()}`;
    } else if (processedRows === totalRows && processedRows > 0) {
        statusMsg = 'Menyelesaikan import...';
    }
    
    if (statusMsg) {
        $('#statusMessage').text(statusMsg);
    }
    
    const progressBar = $('#progressBar');
    if (failedRows > 0 && successRows === 0) {
        progressBar.removeClass('bg-success bg-warning').addClass('bg-danger');
    } else if (failedRows > 0) {
        progressBar.removeClass('bg-success bg-danger').addClass('bg-warning');
    } else {
        progressBar.removeClass('bg-warning bg-danger').addClass('bg-success');
    }
}

function onImportComplete(data) {
    console.log("Import completed:", data);
    
    $('#progressBar').removeClass('progress-bar-animated');
    $('#progressLabel').text('Selesai!');
    
    let message = `
        <div class="alert alert-success mb-0">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Import Selesai!</strong><br>
            Total data: ${data.total_rows.toLocaleString()}<br>
            Berhasil: ${data.success_rows.toLocaleString()}<br>
            Gagal: ${data.failed_rows.toLocaleString()}
        </div>
    `;
    
    $('#statusInfo').html(message);
    $('#startImportBtn').prop('disabled', false).html('<i class="bi bi-play-circle me-2"></i>Mulai Import');
    
    if (data.errors && data.errors.length > 0) {
        showErrorDetails(data.errors);
    }
    
    setTimeout(function() {
        Swal.fire({
            title: 'Import Selesai!',
            html: `Berhasil mengimport ${data.success_rows} dari ${data.total_rows} data mahasiswa.`,
            icon: 'success',
            confirmButtonText: 'Refresh Halaman'
        }).then(() => {
            window.location.reload();
        });
    }, 2000);
}

function onImportFailed(data) {
    console.error("Import failed:", data);
    
    $('#progressBar').removeClass('progress-bar-animated').addClass('bg-danger');
    $('#progressLabel').text('Gagal!');
    
    let errorMsg = 'Terjadi kesalahan saat import';
    if (data.errors && data.errors.length > 0) {
        errorMsg = data.errors[0].message || errorMsg;
    }
    
    let message = `
        <div class="alert alert-danger mb-0">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Import Gagal!</strong><br>
            ${errorMsg}
        </div>
    `;
    
    $('#statusInfo').html(message);
    $('#startImportBtn').prop('disabled', false).html('<i class="bi bi-play-circle me-2"></i>Mulai Import');
}

function showError(message) {
    console.error("Show error:", message);
    
    $('#progressArea').hide();
    $('#uploadForm').show();
    $('#startImportBtn').prop('disabled', false).html('<i class="bi bi-play-circle me-2"></i>Mulai Import');
    Swal.fire('Error', message, 'error');
}

function showErrorDetails(errors) {
    let errorHtml = '';
    errors.forEach(function(error, index) {
        if (index < 50) {
            errorHtml += `Baris ${index + 2}: ${error.message}\n`;
        }
    });
    
    if (errors.length > 50) {
        errorHtml += `\n... dan ${errors.length - 50} error lainnya`;
    }
    
    $('#errorList').text(errorHtml);
    $('#errorDetails').show();
}

function resetImportModal() {
    $('#uploadForm').show();
    $('#progressArea').hide();
    $('#errorDetails').hide();
    $('#importFile').val('');
    $('#startImportBtn').prop('disabled', false).html('<i class="bi bi-play-circle me-2"></i>Mulai Import');
    $('#closeBtnFooter').text('Tutup');
    
    if (progressInterval) {
        clearInterval(progressInterval);
    }
}

// Function confirm delete
window.confirmDelete = function(url, message) {
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
};
</script>
@endpush