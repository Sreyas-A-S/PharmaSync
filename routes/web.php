<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UpdatesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'index']);
Route::get('/login', [AuthController::class, 'index']);
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:staff'])->group(function(){
    Route::get('/staff', [UpdatesController::class, 'index']);
});
