@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%);">
                    <h5 class="mb-0"><i class="bi bi-key"></i> Ubah Password</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('password.change') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Password Saat Ini</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="current_password" id="current_password" 
                                       class="form-control @error('current_password') is-invalid @enderror" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="new_password" id="new_password" 
                                       class="form-control @error('new_password') is-invalid @enderror" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength mt-2" id="passwordStrength"></div>
                            @error('new_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka</small>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                                       class="form-control" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password_confirmation">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div id="passwordMatch" class="small mt-1"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Password strength checker
    $('#new_password').on('keyup', function() {
        const password = $(this).val();
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;
        
        const strengthBar = $('#passwordStrength');
        if (strength <= 2) {
            strengthBar.css('background', '#dc3545').css('width', '20%').css('height', '4px').css('border-radius', '2px');
            strengthBar.html('');
        } else if (strength <= 3) {
            strengthBar.css('background', '#ffc107').css('width', '50%').css('height', '4px').css('border-radius', '2px');
            strengthBar.html('');
        } else if (strength <= 4) {
            strengthBar.css('background', '#17a2b8').css('width', '75%').css('height', '4px').css('border-radius', '2px');
            strengthBar.html('');
        } else {
            strengthBar.css('background', '#28a745').css('width', '100%').css('height', '4px').css('border-radius', '2px');
            strengthBar.html('');
        }
        
        checkPasswordMatch();
    });
    
    // Password match checker
    $('#new_password_confirmation').on('keyup', function() {
        checkPasswordMatch();
    });
    
    function checkPasswordMatch() {
        const password = $('#new_password').val();
        const confirm = $('#new_password_confirmation').val();
        
        if (confirm.length > 0) {
            if (password === confirm) {
                $('#passwordMatch').html('<i class="bi bi-check-circle text-success"></i> Password cocok').css('color', 'green');
                $('#submitBtn').prop('disabled', false);
            } else {
                $('#passwordMatch').html('<i class="bi bi-x-circle text-danger"></i> Password tidak cocok').css('color', 'red');
                $('#submitBtn').prop('disabled', true);
            }
        } else {
            $('#passwordMatch').html('');
            $('#submitBtn').prop('disabled', false);
        }
    }
    
    // Toggle password visibility
    $('.toggle-password').click(function() {
        const target = $(this).data('target');
        const input = $('#' + target);
        const type = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);
        $(this).find('i').toggleClass('bi-eye bi-eye-slash');
    });
</script>
@endpush