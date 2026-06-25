@extends('layouts.app')

@section('title', 'Kategori Surat')

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
                                    <i class="bi bi-tags me-2"></i>Kategori Surat
                                </h3>
                                <p class="text-white-50 mb-0">Kelola kategori surat yang tersedia</p>
                            </div>
                            <div>
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-tag me-1"></i>
                                    {{ $kategoris->count() }} Kategori
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

        <!-- Toolbar -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0">Daftar Kategori</h5>
                <p class="text-muted small mb-0">Total {{ $kategoris->count() }} kategori surat</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKategoriModal">
                <i class="bi bi-plus-circle me-2"></i>Tambah Kategori
            </button>
        </div>

        <!-- Tabel Kategori -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="kategoriTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Nama Kategori</th>
                                <th>Slug</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Dibuat</th>
                                <th class="text-center" style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kategoris as $index => $kategori)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge"
                                                style="background-color: {{ $kategori->warna ?? '#6f42c1' }}; width: 12px; height: 12px; border-radius: 4px;"></span>
                                            <strong>{{ $kategori->nama_kategori }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="font-monospace text-muted">{{ $kategori->slug }}</span>
                                    </td>
                                    <td>
                                        @if ($kategori->deskripsi)
                                            {{ Str::limit($kategori->deskripsi, 50) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($kategori->is_active)
                                            <span class="badge bg-success">
                                                <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted">{{ $kategori->created_at->format('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button class="btn btn-sm btn-outline-info edit-kategori"
                                                data-id="{{ $kategori->id }}" data-nama="{{ $kategori->nama_kategori }}"
                                                data-deskripsi="{{ $kategori->deskripsi }}"
                                                data-warna="{{ $kategori->warna ?? '#6f42c1' }}"
                                                data-status="{{ $kategori->is_active }}" title="Edit Kategori">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('admin.kategori-surat.destroy', $kategori->id) }}"
                                                method="POST" class="d-inline delete-form"
                                                data-nama="{{ $kategori->nama_kategori }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger delete-kategori"
                                                    data-id="{{ $kategori->id }}"
                                                    data-nama="{{ $kategori->nama_kategori }}" title="Hapus Kategori">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-tags fs-1 text-muted d-block mb-3"></i>
                                        <p class="text-muted mb-0">Belum ada kategori surat</p>
                                        <p class="text-muted small">Klik tombol "Tambah Kategori" untuk menambahkan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="row mt-4 g-3">
            <div class="col-md-4">
                <div class="card bg-success bg-opacity-10 border-0">
                    <div class="card-body text-center">
                        <h5 class="text-success">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ $kategoris->where('is_active', true)->count() }}
                        </h5>
                        <p class="text-muted mb-0">Kategori Aktif</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-secondary bg-opacity-10 border-0">
                    <div class="card-body text-center">
                        <h5 class="text-secondary">
                            <i class="bi bi-circle me-2"></i>
                            {{ $kategoris->where('is_active', false)->count() }}
                        </h5>
                        <p class="text-muted mb-0">Kategori Nonaktif</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary bg-opacity-10 border-0">
                    <div class="card-body text-center">
                        <h5 class="text-primary">
                            <i class="bi bi-tags me-2"></i>
                            {{ $kategoris->count() }}
                        </h5>
                        <p class="text-muted mb-0">Total Kategori</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL TAMBAH KATEGORI -->
    <!-- ============================================ -->
    <div class="modal fade" id="addKategoriModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.kategori-surat.store') }}" method="POST" id="formAddKategori">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Kategori Surat
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" class="form-control"
                                placeholder="Contoh: Surat Keterangan" required>
                            <small class="text-muted">Slug akan dibuat otomatis dari nama kategori</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Masukkan deskripsi kategori"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Warna</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" name="warna" class="form-control form-control-color"
                                    style="width: 60px; padding: 0; height: 38px;" value="#6f42c1">
                                <span class="text-muted small">Pilih warna untuk badge kategori</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="add_is_active"
                                    value="1" checked>
                                <label class="form-check-label fw-bold" for="add_is_active">
                                    Aktifkan Kategori
                                </label>
                            </div>
                            <small class="text-muted">Nonaktifkan untuk menyembunyikan kategori</small>
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
    <!-- MODAL EDIT KATEGORI -->
    <!-- ============================================ -->
    <div class="modal fade" id="editKategoriModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editKategoriForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square me-2"></i>Edit Kategori Surat
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" id="edit_nama_kategori" class="form-control"
                                required>
                            <small class="text-muted">Slug akan diperbarui otomatis</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Warna</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" name="warna" id="edit_warna"
                                    class="form-control form-control-color" style="width: 60px; padding: 0; height: 38px;"
                                    value="#6f42c1">
                                <span class="text-muted small">Pilih warna untuk badge kategori</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active"
                                    value="1">
                                <label class="form-check-label fw-bold" for="edit_is_active">
                                    Aktifkan Kategori
                                </label>
                            </div>
                            <small class="text-muted">Nonaktifkan untuk menyembunyikan kategori</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .font-monospace {
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }

        .form-check-input:checked {
            background-color: #6f42c1;
            border-color: #6f42c1;
        }

        .form-check-input:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.25);
        }

        .form-control-color {
            cursor: pointer;
            border-radius: 8px;
        }

        .form-control-color::-webkit-color-swatch-wrapper {
            padding: 0;
        }

        .form-control-color::-webkit-color-swatch {
            border: none;
            border-radius: 6px;
        }

        .table> :not(caption)>*>* {
            vertical-align: middle;
        }

        /* DataTable Custom */
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 6px 12px;
            margin-left: 8px;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.25);
            outline: none;
        }

        .dataTables_wrapper .dataTables_length select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 4px 8px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

            // ============================================
            // INIT DATATABLE
            // ============================================
            if (document.getElementById('kategoriTable')) {
                $('#kategoriTable').DataTable({
                    responsive: true,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                    },
                    pageLength: 10,
                    order: [
                        [0, 'asc']
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: [5, 6]
                    }]
                });
            }

            // ============================================
            // EDIT KATEGORI - Load data ke modal
            // ============================================
            document.querySelectorAll('.edit-kategori').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const nama = this.dataset.nama || '';
                    const deskripsi = this.dataset.deskripsi || '';
                    const warna = this.dataset.warna || '#6f42c1';
                    const status = this.dataset.status === '1' || this.dataset.status === true;

                    document.getElementById('edit_nama_kategori').value = nama;
                    document.getElementById('edit_deskripsi').value = deskripsi;
                    document.getElementById('edit_warna').value = warna;
                    document.getElementById('edit_is_active').checked = status;

                    document.getElementById('editKategoriForm').action =
                        `/admin/kategori-surat/${id}`;
                    $('#editKategoriModal').modal('show');
                });
            });

            // ============================================
            // DELETE KATEGORI - Dengan Form Submit
            // ============================================
            document.querySelectorAll('.delete-kategori').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    const form = this.closest('form');
                    const nama = this.dataset.nama || 'Kategori';

                    Swal.fire({
                        title: 'Apakah anda yakin?',
                        html: `Kategori <strong>"${nama}"</strong> akan dihapus permanen!<br>
                       <small class="text-danger">Data ini tidak dapat dikembalikan.</small>`,
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
                });
            });

            // ============================================
            // AUTO CLOSE MODAL & RESET FORM
            // ============================================
            document.querySelectorAll('#addKategoriModal, #editKategoriModal').forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function() {
                    if (this.id === 'addKategoriModal') {
                        const form = this.querySelector('form');
                        if (form) form.reset();
                    }
                });
            });

            // ============================================
            // VALIDASI FORM TAMBAH
            // ============================================
            document.getElementById('formAddKategori')?.addEventListener('submit', function(e) {
                const nama = this.querySelector('input[name="nama_kategori"]');

                if (!nama.value.trim()) {
                    e.preventDefault();
                    Swal.fire('Error', 'Nama kategori harus diisi', 'error');
                    nama.focus();
                    return false;
                }

                if (nama.value.trim().length < 3) {
                    e.preventDefault();
                    Swal.fire('Error', 'Nama kategori minimal 3 karakter', 'error');
                    nama.focus();
                    return false;
                }

                return true;
            });

            // ============================================
            // VALIDASI FORM EDIT
            // ============================================
            document.getElementById('editKategoriForm')?.addEventListener('submit', function(e) {
                const nama = this.querySelector('input[name="nama_kategori"]');

                if (!nama.value.trim()) {
                    e.preventDefault();
                    Swal.fire('Error', 'Nama kategori harus diisi', 'error');
                    nama.focus();
                    return false;
                }

                if (nama.value.trim().length < 3) {
                    e.preventDefault();
                    Swal.fire('Error', 'Nama kategori minimal 3 karakter', 'error');
                    nama.focus();
                    return false;
                }

                return true;
            });
        });
    </script>
@endpush
