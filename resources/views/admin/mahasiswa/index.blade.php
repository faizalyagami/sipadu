@extends('layouts.app')

@section('title', 'Data Mahasiswa')

@section('content')
    <div class="container-fluid">
        <!-- Header Card -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4"
                        style="background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%); border-radius: 16px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="text-white mb-1 fw-bold">Data Mahasiswa</h3>
                                <p class="text-white-50 mb-0">Kelola data mahasiswa aktif, cuti, dan lulus</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1">Daftar Mahasiswa</h4>
                <p class="text-muted mb-0">Total: {{ $mahasiswas->total() }} mahasiswa</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
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
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="{{ route('admin.mahasiswa.export-template') }}">
                                <i class="bi bi-file-earmark-spreadsheet"></i> Download Template
                            </a></li>
                    </ul>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMahasiswaModal">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Mahasiswa
                </button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                    data-bs-target="#importMahasiswaModal">
                    <i class="bi bi-file-excel me-2"></i>Import Excel
                </button>
            </div>
        </div>

        <!-- Alert Section -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Tabel Mahasiswa -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="mahasiswaTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>NPM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Dosen Wali</th>
                                <th>Fakultas</th>
                                <th>Program Studi</th>
                                <th>Jenjang</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">IPK</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mahasiswas as $mhs)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td><strong>{{ $mhs->npm }}</strong></td>
                                    <td>{{ $mhs->nama_lengkap }}</td>
                                    <td>
                                        @if ($mhs->dosen_wali)
                                            <div><strong>{{ $mhs->dosen_wali }}</strong></div>
                                            <small class="text-muted">NIK: {{ $mhs->dosen_wali_nik ?? '-' }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $mhs->prodi->fakultas->nama_fakultas ?? '-' }}</td>
                                    <td>{{ $mhs->prodi->nama_prodi ?? '-' }}</td>
                                    <td>{{ $mhs->prodi->jenjang ?? '-' }}</td>
                                    <td class="text-center">
                                        @if ($mhs->status_mahasiswa == 'Aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @elseif($mhs->status_mahasiswa == 'Cuti')
                                            <span class="badge bg-warning text-dark">Cuti</span>
                                        @else
                                            <span class="badge bg-secondary">Lulus</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($mhs->ipk)
                                            <span class="fw-bold">{{ number_format($mhs->ipk, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('admin.mahasiswa.show', $mhs->id) }}"
                                                class="btn btn-sm btn-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.mahasiswa.edit', $mhs->id) }}"
                                                class="btn btn-sm btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger delete-mahasiswa"
                                                data-id="{{ $mhs->id }}" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            Belum ada data mahasiswa
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
            <div class="text-muted small">
                Menampilkan
                {{ $mahasiswas->firstItem() ?? 0 }}
                sampai
                {{ $mahasiswas->lastItem() ?? 0 }}
                dari
                {{ $mahasiswas->total() }} data
            </div>
            <div>
                {{ $mahasiswas->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH MAHASISWA --}}
    <div class="modal fade" id="addMahasiswaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.mahasiswa.store') }}" method="POST" id="formTambahMahasiswa">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-person-plus me-2"></i>Tambah Mahasiswa
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">NPM <span class="text-danger">*</span></label>
                                <input type="text" name="npm" class="form-control" required maxlength="20">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Fakultas <span class="text-danger">*</span></label>
                                <select name="fakultas_id" id="fakultas_id" class="form-select" required>
                                    <option value="">Pilih Fakultas</option>
                                    @foreach ($fakultas as $fak)
                                        <option value="{{ $fak->id }}">{{ $fak->nama_fakultas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Program Studi <span class="text-danger">*</span></label>
                                <select name="prodi_id" id="prodi_id" class="form-select" required disabled>
                                    <option value="">Pilih Fakultas Terlebih Dahulu</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Status Mahasiswa <span
                                        class="text-danger">*</span></label>
                                <select name="status_mahasiswa" class="form-select" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Cuti">Cuti</option>
                                    <option value="Lulus">Lulus</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" name="tempat_lahir" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_lahir" class="form-control" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Alamat <span class="text-danger">*</span></label>
                                <textarea name="alamat" class="form-control" rows="2" required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">No HP <span class="text-danger">*</span></label>
                                <input type="text" name="no_hp" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tanggal Masuk <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_masuk" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">IPK</label>
                                <input type="number" name="ipk" step="0.01" min="0" max="4"
                                    class="form-control">
                                <small class="text-muted">Range: 0.00 - 4.00</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL IMPORT EXCEL --}}
    <div class="modal fade" id="importMahasiswaModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-file-excel me-2"></i>Import Data Mahasiswa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="uploadForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold">File Excel <span class="text-danger">*</span></label>
                            <input type="file" id="importFile" class="form-control" accept=".xlsx,.xls,.csv"
                                required>
                            <small class="text-muted">Format: .xlsx, .xls, atau .csv (Max 10MB)</small>
                        </div>
                        <div class="alert alert-info">
                            <i class="bi bi-download me-2"></i>
                            <a href="{{ route('admin.mahasiswa.export-template') }}" class="alert-link fw-bold">
                                Download template Excel
                            </a>
                            <hr class="my-2">
                            <small>Pastikan format file sesuai dengan template yang disediakan.</small>
                        </div>
                    </div>

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

                        <div class="row g-2 mt-3">
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
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Detail Error
                                </div>
                                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                    <pre id="errorList" class="mb-0 small text-danger"></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
        .progress {
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            height: 30px;
        }

        .progress-bar {
            transition: width 0.5s ease;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(90deg, #6f42c1 0%, #8b5cf6 100%);
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

        input[type="file"]::file-selector-button {
            background: linear-gradient(135deg, #6f42c1, #8b5cf6);
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

        .table> :not(caption)>*>* {
            vertical-align: middle;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data prodi dari server
            const allProdis = @json($allProdis ?? []);
            let progressInterval = null;
            let currentBatchId = null;

            // ========== FILTER PRODI BERDASARKAN FAKULTAS ==========
            const fakultasSelect = document.getElementById('fakultas_id');
            const prodiSelect = document.getElementById('prodi_id');

            if (fakultasSelect && prodiSelect) {
                fakultasSelect.addEventListener('change', function() {
                    const fakultasId = this.value;
                    prodiSelect.innerHTML = '<option value="">Pilih Program Studi</option>';

                    if (fakultasId) {
                        const filteredProdis = allProdis.filter(prodi => prodi.fakultas_id == fakultasId);
                        filteredProdis.forEach(prodi => {
                            const option = document.createElement('option');
                            option.value = prodi.id;
                            option.textContent = `${prodi.nama_prodi} (${prodi.jenjang})`;
                            prodiSelect.appendChild(option);
                        });
                        prodiSelect.disabled = false;
                    } else {
                        prodiSelect.disabled = true;
                    }
                });
            }

            // ========== DELETE MAHASISWA ==========
            document.querySelectorAll('.delete-mahasiswa').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const url = `/admin/mahasiswa/${id}`;

                    Swal.fire({
                        title: 'Apakah anda yakin?',
                        text: 'Data mahasiswa akan dihapus permanen!',
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
                });
            });

            // ========== IMPORT FUNCTION ==========
            const startImportBtn = document.getElementById('startImportBtn');
            const importFileInput = document.getElementById('importFile');

            if (startImportBtn) {
                startImportBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    if (this.disabled) return;

                    const file = importFileInput.files[0];

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
            }

            function startImport(file) {
                document.getElementById('uploadForm').style.display = 'none';
                document.getElementById('progressArea').style.display = 'block';

                const btn = document.getElementById('startImportBtn');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

                updateProgressDisplay({
                    total_rows: 0,
                    processed_rows: 0,
                    success_rows: 0,
                    failed_rows: 0,
                    percentage: 0
                });

                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                fetch("{{ route('admin.mahasiswa.import') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            currentBatchId = data.batch_id;
                            document.getElementById('fileName').textContent = 'File: ' + file.name;
                            startPollingProgress(currentBatchId);
                        } else {
                            showError(data.message || 'Gagal memulai import');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError('Terjadi kesalahan saat memulai import');
                    });
            }

            function startPollingProgress(batchId) {
                if (progressInterval) {
                    clearInterval(progressInterval);
                }

                progressInterval = setInterval(() => {
                    fetch(`/admin/mahasiswa/import-progress/${batchId}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                updateProgressDisplay(data.data);

                                if (data.data.status === 'completed') {
                                    clearInterval(progressInterval);
                                    onImportComplete(data.data);
                                } else if (data.data.status === 'failed') {
                                    clearInterval(progressInterval);
                                    onImportFailed(data.data);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Polling error:', error);
                        });
                }, 2000);
            }

            function updateProgressDisplay(data) {
                const percentage = data.percentage || 0;
                const totalRows = data.total_rows || 0;
                const processedRows = data.processed_rows || 0;
                const successRows = data.success_rows || 0;
                const failedRows = data.failed_rows || 0;

                const progressBar = document.getElementById('progressBar');
                const progressLabel = document.getElementById('progressLabel');
                const percentageText = document.getElementById('percentageText');

                progressBar.style.width = percentage + '%';
                progressLabel.textContent = percentage + '%';
                percentageText.textContent = percentage + '%';

                document.getElementById('totalRows').textContent = totalRows.toLocaleString();
                document.getElementById('successRows').textContent = successRows.toLocaleString();
                document.getElementById('failedRows').textContent = failedRows.toLocaleString();

                let statusMsg = '';
                if (totalRows === 0) {
                    statusMsg = 'Menghitung total data...';
                } else if (processedRows < totalRows) {
                    statusMsg =
                        `Memproses data ${processedRows.toLocaleString()} dari ${totalRows.toLocaleString()}`;
                } else if (processedRows === totalRows && processedRows > 0) {
                    statusMsg = 'Menyelesaikan import...';
                }

                if (statusMsg) {
                    document.getElementById('statusMessage').textContent = statusMsg;
                }

                // Update progress bar color
                progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated';
                if (failedRows > 0 && successRows === 0) {
                    progressBar.classList.add('bg-danger');
                } else if (failedRows > 0) {
                    progressBar.classList.add('bg-warning');
                } else if (successRows > 0) {
                    progressBar.classList.add('bg-success');
                }
            }

            function onImportComplete(data) {
                document.getElementById('progressBar').classList.remove('progress-bar-animated');
                document.getElementById('progressLabel').textContent = 'Selesai!';

                const statusInfo = document.getElementById('statusInfo');
                statusInfo.innerHTML = `
                <div class="alert alert-success mb-0">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Import Selesai!</strong><br>
                    Total data: ${data.total_rows.toLocaleString()}<br>
                    Berhasil: ${data.success_rows.toLocaleString()}<br>
                    Gagal: ${data.failed_rows.toLocaleString()}
                </div>
            `;

                document.getElementById('startImportBtn').disabled = false;
                document.getElementById('startImportBtn').innerHTML =
                    '<i class="bi bi-play-circle me-2"></i>Mulai Import';

                if (data.errors && data.errors.length > 0) {
                    showErrorDetails(data.errors);
                }

                setTimeout(() => {
                    Swal.fire({
                        title: 'Import Selesai!',
                        html: `Berhasil mengimport ${data.success_rows} dari ${data.total_rows} data mahasiswa.`,
                        icon: 'success',
                        confirmButtonText: 'Refresh Halaman'
                    }).then(() => {
                        window.location.reload();
                    });
                }, 1500);
            }

            function onImportFailed(data) {
                document.getElementById('progressBar').classList.remove('progress-bar-animated');
                document.getElementById('progressBar').classList.add('bg-danger');
                document.getElementById('progressLabel').textContent = 'Gagal!';

                let errorMsg = 'Terjadi kesalahan saat import';
                if (data.errors && data.errors.length > 0) {
                    errorMsg = data.errors[0].message || errorMsg;
                }

                const statusInfo = document.getElementById('statusInfo');
                statusInfo.innerHTML = `
                <div class="alert alert-danger mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Import Gagal!</strong><br>
                    ${errorMsg}
                </div>
            `;

                document.getElementById('startImportBtn').disabled = false;
                document.getElementById('startImportBtn').innerHTML =
                    '<i class="bi bi-play-circle me-2"></i>Mulai Import';
            }

            function showError(message) {
                document.getElementById('progressArea').style.display = 'none';
                document.getElementById('uploadForm').style.display = 'block';

                const btn = document.getElementById('startImportBtn');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-play-circle me-2"></i>Mulai Import';

                Swal.fire('Error', message, 'error');
            }

            function showErrorDetails(errors) {
                let errorHtml = '';
                errors.forEach((error, index) => {
                    if (index < 50) {
                        errorHtml += `Baris ${index + 2}: ${error.message}\n`;
                    }
                });

                if (errors.length > 50) {
                    errorHtml += `\n... dan ${errors.length - 50} error lainnya`;
                }

                document.getElementById('errorList').textContent = errorHtml;
                document.getElementById('errorDetails').style.display = 'block';
            }

            // ========== RESET MODAL ==========
            document.getElementById('importMahasiswaModal').addEventListener('hidden.bs.modal', function() {
                document.getElementById('uploadForm').style.display = 'block';
                document.getElementById('progressArea').style.display = 'none';
                document.getElementById('errorDetails').style.display = 'none';
                document.getElementById('importFile').value = '';

                const btn = document.getElementById('startImportBtn');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-play-circle me-2"></i>Mulai Import';

                if (progressInterval) {
                    clearInterval(progressInterval);
                }
            });
        });
    </script>
@endpush
