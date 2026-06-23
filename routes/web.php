<?php

use App\Http\Controllers\welcomecontroller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PemasukanController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pemasukan', [PemasukanController::class, 'index'])->name('pemasukan');
Route::get('/', [welcomecontroller::class, 'index'])->name('welcome');
