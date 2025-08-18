<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReadingListController;
use Illuminate\Support\Facades\Route;

Route::name('auth.')->group(function () {
    Route::middleware(['guest'])->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])
            ->name('login');

        Route::post('/login', [AuthController::class, 'login'])
            ->name('login');

        Route::get('/register', [AuthController::class, 'showRegisterForm'])
            ->name('register');

        Route::post('/register', [AuthController::class, 'register'])
            ->name('register');
    });

    Route::middleware(['auth'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('logout');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', [ReadingListController::class, 'index'])
        ->name('index');
});
