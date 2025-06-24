<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\PenguranganController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DendaAdministratifController;
use App\Http\Controllers\DafnomController;
use App\Http\Controllers\PembatalanController;
use App\Http\Controllers\PenggabunganController; // <-- Pastikan ini ada
use App\Models\RefKecamatan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- ROUTE UNTUK FITUR PENGURANGAN SPPT ---
    Route::get('/pengurangan-sppt', [PenguranganController::class, 'create'])->name('pengurangan.create')->middleware('check.menu:pengurangan.create');
    Route::post('/pengurangan-sppt/preview', [PenguranganController::class, 'preview'])->name('pengurangan.preview')->middleware('check.menu:pengurangan.create');
    Route::post('/pengurangan-sppt/confirm', [PenguranganController::class, 'confirmStore'])->name('pengurangan.confirm')->middleware('check.menu:pengurangan.create');
    Route::get('/laporan-pengurangan', [PenguranganController::class, 'indexLaporan'])->name('laporan.pengurangan')->middleware('check.menu:laporan.pengurangan');
    Route::get('/pengurangan/{id}/edit', [PenguranganController::class, 'edit'])->name('pengurangan.edit')->middleware('check.menu:laporan.pengurangan');
    Route::patch('/pengurangan/{id}', [PenguranganController::class, 'update'])->name('pengurangan.update')->middleware('check.menu:laporan.pengurangan');
    Route::delete('/pengurangan/{id}', [PenguranganController::class, 'destroy'])->name('pengurangan.destroy')->middleware('check.menu:laporan.pengurangan');
    Route::get('/pengurangan/{id}/cetak-pdf', [PenguranganController::class, 'cetakSinglePdf'])->name('pengurangan.cetak-single-pdf')->middleware('check.menu:laporan.pengurangan');
    Route::get('/laporan-pengurangan/filter-pdf', function(Request $request) {
        $kecamatans = RefKecamatan::on('oracle')->where('kd_propinsi', '35')->where('kd_dati2', '18')->orderBy('nm_kecamatan')->get();
        return view('pengurangan.filter_cetak_pdf', compact('kecamatans'));
    })->name('laporan.pengurangan.filter-pdf')->middleware('check.menu:laporan.pengurangan');
    Route::get('/laporan-pengurangan/cetak-pdf-filtered', [PenguranganController::class, 'cetakFilteredPdf'])->name('laporan.pengurangan.cetak-pdf-filtered')->middleware('check.menu:laporan.pengurangan');

    // --- ROUTE UNTUK MANAJEMEN USER (Admin only) ---
    Route::prefix('users')->name('users.')->middleware('admin')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::patch('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // --- ROUTES UNTUK PENGHAPUSAN DENDA ADMINISTRATIF ---
    Route::prefix('denda-administratif')->name('denda_administratif.')->group(function () {
        Route::get('/create', [DendaAdministratifController::class, 'create'])->name('create')->middleware('check.menu:denda_administratif.create');
        Route::post('/preview', [DendaAdministratifController::class, 'preview'])->name('preview')->middleware('check.menu:denda_administratif.create');
        Route::post('/confirm', [DendaAdministratifController::class, 'confirmStore'])->name('confirm')->middleware('check.menu:denda_administratif.create');
        Route::get('/laporan', [DendaAdministratifController::class, 'index'])->name('index')->middleware('check.menu:denda_administratif.index');
        Route::get('/laporan/{id}/edit', [DendaAdministratifController::class, 'edit'])->name('edit')->middleware('check.menu:denda_administratif.index');
        Route::delete('/laporan/{id}', [DendaAdministratifController::class, 'destroy'])->name('destroy')->middleware('check.menu:denda_administratif.index');
        Route::get('/get-kelurahan-by-kecamatan', [DendaAdministratifController::class, 'getKelurahanByKecamatan'])->name('get-kelurahan-by-kecamatan')->middleware('check.menu:denda_administratif.create');
        Route::get('/laporan/{id}/cetak-pdf', [DendaAdministratifController::class, 'cetakSinglePdf'])->name('cetak-single-pdf')->middleware('check.menu:denda_administratif.index');
        Route::get('/laporan/filter-pdf', function(Request $request) {
            $kecamatans = RefKecamatan::on('oracle')->where('kd_propinsi', '35')->where('kd_dati2', '18')->orderBy('nm_kecamatan')->get();
            return view('denda_administratif.filter_cetak_pdf', compact('kecamatans'));
        })->name('filter-pdf')->middleware('check.menu:denda_administratif.index');
        Route::get('/laporan/cetak-pdf-filtered', [DendaAdministratifController::class, 'cetakFilteredPdf'])->name('cetak-pdf-filtered')->middleware('check.menu:denda_administratif.index');
    });

    // --- ROUTES UNTUK DAFTAR NOMINATIF ---
    Route::prefix('daftar-nominatif')->name('dafnom.')->group(function() {
        Route::get('/', [DafnomController::class, 'create'])->name('create')->middleware('check.menu:dafnom.create');
        Route::post('/', [DafnomController::class, 'store'])->name('store')->middleware('check.menu:dafnom.create');
        Route::get('/get-kelurahan/{kd_kecamatan}', [DafnomController::class, 'getKelurahanByKecamatan'])->name('getKelurahan')->middleware('check.menu:dafnom.create');
        Route::get('/log-status/{log}', [DafnomController::class, 'getLogStatus'])->name('log.status')->middleware('check.menu:dafnom.create');
    });

    // --- ROUTES UNTUK PEMBATALAN SPPT ---
    Route::prefix('pembatalan-sppt')->name('pembatalan.')->group(function () {
        Route::get('/create', [PembatalanController::class, 'create'])->name('create')->middleware('check.menu:pembatalan.create');
        Route::post('/preview', [PembatalanController::class, 'preview'])->name('preview')->middleware('check.menu:pembatalan.create');
        Route::post('/store', [PembatalanController::class, 'store'])->name('store')->middleware('check.menu:pembatalan.create');
        Route::get('/laporan', [PembatalanController::class, 'index'])->name('index')->middleware('check.menu:pembatalan.index');
        Route::get('/{pembatalan}/edit', [PembatalanController::class, 'edit'])->name('edit')->middleware('check.menu:pembatalan.index');
        Route::post('/{pembatalan}/preview-update', [PembatalanController::class, 'previewUpdate'])->name('preview.update')->middleware('check.menu:pembatalan.index');
        Route::put('/{pembatalan}', [PembatalanController::class, 'update'])->name('update')->middleware('check.menu:pembatalan.index');
        Route::delete('/{pembatalan}', [PembatalanController::class, 'destroy'])->name('destroy')->middleware('check.menu:pembatalan.index');
        Route::get('/{pembatalan}/cetak-pdf', [PembatalanController::class, 'cetakSinglePdf'])->name('cetak-single-pdf')->middleware('check.menu:pembatalan.index');
        Route::get('/filter-cetak-pdf', [PembatalanController::class, 'showFilterCetakPdfForm'])->name('filter-cetak-pdf')->middleware('check.menu:pembatalan.index');
        Route::get('/cetak-pdf-filtered', [PembatalanController::class, 'cetakFilteredPdf'])->name('cetak-pdf-filtered')->middleware('check.menu:pembatalan.index');
    });

    // --- ROUTES UNTUK PENGGABUNGAN SPPT ---
    Route::prefix('penggabungan-sppt')->name('penggabungan.')->group(function () {
        Route::get('/create', [PenggabunganController::class, 'create'])->name('create')->middleware('check.menu:penggabungan.create');
        Route::post('/fetch-data', [PenggabunganController::class, 'fetchData'])->name('fetch-data')->middleware('check.menu:penggabungan.create');
        Route::post('/preview', [PenggabunganController::class, 'preview'])->name('preview')->middleware('check.menu:penggabungan.create');
        Route::post('/store', [PenggabunganController::class, 'store'])->name('store')->middleware('check.menu:penggabungan.create');
        Route::get('/laporan', [PenggabunganController::class, 'index'])->name('index')->middleware('check.menu:penggabungan.index');
        Route::delete('/{penggabungan}', [PenggabunganController::class, 'destroy'])->name('destroy')->middleware('check.menu:penggabungan.index');
        Route::get('/{penggabungan}/cetak-pdf', [PenggabunganController::class, 'cetakSinglePdf'])->name('cetak-single-pdf')->middleware('check.menu:penggabungan.index');
        Route::get('/filter-cetak-pdf', [PenggabunganController::class, 'showFilterCetakPdfForm'])->name('filter-cetak-pdf')->middleware('check.menu:penggabungan.index');
        Route::get('/cetak-pdf-filtered', [PenggabunganController::class, 'cetakFilteredPdf'])->name('cetak-pdf-filtered')->middleware('check.menu:penggabungan.index');
    });
});

require __DIR__.'/auth.php';