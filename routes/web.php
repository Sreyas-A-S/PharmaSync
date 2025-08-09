<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UpdatesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'index']);
Route::get('/login', [AuthController::class, 'index']);
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:staff'])->group(function(){
    Route::get('/staff', [UpdatesController::class, 'index'])->name('staff');
    Route::get('/updates', [UpdatesController::class, 'updates'])->name('updates');
    Route::put('/updates/{update}', [UpdatesController::class, 'update'])->name('updates.update');
    Route::delete('/updates/{update}', [UpdatesController::class, 'destroy'])->name('updates.destroy');
    Route::post('/updates', [UpdatesController::class, 'store'])->name('updates.store');
});

Route::middleware(['auth', 'role:head'])->group( function() {
    Route::get('dashboard', [UpdatesController::class, 'dashboard'])->name('dashboard');
    Route::get('department-updates', [UpdatesController::class, 'departmentUpdates'])->name('department.updates');
});

Route::middleware(['auth', 'role:admin'])->group( function() {
    Route::get('admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // User Management
    Route::get('admin/users', [AdminController::class, 'getUsers'])->name('admin.users');
    Route::post('admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('admin/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');

    // Department Management
    Route::get('admin/departments', [AdminController::class, 'getDepartments'])->name('admin.departments');
    Route::post('admin/departments', [AdminController::class, 'storeDepartment'])->name('admin.departments.store');
    Route::put('admin/departments/{department}', [AdminController::class, 'updateDepartment'])->name('admin.departments.update');
    Route::delete('admin/departments/{department}', [AdminController::class, 'destroyDepartment'])->name('admin.departments.destroy');

    // Updates Management
    Route::get('admin/all-updates', [AdminController::class, 'getAllUpdates'])->name('admin.all-updates');
});