<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\InvoiceBarcodeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (session()->has('svs_auth.access_token')) {
        return redirect(match ((string) session('svs_auth.user.role', '')) {
            'PETUGAS_GUDANG' => '/scan',
            'VENDOR' => '/invoice-barcodes',
            'ADMIN', 'SUPERVISOR' => '/home',
            default => '/login',
        });
    }

    return redirect('/login');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', function () {
    return view('auth.register');
});

Route::post('/register', [AuthController::class, 'register']);
Route::get('/public/vendors', [AuthController::class, 'publicVendors']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('frontend.auth');
Route::post('/activity-log', [ActivityLogController::class, 'store'])->middleware('frontend.auth');

Route::get('/home', function () {
    return view('dashboard.home');
})->middleware('frontend.auth:ADMIN,SUPERVISOR');

Route::get('/home/data', [DashboardController::class, 'index'])->middleware('frontend.auth:ADMIN,SUPERVISOR');

Route::get('/scan', function () {
    return view('dashboard.scan');
})->middleware('frontend.auth:PETUGAS_GUDANG');
Route::post('/scan/data', [ScanController::class, 'scan'])->middleware('frontend.auth:PETUGAS_GUDANG');
Route::post('/scan/invoices/{invoice}/confirm', [ScanController::class, 'confirmScan'])->middleware('frontend.auth:PETUGAS_GUDANG');
Route::post('/scan/invoices/{invoice}/pending', [ScanController::class, 'markPending'])->middleware('frontend.auth:PETUGAS_GUDANG');
Route::post('/scan/invoices/{invoice}/complete', [ScanController::class, 'complete'])->middleware('frontend.auth:PETUGAS_GUDANG');

Route::get('/invoice', function () {
    return view('dashboard.invoice');
})->middleware('frontend.auth:ADMIN');
Route::get('/invoice-barcodes', function () {
    return view('dashboard.invoice-barcodes');
})->middleware('frontend.auth:VENDOR');
Route::get('/invoice-barcodes/data', [InvoiceBarcodeController::class, 'index'])->middleware('frontend.auth:VENDOR');
Route::post('/invoice-barcodes/accept/{id}', [InvoiceBarcodeController::class, 'accept'])->middleware('frontend.auth:VENDOR');
Route::post('/invoice-barcodes/reject/{id}', [InvoiceBarcodeController::class, 'reject'])->middleware('frontend.auth:VENDOR');

Route::post('/invoice', [InvoiceController::class, 'store'])->middleware('frontend.auth:ADMIN');
Route::get('/vendors/options', [VendorController::class, 'index'])->middleware('frontend.auth:ADMIN');

Route::get('/vendors', function () {
    return view('dashboard.vendor');
})->middleware('frontend.auth:ADMIN');
Route::get('/vendors/data', [VendorController::class, 'list'])->middleware('frontend.auth:ADMIN');
Route::post('/vendors', [VendorController::class, 'store'])->middleware('frontend.auth:ADMIN');
Route::put('/vendors/{id}', [VendorController::class, 'update'])->middleware('frontend.auth:ADMIN');
Route::delete('/vendors/{id}', [VendorController::class, 'destroy'])->middleware('frontend.auth:ADMIN');

Route::get('/users', [UserController::class, 'indexPage'])->middleware('frontend.auth:ADMIN');
Route::get('/users/data', [UserController::class, 'list'])->middleware('frontend.auth:ADMIN');
Route::post('/users', [UserController::class, 'store'])->middleware('frontend.auth:ADMIN');
Route::put('/users/{id}', [UserController::class, 'update'])->middleware('frontend.auth:ADMIN');
Route::get('/users/{id}/data', [UserController::class, 'details'])->middleware('frontend.auth:ADMIN');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->middleware('frontend.auth:ADMIN');

Route::get('/history', function () {
    return view('dashboard.history');
})->middleware('frontend.auth:ADMIN,SUPERVISOR');

Route::get('/history/data', [HistoryController::class, 'index'])->middleware('frontend.auth:ADMIN,SUPERVISOR');
Route::get('/audit-logs', function () {
    return view('dashboard.audit-logs');
})->middleware('frontend.auth:ADMIN,SUPERVISOR');
Route::get('/audit-logs/data', [AuditLogController::class, 'index'])->middleware('frontend.auth:ADMIN,SUPERVISOR');
Route::get('/audit-logs/users', [AuditLogController::class, 'searchUsers'])->middleware('frontend.auth:ADMIN,SUPERVISOR');
