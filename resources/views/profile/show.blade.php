@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%);">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Profil Saya</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">No Telepon</label>
                            <input type="text" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror" 
                                   value="{{ old('no_telepon', $user->no_telepon) }}">
                            @error('no_telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        @if($user->role == 'petugas' || $user->role == 'admin')
                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" class="form-control" value="{{ $user->nip }}" readonly disabled>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Posisi</label>
                            <input type="text" class="form-control" value="{{ ucfirst($user->position) }}" readonly disabled>
                        </div>
                        @endif
                        
                        @if($user->role == 'mahasiswa' && $user->mahasiswa)
                        <hr>
                        <h6 class="mb-3">Data Mahasiswa</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NPM</label>
                                <input type="text" class="form-control" value="{{ $user->mahasiswa->npm }}" readonly disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Program Studi</label>
                                <input type="text" class="form-control" value="{{ $user->mahasiswa->prodi->nama_prodi }}" readonly disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <input type="text" class="form-control" value="{{ $user->mahasiswa->status_mahasiswa }}" readonly disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">IPK</label>
                                <input type="text" class="form-control" value="{{ $user->mahasiswa->ipk ?? '-' }}" readonly disabled>
                            </div>
                        </div>
                        @endif
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('password.change.form') }}" class="btn btn-warning">
                                <i class="bi bi-key"></i> Ubah Password
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection