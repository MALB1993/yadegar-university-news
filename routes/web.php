<?php

use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentModerationController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Owner\OwnerController;
use App\Http\Controllers\Owner\ProfessorStatsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth', 'check-role-users:professor,employee'])->prefix('employee')->group(function () {
    Route::get('/', [OwnerController::class, 'index'])->name('owner.index');
    Route::get('my-news', [ProfessorStatsController::class, 'index'])->name('owner.my-news');
});

Route::middleware(['auth', 'check-role-users:employee'])->prefix('employee')->group(function () {
    Route::get('comments', [CommentModerationController::class, 'index'])->name('moderation.comments');
});

Route::middleware(['auth', 'check-role-users:student'])->prefix('student')->group(function () {
    Route::get('/', [ClientController::class, 'index'])->name('client.index');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');

    Route::resource('news', PostController::class)->parameters(['news' => 'post']);
    Route::post('news/{post}/bookmark', [BookmarkController::class, 'toggle'])->name('news.bookmark');
    Route::post('news/{post}/duplicate', [PostController::class, 'duplicate'])->name('news.duplicate');
    Route::post('news/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('comments/{comment}/like', [CommentController::class, 'like'])->name('comments.like');
    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('categories/{category}/follow', [FollowController::class, 'toggle'])->name('categories.follow');

    Route::resource('categories', CategoryController::class)
        ->except(['create', 'show'])
        ->middleware('check-role-users:employee');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('check-role-users:employee')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::put('{user}', [UserController::class, 'update'])->name('update');
    });
});
