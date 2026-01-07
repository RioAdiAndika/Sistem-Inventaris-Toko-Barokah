<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ExpiredController;
use App\Http\Controllers\DashboardGudangController;

/*
| AUTH
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
| ROOT
*/
Route::get('/', function () {
    return redirect('/login');
});

/*
| ADMIN
*/
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/dashboard-admin', [DashboardController::class, 'admin'])
        ->name('dashboard.admin');
    Route::resource('products', ProductController::class);
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('laporan.exportCsv');
    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.exportPdf');
});

/*
| GUDANG
*/
Route::middleware(['auth', 'role:Gudang'])->group(function () {
    Route::get('/dashboard-gudang', [DashboardGudangController::class, 'index'])
        ->name('dashboard.gudang');
});



/*
| ADMIN & GUDANG
*/
Route::middleware(['auth', 'role:Admin|Gudang'])->group(function () {
    Route::get('/expired/hampir', [ExpiredController::class, 'hampir'])
        ->name('expired.hampir');
    Route::get('/expired/sudah', [ExpiredController::class, 'sudah'])
        ->name('expired.sudah');
    Route::resource('barang-masuk', BarangMasukController::class)
        ->only(['index', 'create', 'store']);
    Route::resource('barang-keluar', BarangKeluarController::class)
        ->only(['index', 'create', 'store']);
    Route::get('/katalog', [ProductController::class, 'katalog'])->name('products.katalog');
    Route::get('products/{id}/expired', [ProductController::class, 'expired'])->name('products.expired');
});
