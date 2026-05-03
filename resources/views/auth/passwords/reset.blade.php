<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Sistem Surat Unisba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-purple: #6f42c1;
            --secondary-purple: #8b5cf6;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            border: none;
            padding: 12px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(111, 66, 193, 0.3);
        }
        
        .form-control:focus {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
        }
        
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
            transition: all 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="bg-gradient rounded-circle p-3 d-inline-block" style="background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);">
                                <i class="bi bi-key fs-1 text-white"></i>
                            </div>
                            <h3 class="mt-3">Reset Password</h3>
                            <p class="text-muted">Masukkan password baru untuk akun Anda</p>
                        </div>
                        
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> 
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" 
                                           value="{{ $email ?? old('email') }}" required autofocus>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength" id="passwordStrength"></div>
                                <small class="text-muted">Minimal 8 karakter, mengandung huruf dan angka</small>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div id="passwordMatch" class="small mt-1"></div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 text-white" id="submitBtn">
                                <i class="bi bi-check-circle"></i> Reset Password
                            </button>
                        </form>
                        
                        <hr class="my-4">
                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <i class="bi bi-arrow-left"></i> Kembali ke Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength checker
        $('#password').on('keyup', function() {
            const password = $(this).val();
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z\d]/)) strength++;
            
            const strengthBar = $('#passwordStrength');
            if (strength <= 2) {
                strengthBar.css('background', '#dc3545').css('width', '20%');
            } else if (strength <= 3) {
                strengthBar.css('background', '#ffc107').css('width', '50%');
            } else if (strength <= 4) {
                strengthBar.css('background', '#17a2b8').css('width', '75%');
            } else {
                strengthBar.css('background', '#28a745').css('width', '100%');
            }
            
            checkPasswordMatch();
        });
        
        // Password match checker
        $('#password_confirmation').on('keyup', function() {
            checkPasswordMatch();
        });
        
        function checkPasswordMatch() {
            const password = $('#password').val();
            const confirm = $('#password_confirmation').val();
            
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
</body>
</html>