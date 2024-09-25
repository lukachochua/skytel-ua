<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserInfoController;

// Login routes
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('auth.login');
})->name('login');

// Google authentication routes
Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

// Facebook authentication routes
Route::get('auth/facebook', [LoginController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('auth/facebook/callback', [LoginController::class, 'handleFacebookCallback']);

// Dashboard route
Route::get('/dashboard', function () {
    $user = Auth::user();

    if (!$user->is_info_provided) {
        return redirect()->route('user.info.form');
    }

    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Logout route
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');

// User info form route
Route::get('/user-info', [UserInfoController::class, 'showForm'])
    ->name('user.info.form')
    ->middleware('auth');

// Submit user info form route
Route::post('/user-info', [UserInfoController::class, 'submitForm'])
    ->name('user.info.submit')
    ->middleware('auth');
