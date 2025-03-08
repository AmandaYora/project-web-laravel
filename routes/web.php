<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CpmController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('auth.login');
    Route::post('/login', 'authenticate')->name('auth.authenticate');
});

Route::middleware('check.session')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');

    // Dashboard (All Roles)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Users Management (Admin Only)
    Route::middleware('role:users')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users/save', [UserController::class, 'saveUser'])->name('users.save');
        Route::delete('/users/{id}', [UserController::class, 'deleteUser'])->name('users.delete');
    });

    // Projects Management (Admin and Manager)
    Route::middleware('role:projects')->group(function () {
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');
        Route::post('/projects/save', [ProjectController::class, 'saveProject'])->name('projects.save');
        Route::delete('/projects/{id}', [ProjectController::class, 'deleteProject'])->name('projects.delete');
    });

    // Tasks Management (All Roles)
    Route::middleware('role:tasks')->group(function () {
        Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::get('/tasks/project/{projectId}', [TaskController::class, 'index'])->name('tasks.project');
        Route::post('/tasks/save', [TaskController::class, 'saveTask'])->name('tasks.save');
        Route::delete('/tasks/{id}', [TaskController::class, 'deleteTask'])->name('tasks.delete');
        Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    });

    // Documents Management (All Roles)
    Route::middleware('role:documents')->group(function () {
        Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents/save', [DocumentController::class, 'saveDocument'])->name('documents.save');
        Route::delete('/documents/{id}', [DocumentController::class, 'deleteDocument'])->name('documents.delete');
        Route::get('/documents/{id}/download', [DocumentController::class, 'download'])->name('documents.download');
    });

    // CPM Management (Admin and Manager)
    Route::middleware('role:cpm')->group(function () {
        Route::get('/cpm/project/{projectId}', [CpmController::class, 'index'])->name('cpm.index');
        Route::post('/cpm/activity/save', [CpmController::class, 'saveActivity'])->name('cpm.saveActivity');
        Route::delete('/cpm/activity/{id}', [CpmController::class, 'deleteActivity'])->name('cpm.deleteActivity');
        Route::get('/cpm/project/{projectId}/critical-path', [CpmController::class, 'getCriticalPath'])->name('cpm.getCriticalPath');
    });

    // Reports Management (Admin and Manager)
    Route::middleware('role:reports')->group(function () {
        Route::get('/reports/project/{projectId}', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/project/{projectId}/generate', [ReportController::class, 'generateReport'])->name('reports.generate');
        Route::get('/reports/{id}/download', [ReportController::class, 'download'])->name('reports.download');
        Route::delete('/reports/{id}', [ReportController::class, 'delete'])->name('reports.delete');
    });
});
