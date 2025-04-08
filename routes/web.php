<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

//*Authenticated Routes
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    //*Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    //*Admin Routes
    Route::prefix('admin')->middleware(['role:admin'])->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    });

    //*Supervisor Routes
    Route::prefix('supervisor')->middleware(['role:supervisor'])->name('supervisor.')->group(function () {
        Route::get('/dashboard', [SupervisorController::class, 'index'])->name('dashboard');
    });

    //*User Routes
    Route::prefix('user')->middleware(['role:user'])->name('user.')->group(function () {
        Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');
    });
});

require __DIR__.'/auth.php';
