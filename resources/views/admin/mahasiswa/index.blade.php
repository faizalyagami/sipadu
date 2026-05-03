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

    <!-- ============ ALERT SECTION ============ -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- IMPORT SUMMARY - DISISIPKAN DI SINI -->
    @if(session('import_summary'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Hasil Import:</strong>
        Total: {{ session('import_summary')['total'] }} baris |
        Berhasil: {{ session('import_summary')['success'] }} |
        Gagal: {{ session('import_summary')['error'] }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <!-- ============ END ALERT SECTION ============ -->

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 datatable">
                    <thead>
                        <tr>
                            <th>NPM</th>
                            <th>Nama Mahasiswa</th>
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
                            <td>{{ $mhs->npm }}</td>
                            <td>{{ $mhs->nama_lengkap }}</td>
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
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>
                                <p class="text-muted mb-0">Belum ada data mahasiswa</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Mahasiswa -->
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

<!-- Modal Import Excel -->
<div class="modal fade" id="importMahasiswaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.mahasiswa.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">File Excel</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Format: .xlsx, .xls, atau .csv</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-download"></i> 
                        <a href="{{ route('admin.mahasiswa.export-template') }}" class="alert-link">Download template Excel</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Siapkan data prodi dari server
    const allProdis = @json($allProdis ?? []);
    
    $(document).ready(function() {
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
        
        $('.delete-mahasiswa').click(function() {
            let id = $(this).data('id');
            confirmDelete(`/admin/mahasiswa/${id}`, 'Yakin hapus mahasiswa ini?');
        });
    });
</script>
@endpush
@endsection