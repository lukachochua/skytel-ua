<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserInfoController;
use App\Http\Middleware\CheckUserInfoProvided;

// Login routes
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('auth.login');
})->name('login');

Route::post('/', [LoginController::class, 'login'])->name('login.submit');

// Google authentication routes
Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

// Facebook authentication routes
Route::get('auth/facebook', [LoginController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('auth/facebook/callback', [LoginController::class, 'handleFacebookCallback']);

// Logout route
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');

// Register routes
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Password Reset routes via e-mail
Route::get('password/reset', [LoginController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [LoginController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [LoginController::class, 'reset'])->name('password.update');

// Change Password routes
Route::middleware('auth', CheckUserInfoProvided::class)->group(function () {
    Route::get('password/change', [RegisterController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('password/change/update', [RegisterController::class, 'changePassword'])->name('password.change.update');
});

// Dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', CheckUserInfoProvided::class])->name('dashboard');

// User Profile Routes
Route::middleware(['auth', CheckUserInfoProvided::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
});

// User info form route
Route::get('/user-info', [UserInfoController::class, 'showForm'])
    ->name('user.info.form')
    ->middleware('auth');

// Submit user info form route
Route::post('/user-info', [UserInfoController::class, 'submitForm'])
    ->name('user.info.submit')
    ->middleware('auth');
