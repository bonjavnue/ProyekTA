<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JenisPelatihanController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// require __DIR__.'/auth.php';

Route::get('/jenispelatihan', [JenisPelatihanController::class, 'index']);

Route::post('/jenispelatihan/store', [JenisPelatihanController::class, 'store']);

Route::post('/jenispelatihan/{jenisPelatihan}/update', [JenisPelatihanController::class, 'update']);

Route::post('/jenispelatihan/{jenisPelatihan}/delete', [JenisPelatihanController::class, 'destroy']);