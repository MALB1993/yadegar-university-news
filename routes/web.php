<?php

use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Owner\OwnerController;
use App\Http\Controllers\PostController;
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

Route::middleware('auth')->group(function () {
    Route::resource('news', PostController::class)->parameters(['news' => 'post']);

    Route::resource('categories', CategoryController::class)
        ->except(['create', 'show'])
        ->middleware('check-role-users:employee');

    Route::post('news/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});
