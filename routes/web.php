<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\RombelController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\JadwalPelajaranController;
use App\Http\Controllers\JadwalController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Pastikan rute dashboard ini TIDAK TERHAPUS
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Grup Rute yang butuh Login
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Grup Rute Khusus Master Data (Super Admin & TU)
    Route::middleware(['role:super-admin|tu'])->group(function () {
        Route::resource('jurusan', JurusanController::class);
        Route::resource('tahun-ajaran', TahunAjaranController::class);
        Route::resource('guru', GuruController::class);
        Route::resource('rombel', RombelController::class);
        Route::resource('siswa', SiswaController::class);
        Route::resource('mata-pelajaran', MataPelajaranController::class);
        Route::resource('jadwal-pelajaran', JadwalPelajaranController::class);
        Route::resource('jadwal', JadwalController::class);
    });
});

require __DIR__ . '/auth.php';
