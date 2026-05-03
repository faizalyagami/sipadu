{{-- resources/views/admin/fakultas/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Fakultas')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Data Fakultas</h4>
            <p class="text-muted mb-0">Kelola data fakultas dan program studi</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFakultasModal">
            <i class="bi bi-plus-circle me-2"></i>Tambah Fakultas
        </button>
    </div>

    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Fakultas</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($fakultas as $fak)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-building me-2 text-primary"></i>
                                <strong>{{ $fak->nama_fakultas }}</strong>
                                <br>
                                <small class="text-muted">{{ $fak->prodis->count() }} Program Studi</small>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-warning edit-fakultas" data-id="{{ $fak->id }}" data-nama="{{ $fak->nama_fakultas }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-fakultas" data-id="{{ $fak->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="mt-2 mb-0">Belum ada data fakultas</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-7 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Program Studi</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="accordionFakultas">
                        @foreach($fakultas as $fak)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $fak->id }}">
                                    <i class="bi bi-building me-2"></i> {{ $fak->nama_fakultas }}
                                    <span class="badge bg-primary ms-2">{{ $fak->prodis->count() }}</span>
                                </button>
                            </h2>
                            <div id="collapse{{ $fak->id }}" class="accordion-collapse collapse" data-bs-parent="#accordionFakultas">
                                <div class="accordion-body">
                                    <button class="btn btn-sm btn-success mb-3 add-prodi" data-fakultas-id="{{ $fak->id }}" data-fakultas-nama="{{ $fak->nama_fakultas }}">
                                        <i class="bi bi-plus-circle"></i> Tambah Program Studi
                                    </button>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Program Studi</th>
                                                    <th>Jenjang</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($fak->prodis as $prodi)
                                                <tr>
                                                    <td>{{ $prodi->nama_prodi }}</td>
                                                    <td><span class="badge bg-info">{{ $prodi->jenjang }}</span></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning edit-prodi" data-id="{{ $prodi->id }}" data-nama="{{ $prodi->nama_prodi }}" data-jenjang="{{ $prodi->jenjang }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger delete-prodi" data-id="{{ $prodi->id }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Fakultas -->
<div class="modal fade" id="addFakultasModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.fakultas.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Fakultas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Fakultas</label>
                        <input type="text" name="nama_fakultas" class="form-control" required>
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

<!-- Modal Tambah Prodi -->
<div class="modal fade" id="addProdiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addProdiForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Program Studi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fakultas</label>
                        <input type="text" id="fakultas_nama_display" class="form-control" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Program Studi</label>
                        <input type="text" name="nama_prodi" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenjang</label>
                        <select name="jenjang" class="form-select" required>
                            <option value="">Pilih Jenjang</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                            <option value="Profesi">Profesi</option>
                        </select>
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

@push('scripts')
<script>
    $(document).ready(function() {
        $('.edit-fakultas').click(function() {
            let id = $(this).data('id');
            let nama = $(this).data('nama');
            Swal.fire({
                title: 'Edit Fakultas',
                input: 'text',
                inputValue: nama,
                inputLabel: 'Nama Fakultas',
                showCancelButton: true,
                confirmButtonColor: '#6f42c1',
                confirmButtonText: 'Update',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    $.ajax({
                        url: `/admin/fakultas/${id}`,
                        method: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            nama_fakultas: result.value
                        },
                        success: function() {
                            location.reload();
                        }
                    });
                }
            });
        });
        
        $('.delete-fakultas').click(function() {
            let id = $(this).data('id');
            confirmDelete(`/admin/fakultas/${id}`, 'Yakin hapus fakultas ini?');
        });
        
        $('.add-prodi').click(function() {
            let fakultasId = $(this).data('fakultas-id');
            let fakultasNama = $(this).data('fakultas-nama');
            $('#fakultas_nama_display').val(fakultasNama);
            $('#addProdiForm').attr('action', `/admin/fakultas/${fakultasId}/prodi`);
            $('#addProdiModal').modal('show');
        });
        
        $('.edit-prodi').click(function() {
            let id = $(this).data('id');
            let nama = $(this).data('nama');
            let jenjang = $(this).data('jenjang');
            Swal.fire({
                title: 'Edit Program Studi',
                html: `
                    <input id="nama_prodi" class="swal2-input" placeholder="Nama Program Studi" value="${nama}">
                    <select id="jenjang" class="swal2-select">
                        <option value="S1" ${jenjang == 'S1' ? 'selected' : ''}>S1</option>
                        <option value="S2" ${jenjang == 'S2' ? 'selected' : ''}>S2</option>
                        <option value="S3" ${jenjang == 'S3' ? 'selected' : ''}>S3</option>
                        <option value="Profesi" ${jenjang == 'Profesi' ? 'selected' : ''}>Profesi</option>
                    </select>
                `,
                showCancelButton: true,
                confirmButtonColor: '#6f42c1',
                confirmButtonText: 'Update',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    return {
                        nama_prodi: document.getElementById('nama_prodi').value,
                        jenjang: document.getElementById('jenjang').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/prodi/${id}`,
                        method: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            nama_prodi: result.value.nama_prodi,
                            jenjang: result.value.jenjang
                        },
                        success: function() {
                            location.reload();
                        }
                    });
                }
            });
        });
        
        $('.delete-prodi').click(function() {
            let id = $(this).data('id');
            confirmDelete(`/admin/prodi/${id}`, 'Yakin hapus program studi ini?');
        });
    });
</script>
@endpush
@endsection