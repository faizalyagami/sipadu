@extends('layouts.app')

@section('title', 'Konfirmasi Password')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%);">
                    <h5 class="mb-0">Konfirmasi Password</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-shield-lock"></i> 
                        Silakan konfirmasi password Anda untuk melanjutkan aksi ini.
                    </div>
                    
                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Konfirmasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection