<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistem Pelayanan Surat Terpadu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-purple: #6f42c1;
            --secondary-purple: #8b5cf6;
            --dark-purple: #5a32a3;
            --light-purple: #e9d5ff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
            margin: 0 auto;
        }

        .login-card {
            border-radius: 24px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            border: none;
            background: white;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            color: white;
            padding: 35px 30px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            pointer-events: none;
        }

        .login-header .icon-wrapper {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .login-header .icon-wrapper i {
            font-size: 2.2rem;
        }

        .login-header h3 {
            font-weight: 700;
            font-size: 1.4rem;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .login-header p {
            font-size: 0.85rem;
            opacity: 0.85;
            margin-bottom: 0;
        }

        .login-body {
            padding: 35px 30px 30px;
        }

        .login-body .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #4a5568;
            margin-bottom: 6px;
        }

        .input-group {
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
            background: white;
        }

        .input-group:focus-within {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 4px rgba(111, 66, 193, 0.1);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: #8b5cf6;
            padding: 0 0 0 14px;
            font-size: 1.1rem;
        }

        .form-control {
            border: none;
            padding: 12px 14px;
            font-size: 0.95rem;
            background: transparent;
            box-shadow: none !important;
        }

        .form-control:focus {
            background: transparent;
        }

        .form-control::placeholder {
            color: #a0aec0;
            font-size: 0.9rem;
        }

        .form-check {
            margin-top: 5px;
        }

        .form-check-input {
            border-radius: 4px;
            border: 2px solid #cbd5e0;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--primary-purple);
            border-color: var(--primary-purple);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(111, 66, 193, 0.15);
        }

        .form-check-label {
            font-size: 0.9rem;
            color: #4a5568;
            cursor: pointer;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            border: none;
            padding: 14px;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            margin-top: 5px;
            color: white;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(111, 66, 193, 0.35);
            color: white;
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        .login-footer {
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid #edf2f7;
            margin-top: 10px;
        }

        .login-footer small {
            color: #718096;
            font-size: 0.75rem;
        }

        .login-footer small i {
            color: var(--primary-purple);
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-danger {
            background: #fce4ec;
            color: #9a1a2e;
            border-left: 4px solid #dc3545;
        }

        .alert-success {
            background: #e8f5e9;
            color: #1e7e34;
            border-left: 4px solid #28a745;
        }

        .login-info {
            background: #f8f4ff;
            border-radius: 10px;
            padding: 12px 16px;
            margin-top: 15px;
            border: 1px solid rgba(111, 66, 193, 0.1);
        }

        .login-info small {
            color: #6f42c1;
            font-size: 0.75rem;
            display: block;
        }

        .login-info small i {
            margin-right: 6px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-body {
                padding: 25px 20px 20px;
            }

            .login-header {
                padding: 25px 20px;
            }

            .login-header h3 {
                font-size: 1.2rem;
            }

            .login-header .icon-wrapper {
                width: 60px;
                height: 60px;
            }

            .login-header .icon-wrapper i {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="card login-card">
            <!-- Header -->
            <div class="login-header">
                <div class="icon-wrapper">
                    <i class="bi bi-envelope-paper"></i>
                </div>
                <h3>Sistem Pelayanan Surat Terpadu</h3>
                <p>Fakultas Psikologi - Universitas Islam Bandung</p>
            </div>

            <!-- Body -->
            <div class="login-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">NPM atau Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="login" class="form-control"
                                placeholder="Masukkan NPM atau Email" value="{{ old('login') }}" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password"
                                required>
                            <button class="btn btn-link input-group-text" type="button" id="togglePassword"
                                style="padding-right: 14px;">
                                <i class="bi bi-eye-slash" id="passwordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Ingat Saya</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-decoration-none small"
                            style="color: var(--primary-purple);">
                            Lupa Password?
                        </a>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                </form>

                <div class="login-info">
                    <small>
                        <i class="bi bi-info-circle"></i>
                        <strong>Mahasiswa:</strong> Gunakan NPM sebagai username dan password default NPM Anda
                    </small>
                    <small class="mt-1">
                        <i class="bi bi-person-badge"></i>
                        <strong>Admin/Petugas:</strong> Gunakan email dan password yang telah diberikan
                    </small>
                </div>

                <div class="login-footer">
                    <small>
                        <i class="bi bi-c-circle"></i> {{ date('Y') }} Universitas Islam Bandung &nbsp;|&nbsp; <i
                            class="bi bi-code"></i> v1.0
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Password Visibility
        document.getElementById('togglePassword')?.addEventListener('click', function() {
            const passwordInput = document.querySelector('input[name="password"]');
            const icon = document.getElementById('passwordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        });

        // Auto focus pada input pertama
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('input[name="login"]');
            if (firstInput) firstInput.focus();
        });
    </script>
</body>

</html>
