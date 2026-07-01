{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Petugas')

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
                                    <i class="bi bi-people me-2"></i>Data Petugas
                                </h3>
                                <p class="text-white-50 mb-0">Kelola data petugas dan admin sistem</p>
                            </div>
                            <div>
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-person-check me-1"></i>
                                    @if ($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                        {{ $users->total() }}
                                    @else
                                        {{ $users->count() }}
                                    @endif
                                    Petugas
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
                <h5 class="fw-bold mb-0">Daftar Petugas</h5>
                <p class="text-muted small mb-0">
                    @if ($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari
                        {{ $users->total() }} petugas
                    @else
                        Total {{ $users->count() }} petugas
                    @endif
                </p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-person-plus me-2"></i>Tambah Petugas
            </button>
        </div>

        <!-- Tabel Petugas -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="userTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>NIP</th>
                                <th class="text-center">Posisi</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Tanggal Dibuat</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                <tr>
                                    <td class="text-center">
                                        @if ($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                            {{ $users->firstItem() + $index }}
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                            <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="font-monospace">{{ $user->nip }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if ($user->position == 'admin')
                                            <span class="badge bg-danger">
                                                <i class="bi bi-shield-check me-1"></i>Admin
                                            </span>
                                        @else
                                            <span class="badge bg-info">
                                                <i class="bi bi-person me-1"></i>Petugas
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($user->is_active)
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
                                        <small class="text-muted">{{ $user->created_at->format('d M Y') }}</small>
                                        <br>
                                        <small class="text-muted">{{ $user->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button class="btn btn-sm btn-outline-warning edit-user"
                                                data-id="{{ $user->id }}" title="Edit Petugas">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-user"
                                                data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                                title="Hapus Petugas">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                        <p class="text-muted mb-0">Belum ada data petugas</p>
                                        <p class="text-muted small">Klik tombol "Tambah Petugas" untuk menambahkan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if ($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <div class="text-muted small">
                    Menampilkan
                    {{ $users->firstItem() ?? 0 }}
                    sampai
                    {{ $users->lastItem() ?? 0 }}
                    dari
                    {{ $users->total() }} petugas
                </div>
                <div>
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

    <!-- ============================================ -->
    <!-- MODAL TAMBAH PETUGAS -->
    <!-- ============================================ -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.users.store') }}" method="POST" id="formAddUser">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-person-plus me-2"></i>Tambah Petugas
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control"
                                placeholder="Masukkan alamat email" required>
                            <small class="text-muted">Password akan dikirim otomatis ke email ini</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">NIP <span class="text-danger">*</span></label>
                            <input type="text" name="nip" class="form-control" placeholder="Masukkan NIP"
                                required>
                            <small class="text-muted">Nomor Induk Pegawai</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Posisi <span class="text-danger">*</span></label>
                            <select name="position" class="form-select" required>
                                <option value="petugas">Petugas</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Informasi:</strong> Password akan otomatis dibuat dan dikirim ke email petugas.
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
    <!-- MODAL EDIT PETUGAS -->
    <!-- ============================================ -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square me-2"></i>Edit Petugas
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">NIP <span class="text-danger">*</span></label>
                            <input type="text" name="nip" id="edit_nip" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Posisi <span class="text-danger">*</span></label>
                            <select name="position" id="edit_position" class="form-select" required>
                                <option value="petugas">Petugas</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active"
                                    value="1">
                                <label class="form-check-label fw-bold" for="edit_is_active">
                                    Status Aktif
                                </label>
                            </div>
                            <small class="text-muted">Nonaktifkan untuk menonaktifkan akses petugas</small>
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
        .table> :not(caption)>*>* {
            vertical-align: middle;
        }

        .font-monospace {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        .form-check-input:checked {
            background-color: #6f42c1;
            border-color: #6f42c1;
        }

        .form-check-input:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.25);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

            // ============================================
            // EDIT USER - Load data ke modal
            // ============================================
            document.querySelectorAll('.edit-user').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;

                    // Show loading state
                    Swal.fire({
                        title: 'Memuat data...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`/admin/users/${id}/edit`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Gagal memuat data');
                            }
                            return response.json();
                        })
                        .then(user => {
                            Swal.close();

                            document.getElementById('edit_name').value = user.name || '';
                            document.getElementById('edit_email').value = user.email || '';
                            document.getElementById('edit_nip').value = user.nip || '';
                            document.getElementById('edit_position').value = user.position ||
                                'petugas';
                            document.getElementById('edit_is_active').checked = user
                                .is_active === 1 || user.is_active === true;

                            document.getElementById('editUserForm').action =
                                `/admin/users/${id}`;
                            $('#editUserModal').modal('show');
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message || 'Gagal memuat data petugas'
                            });
                        });
                });
            });

            // ============================================
            // DELETE USER
            // ============================================
            document.querySelectorAll('.delete-user').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name || 'Petugas';

                    Swal.fire({
                        title: 'Apakah anda yakin?',
                        html: `Petugas <strong>"${name}"</strong> akan dihapus permanen!<br>
                       <small class="text-danger">Data ini tidak dapat dikembalikan.</small>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/admin/users/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken,
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: 'Petugas berhasil dihapus',
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire('Error', data.message ||
                                            'Gagal hapus data', 'error');
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
            document.querySelectorAll('#addUserModal, #editUserModal').forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function() {
                    // Reset form jika modal tambah
                    if (this.id === 'addUserModal') {
                        const form = this.querySelector('form');
                        if (form) form.reset();
                    }
                });
            });

            // ============================================
            // VALIDASI FORM TAMBAH
            // ============================================
            document.getElementById('formAddUser')?.addEventListener('submit', function(e) {
                const name = this.querySelector('input[name="name"]');
                const email = this.querySelector('input[name="email"]');
                const nip = this.querySelector('input[name="nip"]');

                if (!name.value.trim()) {
                    e.preventDefault();
                    Swal.fire('Error', 'Nama lengkap harus diisi', 'error');
                    name.focus();
                    return false;
                }

                if (!email.value.trim()) {
                    e.preventDefault();
                    Swal.fire('Error', 'Email harus diisi', 'error');
                    email.focus();
                    return false;
                }

                if (!nip.value.trim()) {
                    e.preventDefault();
                    Swal.fire('Error', 'NIP harus diisi', 'error');
                    nip.focus();
                    return false;
                }

                // Validasi email format
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email.value.trim())) {
                    e.preventDefault();
                    Swal.fire('Error', 'Format email tidak valid', 'error');
                    email.focus();
                    return false;
                }

                return true;
            });

            // ============================================
            // VALIDASI FORM EDIT
            // ============================================
            document.getElementById('editUserForm')?.addEventListener('submit', function(e) {
                const name = this.querySelector('input[name="name"]');
                const email = this.querySelector('input[name="email"]');
                const nip = this.querySelector('input[name="nip"]');

                if (!name.value.trim()) {
                    e.preventDefault();
                    Swal.fire('Error', 'Nama lengkap harus diisi', 'error');
                    name.focus();
                    return false;
                }

                if (!email.value.trim()) {
                    e.preventDefault();
                    Swal.fire('Error', 'Email harus diisi', 'error');
                    email.focus();
                    return false;
                }

                if (!nip.value.trim()) {
                    e.preventDefault();
                    Swal.fire('Error', 'NIP harus diisi', 'error');
                    nip.focus();
                    return false;
                }

                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email.value.trim())) {
                    e.preventDefault();
                    Swal.fire('Error', 'Format email tidak valid', 'error');
                    email.focus();
                    return false;
                }

                return true;
            });
        });
    </script>
@endpush
