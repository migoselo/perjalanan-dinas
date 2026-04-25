<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\SpbyController;
use App\Http\Controllers\SPDController;
use App\Http\Controllers\RincianController;
use App\Http\Controllers\KuitansiController;
use App\Http\Controllers\ProgresController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Default -> halaman form input
Route::get('/', function () {
    return redirect()->route('travel.create');
});

// Form (FormController tetap menanganinya)
Route::get('/travel/create', [FormController::class, 'create'])->name('travel.create');
Route::post('/travel', [FormController::class, 'store'])->name('travel.store');

// Data (DataController)
Route::get('/data', [DataController::class, 'index'])->name('data.index');
Route::get('/data/{travel}', [DataController::class, 'show'])->name('data.show');
Route::delete('/data/{travel}', [DataController::class, 'destroy'])->name('data.destroy');
Route::get('/data/{travel}/edit', [DataController::class, 'edit'])->name('data.edit');
Route::put('/data/{travel}', [DataController::class, 'update'])->name('data.update');
Route::get('/data/{travel}/signature', [DataController::class, 'signatureForm'])->name('signature.form');
Route::post('/data/{travel}/signature', [DataController::class, 'signatureStore'])->name('signature.store');

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
Route::prefix('spd')->group(function () {
    Route::get('/', [SPDController::class, 'index'])->name('spd.index');
    Route::get('/create', [SPDController::class, 'create'])->name('spd.create');
    Route::get('/form', [SPDController::class, 'form'])->name('spd.form');
    Route::get('/{travel}/edit', [SPDController::class, 'edit'])->name('spd.edit');
    Route::get('/{travel}/pdf', [SPDController::class, 'pdf'])->name('spd.pdf');
    Route::delete('/{travel}', [SPDController::class, 'destroy'])->name('spd.destroy');
    Route::post('/parse', [SPDController::class, 'parseCsv'])->name('spd.parse');
    Route::match(['GET', 'POST'], '/preview', [SPDController::class, 'preview'])->name('spd.preview');
    Route::get('{travel}', [SPDController::class, 'show'])->name('spd.show');
});
Route::get('/data/{travel}/spd', [SPDController::class, 'show'])->name('data.spd');

// Rincian Surat (generate detailed travel expense report)
Route::get('/rincian-surat/{travelId}', [RincianController::class, 'show'])->name('rincian-surat');
Route::get('/rincian-surat', [RincianController::class, 'index'])->name('rincian-surat.index');
Route::get('/surat/{travelId?}', [RincianController::class, 'indexSurat'])->name('surat.index');
Route::get('/rincian', [RincianController::class, 'index'])->name('rincian.index');
Route::get('/rincian/form/{travel}', [RincianController::class, 'showForm'])->name('rincian.form');
Route::post('/rincian/store', [RincianController::class, 'storeForm'])->name('surat.store');

// Kuitansi
Route::get('/kuitansi', [KuitansiController::class, 'index'])->name('kuitansi.index');
Route::get('/kuitansi/{travel}', [KuitansiController::class, 'show'])->name('kuitansi.show');
Route::get('/kuitansi/{travel}/pdf', [KuitansiController::class, 'pdf'])->name('kuitansi.pdf');
Route::get('/kuitansi/{travel}/input', [KuitansiController::class, 'inputData'])->name('kuitansi.inputData');
Route::post('/kuitansi/{travel}/input', [KuitansiController::class, 'storeData'])->name('kuitansi.storeData');

// Progres SPT
Route::prefix('progres')->group(function () {
    Route::get('/', [ProgresController::class, 'index'])->name('progres.index');
    Route::post('/', [ProgresController::class, 'store'])->name('progres.store');
    Route::get('/data', [ProgresController::class, 'getData'])->name('progres.getData');
    Route::get('/travels', [ProgresController::class, 'getTravelList'])->name('progres.getTravelList');
    Route::post('/{id}/upload', [ProgresController::class, 'uploadFile'])->name('progres.uploadFile');
    Route::delete('/{id}/file', [ProgresController::class, 'deleteFile'])->name('progres.deleteFile');
    Route::delete('/{id}', [ProgresController::class, 'destroy'])->name('progres.destroy');
});