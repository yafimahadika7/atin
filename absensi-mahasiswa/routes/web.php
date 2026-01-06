<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\LokasiUnpamController;

use App\Http\Controllers\Mahasiswa\MahasiswaAbsensiController;
use App\Http\Controllers\Mahasiswa\MahasiswaRiwayatController;

use App\Http\Controllers\Admin\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Dashboard Redirect (BERDASARKAN ROLE)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($user->role === 'dosen') {
        return redirect()->route('dosen.dashboard');
    }

    return redirect()->route('mahasiswa.dashboard');
})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // MAHASISWA CRUD
        Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');
        Route::post('/mahasiswa', [MahasiswaController::class, 'store'])->name('mahasiswa.store');
        Route::put('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'update'])->name('mahasiswa.update');
        Route::delete('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'destroy'])->name('mahasiswa.destroy');

        // ABSENSI (rekap)
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');

        // LOKASI UNPAM
        Route::get('/lokasi', [LokasiUnpamController::class, 'index'])->name('lokasi.index');
        Route::post('/lokasi', [LokasiUnpamController::class, 'store'])->name('lokasi.store');
        Route::put('/lokasi/{lokasi}', [LokasiUnpamController::class, 'update'])->name('lokasi.update');
        Route::post('/lokasi/{lokasi}/active', [LokasiUnpamController::class, 'setActive'])->name('lokasi.active');
        Route::delete('/lokasi/{lokasi}', [LokasiUnpamController::class, 'destroy'])->name('lokasi.destroy');
    });

/*
|--------------------------------------------------------------------------
| DOSEN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:dosen'])
    ->prefix('dosen')
    ->name('dosen.')
    ->group(function () {

        Route::view('/dashboard', 'dosen.dashboard')->name('dashboard');
    });

/*
|--------------------------------------------------------------------------
| MAHASISWA ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:mahasiswa'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {

        // Dashboard (modal kamera IN/OUT)
        Route::get('/dashboard', [MahasiswaAbsensiController::class, 'dashboard'])->name('dashboard');

        // endpoint absen (fetch)
        Route::post('/absen-in',  [MahasiswaAbsensiController::class, 'storeIn'])->name('absen.in.store');
        Route::post('/absen-out', [MahasiswaAbsensiController::class, 'storeOut'])->name('absen.out.store');

        // RIWAYAT ABSENSI
        Route::get('/riwayat', [MahasiswaRiwayatController::class, 'index'])->name('riwayat');

    });

/*
|--------------------------------------------------------------------------
| PROFILE (BREEZE DEFAULT)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';