<?php

use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Owner\OwnerController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth', 'check-role-users:professor,employee'])->prefix('employee')->group(function () {
    Route::get('/', [OwnerController::class, 'index'])->name('owner.index');
});

Route::middleware(['auth', 'check-role-users:student'])->prefix('student')->group(function () {
    Route::get('/', [ClientController::class, 'index'])->name('client.index');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');
