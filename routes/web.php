<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', function () {
    return view('auth.register');
});

Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('frontend.auth');

Route::get('/home', function () {
    return view('dashboard.home');
})->middleware('frontend.auth');

Route::get('/home/data', [DashboardController::class, 'index'])->middleware('frontend.auth');

Route::get('/scan', function () {
    return view('dashboard.scan');
})->middleware('frontend.auth');

Route::get('/invoice', function () {
    return view('dashboard.invoice');
})->middleware('frontend.auth');

Route::post('/invoice', [InvoiceController::class, 'store'])->middleware('frontend.auth');
Route::get('/vendors/options', [VendorController::class, 'index'])->middleware('frontend.auth');

Route::get('/history', function () {
    return view('dashboard.history');
})->middleware('frontend.auth');

Route::get('/history/data', [HistoryController::class, 'index'])->middleware('frontend.auth');
