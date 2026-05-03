{{-- resources/views/admin/mahasiswa/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Mahasiswa')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Data Mahasiswa</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.mahasiswa.update', $mahasiswa) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NPM</label>
                                <input type="text" class="form-control" value="{{ $mahasiswa->npm }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" value="{{ $mahasiswa->nama_lengkap }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fakultas</label>
                                <select name="fakultas_id" id="fakultas_id" class="form-select" required>
                                    <option value="">Pilih Fakultas</option>
                                    @foreach($fakultas as $fak)
                                    <option value="{{ $fak->id }}" {{ $mahasiswa->prodi->fakultas_id == $fak->id ? 'selected' : '' }}>
                                        {{ $fak->nama_fakultas }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Program Studi</label>
                                <select name="prodi_id" id="prodi_id" class="form-select" required>
                                    <option value="">Pilih Program Studi</option>
                                    @foreach($prodis as $prodi)
                                    <option value="{{ $prodi->id }}" {{ $mahasiswa->prodi_id == $prodi->id ? 'selected' : '' }}>
                                        {{ $prodi->nama_prodi }} ({{ $prodi->jenjang }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Mahasiswa</label>
                                <select name="status_mahasiswa" class="form-select" required>
                                    <option value="Aktif" {{ $mahasiswa->status_mahasiswa == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Cuti" {{ $mahasiswa->status_mahasiswa == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                                    <option value="Lulus" {{ $mahasiswa->status_mahasiswa == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">IPK</label>
                                <input type="number" name="ipk" step="0.01" class="form-control" value="{{ $mahasiswa->ipk }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" value="{{ $mahasiswa->tempat_lahir }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="{{ $mahasiswa->tanggal_lahir->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="2" required>{{ $mahasiswa->alamat }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No HP</label>
                                <input type="text" name="no_hp" class="form-control" value="{{ $mahasiswa->no_hp }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Masuk</label>
                                <input type="date" name="tanggal_masuk" class="form-control" value="{{ $mahasiswa->tanggal_masuk->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const allProdis = @json($prodis ?? []);
    
    $('#fakultas_id').change(function() {
        let fakultasId = $(this).val();
        let prodiSelect = $('#prodi_id');
        let currentProdiId = {{ $mahasiswa->prodi_id }};
        
        prodiSelect.empty().append('<option value="">Pilih Program Studi</option>');
        
        if (fakultasId) {
            let filteredProdis = allProdis.filter(prodi => prodi.fakultas_id == fakultasId);
            filteredProdis.forEach(prodi => {
                let selected = (prodi.id == currentProdiId) ? 'selected' : '';
                prodiSelect.append(`<option value="${prodi.id}" ${selected}>${prodi.nama_prodi} (${prodi.jenjang})</option>`);
            });
        }
    });
</script>
@endpush
@endsection