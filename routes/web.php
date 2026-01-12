<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\SPBYController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Default -> halaman data
Route::get('/', function () {
    return redirect()->route('data.index');
});

// Form (FormController tetap menanganinya)
Route::get('/travel/create', [FormController::class, 'create'])->name('travel.create');
Route::post('/travel', [FormController::class, 'store'])->name('travel.store');

// Data (DataController)
Route::get('/data', [DataController::class, 'index'])->name('data.index');
Route::get('/data/{travel}', [DataController::class, 'show'])->name('data.show');
Route::delete('/data/{travel}', [DataController::class, 'destroy'])->name('data.destroy');

// Partial endpoint for AJAX detail
Route::get('/data/{travel}/partial', [DataController::class, 'partial'])->name('data.partial');

// SPBY (view + pdf)
Route::get('/data/{travel}/spby', [SpbyController::class, 'show'])->name('data.spby');
Route::get('/data/{travel}/spby/pdf', [SpbyController::class, 'pdf'])->name('data.spby.pdf');