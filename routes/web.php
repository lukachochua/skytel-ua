<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserInfoController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');


// Form to collect additional information
Route::get('/user-info', [UserInfoController::class, 'showForm'])->name('user.info.form')->middleware('auth');
Route::post('/user-info', [UserInfoController::class, 'submitForm'])->name('user.info.submit');
