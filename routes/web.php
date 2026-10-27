<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Gregwar\Captcha\CaptchaBuilder;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/captcha', function () {
    
    $builder = new CaptchaBuilder();
    $builder->setPhraseBuilder(new Gregwar\Captcha\PhraseBuilder(5, '0123456789'));
    $builder->build();

    
    session(['captcha_phrase' => $builder->getPhrase()]);

    
    header('Content-Type: image/jpeg');
    $builder->output();
})->name('captcha');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
