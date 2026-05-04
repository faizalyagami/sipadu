<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\FakultasController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\JenisSuratController;
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\Petugas\ApprovalController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\PengajuanSuratController;
use App\Http\Controllers\Profile\ChangePasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root route
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role == 'admin') return redirect()->route('admin.dashboard');
        if ($user->role == 'petugas') return redirect()->route('petugas.dashboard');
        if ($user->role == 'mahasiswa') return redirect()->route('mahasiswa.dashboard');
    }
    return redirect()->route('login');
})->name('root');

// Home route for Laravel default
Route::get('/home', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role == 'admin') return redirect()->route('admin.dashboard');
        if ($user->role == 'petugas') return redirect()->route('petugas.dashboard');
        if ($user->role == 'mahasiswa') return redirect()->route('mahasiswa.dashboard');
    }
    return redirect()->route('login');
})->name('home');

// =============== AUTHENTICATION ROUTES ===============
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

// =============== PROFILE ROUTES (Must be defined before auth middleware group) ===============
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
});

// Change Password (Authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [ChangePasswordController::class, 'showChangeForm'])->name('password.change.form');
    Route::post('/change-password', [ChangePasswordController::class, 'change'])->name('password.change');
});

// =============== ADMIN ROUTES ===============
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-data', [AdminDashboardController::class, 'getDashboardData'])->name('dashboard.data');
    
    // User Management (Petugas)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // Fakultas Management
    Route::get('/fakultas', [FakultasController::class, 'index'])->name('fakultas.index');
    Route::post('/fakultas', [FakultasController::class, 'store'])->name('fakultas.store');
    Route::get('/fakultas/{fakultas}/edit', [FakultasController::class, 'edit'])->name('fakultas.edit');
    Route::put('/fakultas/{fakultas}', [FakultasController::class, 'update'])->name('fakultas.update');
    Route::delete('/fakultas/{fakultas}', [FakultasController::class, 'destroy'])->name('fakultas.destroy');
    
    // Prodi Management
    Route::get('/get-prodi/{fakultasId}', [FakultasController::class, 'getProdiByFakultas'])->name('get-prodi');
    Route::post('/fakultas/{fakultas}/prodi', [FakultasController::class, 'storeProdi'])->name('fakultas.prodi.store');
    Route::put('/prodi/{prodi}', [FakultasController::class, 'updateProdi'])->name('prodi.update');
    Route::delete('/prodi/{prodi}', [FakultasController::class, 'destroyProdi'])->name('prodi.destroy');
    
    // Mahasiswa Management
    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');
    Route::get('/mahasiswa/create', [MahasiswaController::class, 'create'])->name('mahasiswa.create');
    Route::post('/mahasiswa', [MahasiswaController::class, 'store'])->name('mahasiswa.store');
    Route::get('/mahasiswa/{mahasiswa}/edit', [MahasiswaController::class, 'edit'])->name('mahasiswa.edit');
    Route::put('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'update'])->name('mahasiswa.update');
    Route::delete('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'destroy'])->name('mahasiswa.destroy');
    Route::get('/mahasiswa/export', [MahasiswaController::class, 'export'])->name('mahasiswa.export');
    Route::get('/mahasiswa/export-template', [MahasiswaController::class, 'exportTemplate'])->name('mahasiswa.export-template');
    Route::post('/mahasiswa/import', [MahasiswaController::class, 'import'])->name('mahasiswa.import');
    
    // Jenis Surat Management
    Route::get('/jenis-surat', [JenisSuratController::class, 'index'])->name('jenis-surat.index');
    Route::post('/jenis-surat', [JenisSuratController::class, 'store'])->name('jenis-surat.store');
    Route::get('/jenis-surat/{jenisSurat}/edit', [JenisSuratController::class, 'edit'])->name('jenis-surat.edit');
    Route::put('/jenis-surat/{jenisSurat}', [JenisSuratController::class, 'update'])->name('jenis-surat.update');
    Route::delete('/jenis-surat/{jenisSurat}', [JenisSuratController::class, 'destroy'])->name('jenis-surat.destroy');

    Route::post('/jenis-surat/{jenisSurat}/template', [JenisSuratController::class, 'updateTemplate'])
        ->name('jenis-surat.template');

    Route::get('/surat/{id}/pdf', [JenisSuratController::class, 'exportPdf'])
        ->name('surat.pdf');
    Route::get('/surat/{id}/json', [JenisSuratController::class, 'getSuratJson'])->name('surat.json');
});

// =============== PETUGAS ROUTES ===============
Route::middleware(['auth'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', [PetugasDashboardController::class, 'index'])->name('dashboard');
    Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');
    Route::post('/approval/{surat}/approve', [ApprovalController::class, 'approve'])->name('approval.approve');
    Route::post('/approval/{surat}/reject', [ApprovalController::class, 'reject'])->name('approval.reject');
    Route::get('/history', [ApprovalController::class, 'history'])->name('history');
});

// =============== MAHASISWA ROUTES ===============
Route::middleware(['auth'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/pengajuan', [PengajuanSuratController::class, 'index'])->name('pengajuan.index');
    Route::post('/pengajuan', [PengajuanSuratController::class, 'store'])->name('pengajuan.store');
    Route::get('/pengajuan/{surat}/download', [PengajuanSuratController::class, 'download'])->name('pengajuan.download');
    Route::get('/riwayat', [PengajuanSuratController::class, 'history'])->name('riwayat');
});