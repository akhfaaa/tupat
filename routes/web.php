<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\RombelController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\MataPelajaranController;
// use App\Http\Controllers\JadwalPelajaranController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\PklController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\RaporSiswaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\AbsensiSiswaController;
use App\Http\Controllers\MitraDudiController;
use App\Http\Controllers\PklWorkflowController;
use App\Http\Controllers\ExtendedModuleController;
use App\Http\Controllers\ParentDashboardController;
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
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

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
        Route::resource('jadwal', JadwalController::class);
        Route::resource('pkl', PklController::class);
        Route::resource('mitra-dudi', MitraDudiController::class)->except(['create', 'show', 'edit']);
    });

    // Grup Rute Khusus Guru & Admin
    Route::middleware(['role:super-admin|guru'])->group(function () {
        Route::get('penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
        Route::post('penilaian', [PenilaianController::class, 'store'])->name('penilaian.store');

        Route::get('jurnal', [JurnalController::class, 'index'])->name('jurnal.index');
        Route::post('jurnal', [JurnalController::class, 'store'])->name('jurnal.store');
    });

    // Grup Rute Khusus Siswa
    Route::middleware(['role:siswa'])->group(function () {
        Route::get('/rapor-ku', [RaporSiswaController::class, 'index'])->name('siswa.rapor');
        Route::get('/rapor-ku/cetak', [RaporSiswaController::class, 'cetakPdf'])->name('siswa.rapor.cetak');

        Route::get('/absensi-ku', [AbsensiSiswaController::class, 'index'])->name('siswa.absensi.index');
        Route::get('/pkl-ku', [PklWorkflowController::class, 'studentIndex'])->name('siswa.pkl.index');
        Route::post('/pkl/{pkl}/logbook', [PklWorkflowController::class, 'storeLogbook'])->name('siswa.pkl.logbook.store');
        Route::post('/pkl/{pkl}/attendance', [ExtendedModuleController::class, 'storePklAttendance'])->name('pkl.attendance.store');
    });

    Route::middleware(['role:orang-tua'])->group(function () {
        Route::get('/pantauan-anak', [ParentDashboardController::class, 'index'])->name('parent.dashboard');
    });

    Route::middleware(['role:super-admin|hubin|mentor-industri'])->group(function () {
        Route::patch('/pkl-logbook/{logbook}/verify', [PklWorkflowController::class, 'verifyLogbook'])->name('pkl.logbook.verify');
        Route::post('/pkl/{pkl}/assessment', [PklWorkflowController::class, 'assess'])->name('pkl.assessment.store');
        Route::post('/ukk/assessments', [ExtendedModuleController::class, 'storeUkkAssessment'])->name('ukk.assessments.store');
        Route::post('/tracer-study', [ExtendedModuleController::class, 'storeTracerStudy'])->name('tracer-study.store');
        Route::post('/ukk/schemes', [ExtendedModuleController::class, 'storeUkkScheme'])->name('ukk.schemes.store');
        Route::post('/job-postings', [ExtendedModuleController::class, 'storeJobPosting'])->name('job-postings.store');
    });

    Route::middleware(['role:super-admin|guru|guru-bk|wali-kelas'])->group(function () {
        Route::post('/discipline-records', [ExtendedModuleController::class, 'storeDisciplineRecord'])->name('discipline-records.store');
        Route::post('/report-components', [ExtendedModuleController::class, 'storeReportComponent'])->name('report-components.store');
    });

    Route::middleware(['role:super-admin|tu|guru'])->group(function () {
        Route::post('/inventory-loans', [ExtendedModuleController::class, 'storeInventoryLoan'])->name('inventory-loans.store');
        Route::patch('/inventory-loans/{loan}/return', [ExtendedModuleController::class, 'returnInventoryLoan'])->name('inventory-loans.return');
    });

    Route::middleware(['role:super-admin|tu'])->group(function () {
        Route::post('/inventory-items', [ExtendedModuleController::class, 'storeInventoryItem'])->name('inventory-items.store');
        Route::get('/dapodik/export/siswa', [ExtendedModuleController::class, 'exportDapodik'])->name('dapodik.export.siswa');
        Route::post('/dapodik/import/siswa', [ExtendedModuleController::class, 'importDapodik'])->name('dapodik.import.siswa');
    });

    Route::get('/modul-smk', [ExtendedModuleController::class, 'dashboard'])
        ->middleware('role:super-admin|tu|guru|guru-bk|wali-kelas|hubin|mentor-industri|bkk')
        ->name('modules.smk');
});

require __DIR__ . '/auth.php';
