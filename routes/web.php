<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LowStockController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('items', ItemController::class)->except('show');
    Route::resource('suppliers', SupplierController::class)->except('show');

    Route::resource('stock-ins', StockInController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::resource('stock-outs', StockOutController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::resource('stock-opnames', StockOpnameController::class)->only(['index', 'create', 'store']);

    Route::get('/low-stock', LowStockController::class)->name('low-stock');

    Route::get('/reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
    Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
});
