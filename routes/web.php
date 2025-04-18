<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/products-list', [ProductController::class, 'list'])->name('product.list');

//* Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {

    //* Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    //* Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    //* Admin Routes
    Route::middleware(['role:admin'])->group(function () {

        Route::prefix('product')->name('product.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('edit');
            Route::post('/save', [ProductController::class, 'store'])->name('store');
            Route::put('/update', [ProductController::class, 'update'])->name('update');
            Route::delete('/delete/{product}', [ProductController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
            Route::post('/save', [UserController::class, 'store'])->name('store');
            Route::put('/update', [UserController::class, 'update'])->name('update');
            Route::delete('/delete/{product}', [UserController::class, 'destroy'])->name('destroy');
        });

        Route::post('/deposit/manual-callback/{depositId}', [DepositController::class, 'manualCallback'])->name('manual.callback');
    });

    //* Supervisor Routes
    Route::middleware(['role:supervisor'])->group(function () {
    });

    //* User Routes
    Route::middleware(['role:user'])->group(function () {
        Route::get('/products', [ProductController::class, 'all'])->name('product.all');
        Route::get('/products/{id}', [ProductController::class, 'show'])->name('product.show');
        Route::post('/products/{id}/buy', [ProductController::class, 'buy'])->name('product.buy');

        Route::get('/deposit/history', [DepositController::class, 'history'])->name('deposit.history');
        Route::get('/deposit/topup', [DepositController::class, 'create'])->name('deposit.create');
        Route::post('/deposit/save', [DepositController::class, 'store'])->name('deposit.store');
        Route::get('/deposit/show/{id}', [DepositController::class, 'show'])->name('deposit.show');

        Route::get('/transaction', [TransactionController::class, 'index'])->name('transactions.user');

        Route::post('/cart/add/{id}', [TransactionController::class, 'add'])->name('cart.add');
        Route::post('/cart/remove/{id}', [TransactionController::class, 'remove'])->name('cart.remove');
        Route::post('/cart/checkout', [TransactionController::class, 'checkout'])->name('cart.checkout');

    });

    Route::get('/deposit', [DepositController::class, 'index'])->name('deposit.index');
    Route::get('/deposit/detail/{id}', [DepositController::class, 'show'])->name('deposit.detail');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/return', [DepositController::class, 'return'])->name('return');
});

require __DIR__.'/auth.php';
