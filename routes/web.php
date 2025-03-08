<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('auth.login');
    Route::post('/login', 'authenticate')->name('auth.authenticate');
});

Route::middleware('check.session')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::post('/save', [UserController::class, 'saveUser'])->name('users.save');
        Route::delete('/{id}', [UserController::class, 'deleteUser'])->name('users.delete');
    });
});