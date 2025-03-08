<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ClassSiswaController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\ClassSessionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('auth.login');
    Route::post('/login', 'authenticate')->name('auth.authenticate');
});

Route::middleware('check.session')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::post('/save', [UserController::class, 'saveUser'])->name('users.save');
        Route::delete('/{id}', [UserController::class, 'deleteUser'])->name('users.delete');
    });

    Route::prefix('subjects')->group(function () {
        Route::get('/', [SubjectController::class, 'index'])->name('subjects.index');
        Route::post('/save', [SubjectController::class, 'saveSubject'])->name('subjects.save');
        Route::delete('/{id}', [SubjectController::class, 'deleteSubject'])->name('subjects.delete');
    });

    Route::prefix('classes')->group(function () {
        Route::get('/', [ClassSiswaController::class, 'index'])->name('classes.index');
        Route::post('/save', [ClassSiswaController::class, 'saveClass'])->name('classes.save');
        Route::delete('/{id}', [ClassSiswaController::class, 'deleteClass'])->name('classes.delete');
    });

    Route::prefix('jurusan')->group(function () {
        Route::get('/', [JurusanController::class, 'index'])->name('jurusan.index');
        Route::post('/save', [JurusanController::class, 'saveJurusan'])->name('jurusan.save');
        Route::delete('/{id}', [JurusanController::class, 'deleteJurusan'])->name('jurusan.delete');
    });

    Route::prefix('mapel')->group(function () {
        Route::get('/', [MapelController::class, 'index'])->name('mapel.index');
        Route::post('/save', [MapelController::class, 'saveMapel'])->name('mapel.save');
        Route::delete('/{id}', [MapelController::class, 'deleteMapel'])->name('mapel.delete');
    });

    Route::prefix('sessions')->group(function () {
        Route::get('/', [ClassSessionController::class, 'index'])->name('sessions.index');
        Route::post('/save', [ClassSessionController::class, 'saveSession'])->name('sessions.save');
        Route::delete('/{id}', [ClassSessionController::class, 'deleteSession'])->name('sessions.delete');
    });

    Route::prefix('attendance')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
        Route::post('/process', [AttendanceController::class, 'processAttendance'])->name('attendance.process');
        Route::post('/save', [AttendanceController::class, 'saveAttendance'])->name('attendance.save');
        Route::delete('/{id}', [AttendanceController::class, 'deleteAttendance'])->name('attendance.delete');
        Route::get('/print/{monthYear}', [AttendanceController::class, 'printByMonth'])->name('attendance.print');
    });

    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/guru', [GuruController::class, 'index'])->name('guru.index');
});
