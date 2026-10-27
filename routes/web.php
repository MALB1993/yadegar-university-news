<?php

use App\Http\Controllers\Owner\OwnerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();


Route::middleware(['auth', 'check-role-users:professor, employee'])->prefix('employee')->group(function(){

    Route::get('/', [OwnerController::class, 'index'])->name('ownder.index');

});

Route::middleware(['auth', 'check-role-users:student'])->prefix('student')->group(function(){

    Route::get('/', [App\Http\Controllers\Client\ClientController::class, 'index'])->name('client.index');

});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
