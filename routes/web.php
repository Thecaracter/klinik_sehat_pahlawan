<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\ObatMasukController;
use App\Http\Controllers\PemeriksaanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth-login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// group middleware
Route::middleware('isLogin')->group(function () {
    //dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //user routes
    Route::get('/users', [UserController::class, 'index'])->name('user.index');
    Route::post('/users', [UserController::class, 'store'])->name('user.store');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('user.destroy');

    //pasiens routes
    Route::get('/pasien', [PasienController::class, 'index'])->name('pasiens.index');
    Route::post('/pasien', [PasienController::class, 'store'])->name('pasiens.store');
    Route::put('/pasien/{nik}', [PasienController::class, 'update'])->name('pasiens.update');
    Route::delete('/pasien/{nik}', [PasienController::class, 'destroy'])->name('pasiens.destroy');

    //obat routes
    Route::get('/obat', [ObatController::class, 'index'])->name('obat.index');
    Route::post('/obat', [ObatController::class, 'store'])->name('obat.store');
    Route::put('/obat/{id}', [ObatController::class, 'update'])->name('obat.update');
    Route::delete('/obat/{id}', [ObatController::class, 'destroy'])->name('obat.destroy');

    //obat masuk routes    
    Route::get('/obatmasuk', [ObatMasukController::class, 'index'])->name('obatmasuk.index');
    Route::post('/obatmasuk', [ObatMasukController::class, 'store'])->name('obatmasuk.store');
    Route::put('/obatmasuk/{obatmasuk}', [ObatMasukController::class, 'update'])->name('obatmasuk.update');
    Route::delete('/obatmasuk/{obatmasuk}', [ObatMasukController::class, 'destroy'])->name('obatmasuk.destroy');

    //kunjungan routes
    Route::get('/kunjungan', [KunjunganController::class, 'index'])->name('kunjungan.index');
    Route::post('/kunjungan', [KunjunganController::class, 'store'])->name('kunjungan.store');
    Route::put('/kunjungan/{kunjungan}', [KunjunganController::class, 'update'])->name('kunjungan.update');
    Route::delete('/kunjungan/{kunjungan}', [KunjunganController::class, 'destroy'])->name('kunjungan.destroy');
    Route::post('/kunjungan/check-nik', [KunjunganController::class, 'checkNik'])->name('kunjungan.checkNik');
    Route::delete('/kunjungan/foto/{foto}', [KunjunganController::class, 'deleteFoto'])->name('kunjungan.deleteFoto');
    Route::post('/kunjungan/{kunjungan}/add-photo', [KunjunganController::class, 'addPhoto'])->name('kunjungan.addPhoto');

    // Pemeriksaan Routes
    Route::get('/pemeriksaan', [PemeriksaanController::class, 'index'])->name('pemeriksaan.index');
    Route::put('/pemeriksaan/{kunjungan}', [PemeriksaanController::class, 'update'])->name('pemeriksaan.update');
    Route::post('/pemeriksaan/{kunjungan}/upload-foto', [PemeriksaanController::class, 'uploadFoto'])->name('pemeriksaan.uploadFoto');
    Route::delete('/pemeriksaan/foto/{foto}', [PemeriksaanController::class, 'deleteFoto'])->name('pemeriksaan.deleteFoto');
    Route::delete('/pemeriksaan/obat/{detailKunjungan}', [PemeriksaanController::class, 'deleteObat'])->name('pemeriksaan.deleteObat');

});

