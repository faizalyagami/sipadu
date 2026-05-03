{{-- resources/views/admin/mahasiswa/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Mahasiswa')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person-plus me-2"></i>Tambah Mahasiswa Baru
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.mahasiswa.store') }}" method="POST" id="formMahasiswa">
                        @csrf
                        
                        <ul class="nav nav-tabs mb-4" id="mahasiswaTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#dataPribadi" type="button" role="tab">
                                    <i class="bi bi-person"></i> Data Pribadi
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#dataAkademik" type="button" role="tab">
                                    <i class="bi bi-mortarboard"></i> Data Akademik
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#dataKontak" type="button" role="tab">
                                    <i class="bi bi-envelope"></i> Data Kontak
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content">
                            <!-- Tab Data Pribadi -->
                            <div class="tab-pane fade show active" id="dataPribadi" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NPM <span class="text-danger">*</span></label>
                                        <input type="text" name="npm" class="form-control @error('npm') is-invalid @enderror" 
                                               value="{{ old('npm') }}" required placeholder="Contoh: 202410001">
                                        @error('npm')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Nomor Pokok Mahasiswa (10 digit)</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" 
                                               value="{{ old('nama_lengkap') }}" required>
                                        @error('nama_lengkap')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                        <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" 
                                               value="{{ old('tempat_lahir') }}" required>
                                        @error('tempat_lahir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                                               value="{{ old('tanggal_lahir') }}" required>
                                        @error('tanggal_lahir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" 
                                                  rows="3" required>{{ old('alamat') }}</textarea>
                                        @error('alamat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab Data Akademik -->
                            <div class="tab-pane fade" id="dataAkademik" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fakultas <span class="text-danger">*</span></label>
                                        <select name="fakultas_id" id="fakultas_id" class="form-select @error('fakultas_id') is-invalid @enderror" required>
                                            <option value="">Pilih Fakultas</option>
                                            @foreach($fakultas as $fak)
                                            <option value="{{ $fak->id }}" {{ old('fakultas_id') == $fak->id ? 'selected' : '' }}>
                                                {{ $fak->nama_fakultas }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('fakultas_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                                        <select name="prodi_id" id="prodi_id" class="form-select @error('prodi_id') is-invalid @enderror" required>
                                            <option value="">Pilih Fakultas Terlebih Dahulu</option>
                                        </select>
                                        @error('prodi_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_masuk" class="form-control @error('tanggal_masuk') is-invalid @enderror" 
                                               value="{{ old('tanggal_masuk') }}" required>
                                        @error('tanggal_masuk')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status Mahasiswa <span class="text-danger">*</span></label>
                                        <select name="status_mahasiswa" class="form-select @error('status_mahasiswa') is-invalid @enderror" required>
                                            <option value="Aktif" {{ old('status_mahasiswa') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="Cuti" {{ old('status_mahasiswa') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                                            <option value="Lulus" {{ old('status_mahasiswa') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                                        </select>
                                        @error('status_mahasiswa')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">IPK</label>
                                        <input type="number" name="ipk" step="0.01" class="form-control @error('ipk') is-invalid @enderror" 
                                               value="{{ old('ipk') }}" placeholder="0.00 - 4.00">
                                        @error('ipk')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Isi dengan IPK terakhir (opsional)</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab Data Kontak -->
                            <div class="tab-pane fade" id="dataKontak" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                               value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Email akan digunakan untuk login</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">No Handphone <span class="text-danger">*</span></label>
                                        <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror" 
                                               value="{{ old('no_hp') }}" required placeholder="Contoh: 081234567890">
                                        @error('no_hp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">No WhatsApp</label>
                                        <input type="text" name="no_wa" class="form-control @error('no_wa') is-invalid @enderror" 
                                               value="{{ old('no_wa') }}" placeholder="Contoh: 081234567890">
                                        @error('no_wa')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email Pribadi</label>
                                        <input type="email" name="email_pribadi" class="form-control @error('email_pribadi') is-invalid @enderror" 
                                               value="{{ old('email_pribadi') }}" placeholder="email.pribadi@gmail.com">
                                        @error('email_pribadi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnSubmit">
                                <i class="bi bi-save"></i> Simpan Mahasiswa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Data prodi dari server
    const allProdis = @json($allProdis ?? []);
    
    $(document).ready(function() {
        // Filter prodi berdasarkan fakultas yang dipilih
        $('#fakultas_id').change(function() {
            let fakultasId = $(this).val();
            let prodiSelect = $('#prodi_id');
            prodiSelect.empty().append('<option value="">Pilih Program Studi</option>');
            
            if (fakultasId) {
                let filteredProdis = allProdis.filter(prodi => prodi.fakultas_id == fakultasId);
                if (filteredProdis.length > 0) {
                    filteredProdis.forEach(prodi => {
                        prodiSelect.append(`<option value="${prodi.id}">${prodi.nama_prodi} (${prodi.jenjang})</option>`);
                    });
                } else {
                    prodiSelect.append('<option value="">Tidak ada program studi</option>');
                }
            }
        });
        
        // Trigger change on page load if fakultas already selected
        if ($('#fakultas_id').val()) {
            $('#fakultas_id').trigger('change');
        }
        
        // Validasi form sebelum submit
        $('#formMahasiswa').on('submit', function(e) {
            let npm = $('input[name="npm"]').val();
            let nama = $('input[name="nama_lengkap"]').val();
            let email = $('input[name="email"]').val();
            let prodi = $('#prodi_id').val();
            
            if (!npm || !nama || !email || !prodi) {
                e.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'Harap lengkapi semua field yang wajib diisi',
                    icon: 'error',
                    confirmButtonColor: '#6f42c1'
                });
                return false;
            }
            
            // Show loading
            $('#btnSubmit').html('<i class="bi bi-hourglass-split"></i> Menyimpan...').prop('disabled', true);
        });
    });
</script>
@endpush
@endsection