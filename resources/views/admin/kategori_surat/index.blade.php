@extends('layouts.app')

@section('title', 'Kategori Surat')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Kategori Surat</h4>
            <p class="text-muted mb-0">Kelola kategori surat yang tersedia</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKategoriModal">
            <i class="bi bi-plus-circle me-2"></i>Tambah Kategori
        </button>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="kategoriTable">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Nama Kategori</th>
                            <th>Slug</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kategoris as $index => $kategori)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $kategori->nama_kategori }}</td>
                            <td>{{ $kategori->slug }}</td>
                            <td>{{ Str::limit($kategori->deskripsi, 50) }}</td>
                            <td>
                                @if($kategori->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info edit-kategori" 
                                        data-id="{{ $kategori->id }}"
                                        data-nama="{{ $kategori->nama_kategori }}"
                                        data-deskripsi="{{ $kategori->deskripsi }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-kategori" data-id="{{ $kategori->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                <p class="text-muted mb-0">Belum ada kategori surat</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="addKategoriModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.kategori-surat.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Kategori Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" class="form-control" required>
                        <small class="text-muted">Contoh: Surat Keterangan, Surat Izin, dll</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
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

<!-- Modal Edit Kategori -->
<div class="modal fade" id="editKategoriModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editKategoriForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Kategori Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" id="edit_nama_kategori" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // DataTable
        if ($('#kategoriTable').length) {
            $('#kategoriTable').DataTable({
                responsive: true,
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
                pageLength: 10
            });
        }
        
        // Edit Kategori
        $('.edit-kategori').click(function() {
            let id = $(this).data('id');
            $('#edit_nama_kategori').val($(this).data('nama'));
            $('#edit_deskripsi').val($(this).data('deskripsi'));
            $('#edit_warna_text').val($(this).data('warna'));
            
            $('#editKategoriForm').attr('action', `/admin/kategori-surat/${id}`);
            $('#editKategoriModal').modal('show');
        });
        
        // Delete Kategori
        $('.delete-kategori').click(function() {
            let id = $(this).data('id');
            confirmDelete(`/admin/kategori-surat/${id}`, 'Yakin hapus kategori ini?');
        });
        
    });
</script>
@endpush
@endsection