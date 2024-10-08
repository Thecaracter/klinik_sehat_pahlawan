<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ObatMasukController;

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

});

