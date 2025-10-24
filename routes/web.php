<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BottleController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
| These routes are loaded by the RouteServiceProvider within a group
| which contains the "web" middleware group.
|
*/

// 🏠 Default Home Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// 🔐 Authentication Routes (Login, Register, Forgot Password, etc.)
Auth::routes();

// 🔒 Protected Routes (Require Login)
Route::middleware(['auth'])->group(function () {

    // 🧭 Dashboard / Home
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // 👥 User & Role Management
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);

    // 🛒 Product Management
    Route::resource('products', ProductController::class);

    // 🗂️ Category Management
    Route::resource('categories', CategoryController::class);

    // 🍾 Bottle Management
    Route::resource('bottles', BottleController::class);

    // 📏 Size Management
    Route::resource('sizes', SizeController::class);
    Route::resource('stocks', StockController::class);
    Route::resource('sells', SellController::class);
    // 📊 Reporting Routes
    Route::prefix('reports')->middleware('auth')->group(function () {
        // 📅 Day-wise Report
        Route::get('/daywise', [ReportController::class, 'daywise'])->name('reports.daywise');
        Route::get('/daywise/export/pdf', [ReportController::class, 'exportDaywisePDF'])->name('reports.daywise.export.pdf');
        Route::get('/daywise/export/excel', [ReportController::class, 'exportDaywiseExcel'])->name('reports.daywise.export.excel');

        // 🗓️ Monthly Report
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
        Route::get('/monthly/export/pdf', [ReportController::class, 'exportMonthlyPDF'])->name('reports.monthly.export.pdf');
        Route::get('/monthly/export/excel', [ReportController::class, 'exportMonthlyExcel'])->name('reports.monthly.export.excel');

        // 📊 Product-wise Report
        Route::get('/productwise', [ReportController::class, 'productwise'])->name('reports.productwise');
        Route::get('/productwise/export/pdf', [ReportController::class, 'exportProductwisePDF'])->name('reports.productwise.export.pdf');
        Route::get('/productwise/export/excel', [ReportController::class, 'exportProductwiseExcel'])->name('reports.productwise.export.excel');

        // 📦 Current Stock Summary
        Route::get('/stocksummary', [ReportController::class, 'stocksummary'])->name('reports.stocksummary');
        Route::get('/stocksummary/export/pdf', [ReportController::class, 'exportStockPDF'])->name('reports.stocksummary.export.pdf');
        Route::get('/stocksummary/export/excel', [ReportController::class, 'exportStockExcel'])->name('reports.stocksummary.export.excel');

        // ➕ Stock Added Report
        Route::get('/stockadded', [ReportController::class, 'stockadded'])->name('reports.stockadded');
        Route::get('/stockadded/export/pdf', [ReportController::class, 'exportStockaddedPDF'])->name('reports.stockadded.export.pdf');
        Route::get('/stockadded/export/excel', [ReportController::class, 'exportStockaddedExcel'])->name('reports.stockadded.export.excel');

        // 🍾 Bottle Return Report
        Route::get('/bottles', [ReportController::class, 'bottleReport'])->name('reports.bottles');
        Route::get('/bottles/export/pdf', [ReportController::class, 'exportBottlePDF'])->name('reports.bottles.export.pdf');
        Route::get('/bottles/export/excel', [ReportController::class, 'exportBottleExcel'])->name('reports.bottles.export.excel');
    });


});
