<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\ReceiptAnalysisController;
use App\Http\Controllers\StrukController;
use App\Http\Controllers\RecycleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\welcomecontroller;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login')->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('session.auth')->name('logout');

// Authenticated users: dashboard, reports, receipts (read-only), and profile.
Route::middleware('session.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chartData');
    Route::get('/dashboard/live-data', [DashboardController::class, 'liveData'])->name('dashboard.liveData');
    Route::get('/welcome', [welcomecontroller::class, 'index'])->name('welcome');

    Route::get('/struk', [StrukController::class, 'index'])->name('struk');
    Route::get('/struk/download/{type}/{id}', [StrukController::class, 'download'])->name('struk.download');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('/laporan/live-data', [LaporanController::class, 'liveData'])->name('laporan.liveData');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

// Admin-only operations. Kepala Lab is intentionally excluded here.
Route::middleware(['session.auth', 'role.admin'])->group(function () {
    // Pemasukan - Pilih Manual vs Otomatis
    Route::get('/pemasukan', [PemasukanController::class, 'pilih'])->name('pemasukan.pilih');
    Route::get('/pemasukan/manual', [PemasukanController::class, 'showManual'])->name('pemasukan.manual');
    Route::get('/pemasukan/otomatis', [PemasukanController::class, 'showOtomatis'])->name('pemasukan.otomatis');
    Route::post('/pemasukan/store', [PemasukanController::class, 'store'])->name('pemasukan.store');
    Route::post('/pemasukan/store-manual', [PemasukanController::class, 'storeManual'])->name('pemasukan.store-manual');
    Route::post('/pemasukan/store-otomatis', [PemasukanController::class, 'storeOtomatis'])->name('pemasukan.store-otomatis');
    Route::get('/pemasukan/edit/{id}', [PemasukanController::class, 'edit'])->name('pemasukan.edit');
    Route::post('/pemasukan/update/{id}', [PemasukanController::class, 'update'])->name('pemasukan.update');
    Route::post('/pemasukan/delete/{id}', [PemasukanController::class, 'destroy'])->name('pemasukan.delete');

    Route::post('/receipt/parse', [ReceiptAnalysisController::class, 'parse'])->name('receipt.parse');

    // Pengeluaran - Pilih Manual vs Otomatis
    Route::get('/pengeluaran', [welcomecontroller::class, 'pilih'])->name('pengeluaran.pilih');
    Route::get('/pengeluaran/manual', [welcomecontroller::class, 'showManual'])->name('pengeluaran.manual');
    Route::get('/pengeluaran/otomatis', [welcomecontroller::class, 'showOtomatis'])->name('pengeluaran.otomatis');
    Route::post('/pengeluaran/store', [welcomecontroller::class, 'store'])->name('pengeluaran.store');
    Route::post('/pengeluaran/store-manual', [welcomecontroller::class, 'storeManual'])->name('pengeluaran.store-manual');
    Route::post('/pengeluaran/store-otomatis', [welcomecontroller::class, 'storeOtomatis'])->name('pengeluaran.store-otomatis');
    Route::get('/pengeluaran/edit/{id}', [welcomecontroller::class, 'edit'])->name('pengeluaran.edit');
    Route::post('/pengeluaran/update/{id}', [welcomecontroller::class, 'update'])->name('pengeluaran.update');
    Route::post('/pengeluaran/delete/{id}', [welcomecontroller::class, 'destroy'])->name('pengeluaran.delete');

    Route::post('/struk/delete/{type}/{id}', [StrukController::class, 'destroy'])->name('struk.delete');
    Route::post('/struk/update-foto/{type}/{id}', [StrukController::class, 'updateFoto'])->name('struk.updateFoto');

    Route::get('/recycle', [RecycleController::class, 'index'])->name('recycle');
    Route::post('/recycle/restore/{type}/{id}', [RecycleController::class, 'restore'])->name('recycle.restore');
    Route::post('/recycle/delete/{type}/{id}', [RecycleController::class, 'forceDelete'])->name('recycle.forceDelete');
    Route::post('/recycle/restore-all', [RecycleController::class, 'restoreAll'])->name('recycle.restoreAll');
    Route::post('/recycle/empty-trash', [RecycleController::class, 'emptyTrash'])->name('recycle.emptyTrash');
});
