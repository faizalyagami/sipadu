<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\FakultasController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\JenisSuratController;
use App\Http\Controllers\Admin\KategoriSuratController;
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\Petugas\ApprovalController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\PengajuanSuratController;
use App\Http\Controllers\Profile\ChangePasswordController;
use Illuminate\Http\Request;

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

// =============== PROFILE ROUTES ===============
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
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-data', [AdminDashboardController::class, 'getDashboardData'])->name('dashboard.data');

    // User Management (Petugas)
    Route::resource('users', UserController::class)->except(['show']);

    // Fakultas Management
    Route::get('/fakultas', [FakultasController::class, 'index'])->name('fakultas.index');
    Route::post('/fakultas', [FakultasController::class, 'store'])->name('fakultas.store');
    Route::get('/fakultas/create', [FakultasController::class, 'create'])->name('fakultas.create');
    Route::put('/fakultas/{fakultas}', [FakultasController::class, 'update'])->name('fakultas.update');
    Route::delete('/fakultas/{fakultas}', [FakultasController::class, 'destroy'])->name('fakultas.destroy');
    Route::get('/fakultas/{fakultas}/edit', [FakultasController::class, 'edit'])->name('fakultas.edit');

    // Prodi Management (di dalam fakultas)
    Route::get('/get-prodi/{fakultasId}', [FakultasController::class, 'getProdiByFakultas'])->name('get-prodi');
    Route::post('/fakultas/{fakultas}/prodi', [FakultasController::class, 'storeProdi'])->name('fakultas.prodi.store');
    Route::put('/prodi/{prodi}', [FakultasController::class, 'updateProdi'])->name('prodi.update');
    Route::delete('/prodi/{prodi}', [FakultasController::class, 'destroyProdi'])->name('prodi.destroy');

    // Mahasiswa Management
    Route::get('/mahasiswa/export', [MahasiswaController::class, 'export'])->name('mahasiswa.export');
    Route::get('/mahasiswa/export/{status}', [MahasiswaController::class, 'export'])->name('mahasiswa.export.status');
    Route::get('/mahasiswa/export-template', [MahasiswaController::class, 'exportTemplate'])->name('mahasiswa.export-template');
    Route::post('/mahasiswa/import', [MahasiswaController::class, 'import'])->name('mahasiswa.import');
    Route::get('/mahasiswa/import-progress/{batchId}', [MahasiswaController::class, 'checkProgress'])->name('mahasiswa.import-progress');
    Route::resource('mahasiswa', MahasiswaController::class)->except(['show']);
    Route::get('/mahasiswa/{id}', [MahasiswaController::class, 'show'])->name('mahasiswa.show');

    // Jenis Surat Management
    Route::resource('jenis-surat', JenisSuratController::class)->except(['show']);
    Route::get('kategori-surat', [KategoriSuratController::class, 'index'])->name('kategori-surat.index');
    Route::post('/jenis-surat/{jenisSurat}/template', [JenisSuratController::class, 'updateTemplate'])->name('jenis-surat.template');
    Route::resource('kategori-surat', KategoriSuratController::class);

    // Surat
    Route::get('/surat/{id}/pdf', [JenisSuratController::class, 'exportPdf'])->name('surat.pdf');
    Route::get('/surat/{id}/json', [JenisSuratController::class, 'getSuratJson'])->name('surat.json');

    Route::post('/upload-logo', [JenisSuratController::class, 'uploadLogo'])->name('admin.upload-logo');
    Route::post('/upload-kop', [JenisSuratController::class, 'uploadKop'])->name('admin.upload-kop');
});

// =============== PETUGAS ROUTES ===============
Route::middleware(['auth'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', [PetugasDashboardController::class, 'index'])->name('dashboard');
    Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');

    Route::post('/approval/{surat}/approve', [ApprovalController::class, 'approve'])->name('approval.approve');
    Route::post('/approval/{surat}/reject', [ApprovalController::class, 'reject'])->name('approval.reject');
    Route::get('/approval/{surat}/data', [ApprovalController::class, 'getSuratData'])->name('petugas.approval.data');

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

// =============== CKEDITOR IMAGE UPLOAD ROUTE ===============
Route::post('/upload-image', function (Request $request) {
    try {
        // Validasi file
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Proses upload
        $file = $request->file('upload');
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $file->getClientOriginalName());

        // Buat folder jika belum ada
        if (!file_exists(public_path('uploads/editor'))) {
            mkdir(public_path('uploads/editor'), 0777, true);
        }

        $file->move(public_path('uploads/editor'), $filename);
        $url = asset('uploads/editor/' . $filename);

        // Return response untuk CKEditor
        return response()->json([
            'uploaded' => true,
            'url' => $url
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'uploaded' => false,
            'error' => ['message' => $e->getMessage()]
        ]);
    }
})->name('upload.image')->middleware('auth');
