<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\welcomecontroller;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/welcome', [welcomecontroller::class, 'index'])->name('welcome');
Route::get('/pemasukan', [PemasukanController::class, 'index'])->name('pemasukan');
Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
Route::get('/recycle', [App\Http\Controllers\RecycleController::class, 'index'])->name('recycle');
Route::post('/recycle/restore/{type}/{id}', [App\Http\Controllers\RecycleController::class, 'restore'])->name('recycle.restore');
Route::post('/recycle/delete/{type}/{id}', [App\Http\Controllers\RecycleController::class, 'forceDelete'])->name('recycle.forceDelete');
Route::post('/recycle/restore-all', [App\Http\Controllers\RecycleController::class, 'restoreAll'])->name('recycle.restoreAll');
Route::post('/recycle/empty-trash', [App\Http\Controllers\RecycleController::class, 'emptyTrash'])->name('recycle.emptyTrash');