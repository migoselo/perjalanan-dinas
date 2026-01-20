<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\SpbyController;
use App\Http\Controllers\SPDController;

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

// SPBY (list, view + pdf)
Route::get('/spby', [SpbyController::class, 'index'])->name('spby.index');
Route::get('/spby/create/{travel}', [SpbyController::class, 'form'])->name('spby.create');
Route::post('/spby/create/{travel}', [SpbyController::class, 'form'])->name('spby.store');
Route::get('/data/{travel}/spby', [SpbyController::class, 'show'])->name('data.spby');
Route::get('/data/{travel}/spby/latest', [SpbyController::class, 'getLatestData'])->name('data.spby.latest');
Route::get('/data/{travel}/spby/pdf', [SpbyController::class, 'pdf'])->name('data.spby.pdf');

// SPD (list, view + form input + parse CSV)
Route::get('/spd', [SPDController::class, 'index'])->name('spd.index');
Route::get('/spd/create', [SPDController::class, 'create'])->name('spd.create');
Route::post('/spd/parse', [SPDController::class, 'parseCsv'])->name('spd.parse');
Route::post('/spd/preview', [SPDController::class, 'preview'])->name('spd.preview');
Route::get('/data/{travel}/spd', [SPDController::class, 'show'])->name('data.spd');