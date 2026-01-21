<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JenisPelatihanController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\JadwalPelatihanController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\KehadiranController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

// PUBLIC ROUTES
Route::get('/', function () {
    return redirect('/login');
});

// AUTHENTICATION ROUTES
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// PROTECTED ROUTES - ADMIN & SUPERVISOR
Route::middleware(['auth', 'role:admin,supervisor'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::prefix('admin')->group(function () {
        // Kehadiran routes (Admin & Supervisor)
        Route::get('/kehadiran', [KehadiranController::class, 'index'])->name('kehadiran.index');
        Route::get('/kehadiran/{id}', [KehadiranController::class, 'show'])->name('kehadiran.show');
        Route::put('/kehadiran/{id}/{id_karyawan}', [KehadiranController::class, 'updateStatus'])->name('kehadiran.updateStatus');

        // Jadwal Pelatihan Management (Admin only)
        Route::middleware('role:admin')->group(function () {
            Route::get('/penjadwalan', [JadwalPelatihanController::class, 'index'])->name('penjadwalan.index');
            Route::get('/penjadwalan/create', [JadwalPelatihanController::class, 'create'])->name('penjadwalan.create');
            Route::post('/penjadwalan', [JadwalPelatihanController::class, 'store'])->name('penjadwalan.store');
            Route::get('/penjadwalan/{id}/edit', [JadwalPelatihanController::class, 'edit'])->name('penjadwalan.edit');
            Route::put('/penjadwalan/{id}', [JadwalPelatihanController::class, 'update'])->name('penjadwalan.update');
            Route::delete('/penjadwalan/{id}', [JadwalPelatihanController::class, 'destroy'])->name('penjadwalan.destroy');
            Route::get('/penjadwalan/{id}', [JadwalPelatihanController::class, 'show'])->name('penjadwalan.show');
            Route::post('/penjadwalan/{id}/generate-presensi', [JadwalPelatihanController::class, 'generatePresensi'])->name('penjadwalan.generate-presensi');
            Route::post('/penjadwalan/{id}/extend-presensi', [JadwalPelatihanController::class, 'extendPresensi'])->name('penjadwalan.extend-presensi');
        });
        
        // Jenis Pelatihan Management
        Route::get('/jenispelatihan', [JenisPelatihanController::class, 'index'])->name('jenis-pelatihan.index');
        Route::post('/jenispelatihan/store', [JenisPelatihanController::class, 'store'])->name('jenis-pelatihan.store');
        Route::post('/jenispelatihan/{jenisPelatihan}/update', [JenisPelatihanController::class, 'update'])->name('jenis-pelatihan.update');
        Route::post('/jenispelatihan/{jenisPelatihan}/delete', [JenisPelatihanController::class, 'destroy'])->name('jenis-pelatihan.destroy');

        // Data Karyawan Management (Admin only)
        Route::middleware('role:admin')->group(function () {
            Route::get('/datakaryawan', [KaryawanController::class, 'index'])->name('karyawan.index');
            Route::post('/datakaryawan/store', [KaryawanController::class, 'store'])->name('karyawan.store');
            Route::post('/datakaryawan/import-excel', [KaryawanController::class, 'importExcel'])->name('karyawan.import-excel');
            Route::put('/datakaryawan/{id}/update', [KaryawanController::class, 'update'])->name('karyawan.update');
            Route::delete('/datakaryawan/{id}/delete', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');
        });

        // Supervisor Management (Admin only)
        Route::middleware('role:admin')->group(function () {
            Route::get('/kelolasupervisor', [SupervisorController::class, 'index'])->name('supervisor.index');
            Route::post('/kelolasupervisor/store', [SupervisorController::class, 'store'])->name('supervisor.store');
            Route::get('/kelolasupervisor/{id}', [SupervisorController::class, 'show'])->name('supervisor.show');
            Route::put('/kelolasupervisor/{id}/update', [SupervisorController::class, 'update'])->name('supervisor.update');
            Route::delete('/kelolasupervisor/{id}/delete', [SupervisorController::class, 'destroy'])->name('supervisor.destroy');
            
            // Bagian alias route
            Route::get('/bagian', [SupervisorController::class, 'index'])->name('bagian.index');
        });

        // Admin Management (Admin only)
        Route::middleware('role:admin')->group(function () {
            Route::get('/kelolaadmin', [AdminController::class, 'index'])->name('admin.index');
            Route::post('/kelolaadmin/store', [AdminController::class, 'store'])->name('admin.store');
            Route::put('/kelolaadmin/{email}/update', [AdminController::class, 'update'])->name('admin.update');
            Route::delete('/kelolaadmin/{email}/delete', [AdminController::class, 'destroy'])->name('admin.destroy');
        });
    });
});

// API ENDPOINTS
Route::get('/api/bagians', function() {
    return response()->json(\App\Models\Bagian::all());
});

Route::get('/api/jadwal/{id_jadwal}', [PresensiController::class, 'getJadwal']);
Route::post('/api/presensi', [PresensiController::class, 'submitPresensi']);



// PUBLIC ATTENDANCE ROUTES (No Auth Required)
// TODO: Add attendance page routes here
Route::get('/presensi/{id_jadwal}/{token}', function ($id_jadwal, $token) {
    return view('presensi.index', compact('id_jadwal', 'token'));
})->name('presensi.index');

