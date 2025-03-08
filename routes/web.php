<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AtmController;
use Illuminate\Support\Facades\Route;


Route::redirect('/', '/login');

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

    Route::get('/atms', [AtmController::class, 'index'])->name('atms.index');
    Route::post('/atms/save', [AtmController::class, 'saveAtm'])->name('atms.save');
    Route::delete('/atms/{id}', [AtmController::class, 'deleteAtm'])->name('atms.destroy');

    /* Routes for checks generated automatically */
    Route::get('/checks', [App\Http\Controllers\CheckController::class, 'index'])->name('checks.index');
    Route::post('/checks/save', [App\Http\Controllers\CheckController::class, 'saveCheck'])->name('checks.save');
    Route::delete('/checks/{id}', [App\Http\Controllers\CheckController::class, 'deleteCheck'])->name('checks.destroy');


    /* Routes for tasks generated automatically */
    Route::get('/tasks', [App\Http\Controllers\TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks/save', [App\Http\Controllers\TaskController::class, 'saveTask'])->name('tasks.save');
    Route::delete('/tasks/{id}', [App\Http\Controllers\TaskController::class, 'deleteTask'])->name('tasks.destroy');


    /* Routes for activities generated automatically */
    Route::get('/activities', [App\Http\Controllers\ActivityController::class, 'index'])->name('activities.index');
    Route::post('/activities/save', [App\Http\Controllers\ActivityController::class, 'saveActivity'])->name('activities.save');
    Route::delete('/activities/{id}', [App\Http\Controllers\ActivityController::class, 'deleteActivity'])->name('activities.destroy');
});

