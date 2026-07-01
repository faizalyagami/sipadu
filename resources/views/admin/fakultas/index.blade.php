{{-- resources/views/admin/fakultas/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Fakultas')

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
                                <h3 class="text-white mb-1 fw-bold">
                                    <i class="bi bi-building me-2"></i>Data Fakultas
                                </h3>
                                <p class="text-white-50 mb-0">Kelola data fakultas dan program studi</p>
                            </div>
                            <div>
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-building me-1"></i>
                                    {{ $fakultas->count() }} Fakultas
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
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

        <!-- Main Content -->
        <div class="row g-4">
            <!-- Left Column: Daftar Fakultas -->
            <div class="col-lg-5">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-list-ul me-2"></i>Daftar Fakultas
                        </h5>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addFakultasModal">
                            <i class="bi bi-plus-circle me-1"></i>Tambah
                        </button>
                    </div>
                    <div class="card-body p-0">
                        @if ($fakultas->isEmpty())
                            <div class="text-center py-5">
                                <i class="bi bi-building fs-1 text-muted d-block mb-3"></i>
                                <p class="text-muted mb-0">Belum ada data fakultas</p>
                                <p class="text-muted small">Klik tombol "Tambah" untuk menambahkan</p>
                            </div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach ($fakultas as $fak)
                                    <div
                                        class="list-group-item d-flex justify-content-between align-items-center 
                                            py-3 hover-shadow">
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-building fs-5 text-primary"></i>
                                                <strong class="fs-6">{{ $fak->nama_fakultas }}</strong>
                                            </div>
                                            <small class="text-muted ms-4">
                                                <i class="bi bi-mortarboard me-1"></i>
                                                {{ $fak->prodis->count() }} Program Studi
                                            </small>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm btn-outline-warning edit-fakultas"
                                                data-id="{{ $fak->id }}" data-nama="{{ $fak->nama_fakultas }}"
                                                title="Edit Fakultas">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('admin.fakultas.destroy', $fak->id) }}" method="POST"
                                                class="d-inline delete-form"
                                                onsubmit="return confirmDeleteFakultas(this, '{{ $fak->nama_fakultas }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Hapus Fakultas">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Program Studi -->
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-mortarboard me-2"></i>Program Studi
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($fakultas->isEmpty())
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                <p class="text-muted">Belum ada fakultas. Silahkan tambahkan fakultas terlebih dahulu.</p>
                            </div>
                        @else
                            <div class="accordion" id="accordionFakultas">
                                @foreach ($fakultas as $index => $fak)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $fak->id }}"
                                                aria-expanded="false">
                                                <div class="d-flex align-items-center gap-2 w-100">
                                                    <i class="bi bi-building text-primary"></i>
                                                    <span class="fw-semibold">{{ $fak->nama_fakultas }}</span>
                                                    <span class="badge bg-primary ms-auto">
                                                        {{ $fak->prodis->count() }}
                                                    </span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $fak->id }}" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionFakultas">
                                            <div class="accordion-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="text-muted small">
                                                        <i class="bi bi-info-circle me-1"></i>
                                                        Total: {{ $fak->prodis->count() }} Program Studi
                                                    </span>
                                                    <button class="btn btn-sm btn-success add-prodi"
                                                        data-fakultas-id="{{ $fak->id }}"
                                                        data-fakultas-nama="{{ $fak->nama_fakultas }}">
                                                        <i class="bi bi-plus-circle me-1"></i>Tambah Prodi
                                                    </button>
                                                </div>

                                                @if ($fak->prodis->isEmpty())
                                                    <div class="text-center py-3">
                                                        <p class="text-muted mb-0 small">Belum ada program studi</p>
                                                    </div>
                                                @else
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-hover mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Nama Program Studi</th>
                                                                    <th class="text-center">Jenjang</th>
                                                                    <th class="text-center" style="width: 100px;">Aksi
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($fak->prodis as $prodi)
                                                                    <tr>
                                                                        <td>
                                                                            <i class="bi bi-book me-2 text-primary"></i>
                                                                            {{ $prodi->nama_prodi }}
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <span
                                                                                class="badge 
                                                                            @if ($prodi->jenjang == 'S1') bg-primary 
                                                                            @elseif($prodi->jenjang == 'S2') bg-success 
                                                                            @elseif($prodi->jenjang == 'S3') bg-danger 
                                                                            @else bg-info @endif">
                                                                                {{ $prodi->jenjang }}
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <div
                                                                                class="d-flex gap-1 justify-content-center">
                                                                                <button
                                                                                    class="btn btn-sm btn-outline-warning edit-prodi"
                                                                                    data-id="{{ $prodi->id }}"
                                                                                    data-nama="{{ $prodi->nama_prodi }}"
                                                                                    data-jenjang="{{ $prodi->jenjang }}"
                                                                                    title="Edit Prodi">
                                                                                    <i class="bi bi-pencil"></i>
                                                                                </button>
                                                                                <form
                                                                                    action="{{ route('admin.prodi.destroy', $prodi->id) }}"
                                                                                    method="POST"
                                                                                    class="d-inline delete-prodi-form"
                                                                                    onsubmit="return confirmDeleteProdi(this, '{{ $prodi->nama_prodi }}')">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit"
                                                                                        class="btn btn-sm btn-outline-danger"
                                                                                        title="Hapus Prodi">
                                                                                        <i class="bi bi-trash"></i>
                                                                                    </button>
                                                                                </form>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL TAMBAH FAKULTAS -->
    <!-- ============================================ -->
    <div class="modal fade" id="addFakultasModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.fakultas.store') }}" method="POST" id="formAddFakultas">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-building-add me-2"></i>Tambah Fakultas
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Fakultas <span class="text-danger">*</span></label>
                            <input type="text" name="nama_fakultas" class="form-control"
                                placeholder="Masukkan nama fakultas" required>
                            <small class="text-muted">Contoh: Fakultas Teknik, Fakultas Ekonomi, dll.</small>
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

    <!-- ============================================ -->
    <!-- MODAL TAMBAH PROGRAM STUDI -->
    <!-- ============================================ -->
    <div class="modal fade" id="addProdiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addProdiForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-mortarboard-add me-2"></i>Tambah Program Studi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Fakultas</label>
                            <input type="text" id="fakultas_nama_display" class="form-control" disabled>
                            <input type="hidden" id="fakultas_id_display" name="fakultas_id">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Program Studi <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_prodi" class="form-control"
                                placeholder="Contoh: Teknik Informatika" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jenjang <span class="text-danger">*</span></label>
                            <select name="jenjang" class="form-select" required>
                                <option value="">Pilih Jenjang</option>
                                <option value="S1">S1 (Strata 1)</option>
                                <option value="S2">S2 (Strata 2)</option>
                                <option value="S3">S3 (Strata 3)</option>
                                <option value="Profesi">Profesi</option>
                                <option value="Diploma">Diploma</option>
                            </select>
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
@endsection

@push('styles')
    <style>
        .hover-shadow:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s ease;
        }

        .list-group-item {
            border-left: none;
            border-right: none;
            border-color: rgba(0, 0, 0, 0.05);
        }

        .list-group-item:first-child {
            border-top: none;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .accordion-button:not(.collapsed) {
            background-color: #f8f0ff;
            color: #6f42c1;
        }

        .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.25);
        }

        .badge {
            font-weight: 500;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

            // ============================================
            // DELETE FAKULTAS - Confirm Function
            // ============================================
            window.confirmDeleteFakultas = function(form, nama) {
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    html: `Data fakultas <strong>"${nama}"</strong> akan dihapus permanen!<br>
                   <small class="text-danger">Semua program studi di dalamnya juga akan terhapus.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
                return false;
            };

            // ============================================
            // DELETE PRODI - Confirm Function
            // ============================================
            window.confirmDeleteProdi = function(form, nama) {
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    html: `Program studi <strong>"${nama}"</strong> akan dihapus permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
                return false;
            };

            // ============================================
            // EDIT FAKULTAS
            // ============================================
            document.querySelectorAll('.edit-fakultas').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const nama = this.dataset.nama;

                    Swal.fire({
                        title: 'Edit Fakultas',
                        input: 'text',
                        inputValue: nama,
                        inputLabel: 'Nama Fakultas',
                        inputPlaceholder: 'Masukkan nama fakultas',
                        showCancelButton: true,
                        confirmButtonColor: '#6f42c1',
                        confirmButtonText: 'Update',
                        cancelButtonText: 'Batal',
                        preConfirm: (value) => {
                            if (!value || value.trim() === '') {
                                Swal.showValidationMessage(
                                    'Nama fakultas tidak boleh kosong');
                                return false;
                            }
                            return value.trim();
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            fetch(`/admin/fakultas/${id}`, {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken,
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify({
                                        nama_fakultas: result.value
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: 'Data fakultas berhasil diupdate',
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire('Error', data.message ||
                                            'Gagal update data', 'error');
                                    }
                                })
                                .catch(() => {
                                    Swal.fire('Error', 'Terjadi kesalahan pada server',
                                        'error');
                                });
                        }
                    });
                });
            });

            // ============================================
            // TAMBAH PROGRAM STUDI
            // ============================================
            document.querySelectorAll('.add-prodi').forEach(button => {
                button.addEventListener('click', function() {
                    const fakultasId = this.dataset.fakultasId;
                    const fakultasNama = this.dataset.fakultasNama;

                    document.getElementById('fakultas_nama_display').value = fakultasNama;
                    document.getElementById('fakultas_id_display').value = fakultasId;
                    document.getElementById('addProdiForm').action =
                        `/admin/fakultas/${fakultasId}/prodi`;

                    // Reset form
                    document.querySelector('#addProdiForm input[name="nama_prodi"]').value = '';
                    document.querySelector('#addProdiForm select[name="jenjang"]').value = '';

                    $('#addProdiModal').modal('show');
                });
            });

            // ============================================
            // EDIT PROGRAM STUDI
            // ============================================
            document.querySelectorAll('.edit-prodi').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const nama = this.dataset.nama;
                    const jenjang = this.dataset.jenjang;

                    Swal.fire({
                        title: 'Edit Program Studi',
                        html: `
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Program Studi</label>
                        <input id="edit_nama_prodi" class="swal2-input" 
                               placeholder="Nama Program Studi" value="${nama}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenjang</label>
                        <select id="edit_jenjang" class="swal2-select">
                            <option value="S1" ${jenjang == 'S1' ? 'selected' : ''}>S1 (Strata 1)</option>
                            <option value="S2" ${jenjang == 'S2' ? 'selected' : ''}>S2 (Strata 2)</option>
                            <option value="S3" ${jenjang == 'S3' ? 'selected' : ''}>S3 (Strata 3)</option>
                            <option value="Profesi" ${jenjang == 'Profesi' ? 'selected' : ''}>Profesi</option>
                            <option value="Diploma" ${jenjang == 'Diploma' ? 'selected' : ''}>Diploma</option>
                        </select>
                    </div>
                `,
                        showCancelButton: true,
                        confirmButtonColor: '#6f42c1',
                        confirmButtonText: 'Update',
                        cancelButtonText: 'Batal',
                        preConfirm: () => {
                            const namaValue = document.getElementById('edit_nama_prodi')
                                .value;
                            const jenjangValue = document.getElementById('edit_jenjang')
                                .value;

                            if (!namaValue || namaValue.trim() === '') {
                                Swal.showValidationMessage(
                                    'Nama program studi tidak boleh kosong');
                                return false;
                            }

                            return {
                                nama_prodi: namaValue.trim(),
                                jenjang: jenjangValue
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/admin/prodi/${id}`, {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken,
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify({
                                        nama_prodi: result.value.nama_prodi,
                                        jenjang: result.value.jenjang
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: 'Program studi berhasil diupdate',
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire('Error', data.message ||
                                            'Gagal update data', 'error');
                                    }
                                })
                                .catch(() => {
                                    Swal.fire('Error', 'Terjadi kesalahan pada server',
                                        'error');
                                });
                        }
                    });
                });
            });

            // ============================================
            // AUTO CLOSE MODAL & RESET FORM
            // ============================================
            document.querySelectorAll('#addFakultasModal, #addProdiModal').forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function() {
                    if (this.id === 'addFakultasModal') {
                        const form = this.querySelector('form');
                        if (form) form.reset();
                    }
                });
            });

            // ============================================
            // VALIDASI FORM TAMBAH FAKULTAS
            // ============================================
            document.getElementById('formAddFakultas')?.addEventListener('submit', function(e) {
                const nama = this.querySelector('input[name="nama_fakultas"]');

                if (!nama.value.trim()) {
                    e.preventDefault();
                    Swal.fire('Error', 'Nama fakultas harus diisi', 'error');
                    nama.focus();
                    return false;
                }

                if (nama.value.trim().length < 3) {
                    e.preventDefault();
                    Swal.fire('Error', 'Nama fakultas minimal 3 karakter', 'error');
                    nama.focus();
                    return false;
                }

                return true;
            });
        });
    </script>
@endpush
