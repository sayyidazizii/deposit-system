<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DuitkuController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/duitku/callback', [DuitkuController::class, 'callback'])->name('duitku.callback');
Route::get('/duitku/methods', [DuitkuController::class, 'getPaymentMethods'])->name('duitku.payment.methods');
