<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\PenguranganController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DendaAdministratifController;
use App\Http\Controllers\DafnomController; // Ditambahkan untuk konsistensi
use App\Http\Controllers\PembatalanController; // == BARIS INI YANG PALING PENTING ==
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
    Route::get('/pengurangan-sppt', [PenguranganController::class, 'create'])->name('pengurangan.create');
    Route::post('/pengurangan-sppt/preview', [PenguranganController::class, 'preview'])->name('pengurangan.preview');
    Route::post('/pengurangan-sppt/confirm', [PenguranganController::class, 'confirmStore'])->name('pengurangan.confirm');
    Route::get('/laporan-pengurangan', [PenguranganController::class, 'indexLaporan'])->name('laporan.pengurangan');
    Route::get('/laporan-pengurangan/cetak-pdf', [PenguranganController::class, 'cetakPdf'])->name('laporan.pengurangan.cetak-pdf');
    Route::get('/pengurangan/{id}/cetak-pdf', [PenguranganController::class, 'cetakSinglePdf'])->name('pengurangan.cetak-single-pdf');
    Route::get('/pengurangan/{id}/edit', [PenguranganController::class, 'edit'])->name('pengurangan.edit');
    Route::patch('/pengurangan/{id}', [PenguranganController::class, 'update'])->name('pengurangan.update');
    Route::delete('/pengurangan/{id}', [PenguranganController::class, 'destroy'])->name('pengurangan.destroy');

    Route::get('/laporan-pengurangan/filter-pdf', function(Request $request) {
        $kecamatans = RefKecamatan::on('oracle')
            ->where('kd_propinsi', '35')
            ->where('kd_dati2', '18')
            ->orderBy('nm_kecamatan')
            ->get();
        return view('pengurangan.filter_cetak_pdf', compact('kecamatans'));
    })->name('laporan.pengurangan.filter-pdf');
    Route::get('/laporan-pengurangan/cetak-pdf-filtered', [PenguranganController::class, 'cetakFilteredPdf'])->name('laporan.pengurangan.cetak-pdf-filtered');
    

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
        Route::get('/create', [DendaAdministratifController::class, 'create'])->name('create');
        Route::get('/get-kelurahan-by-kecamatan', [DendaAdministratifController::class, 'getKelurahanByKecamatan'])->name('get-kelurahan-by-kecamatan'); // AJAX
        Route::post('/preview', [DendaAdministratifController::class, 'preview'])->name('preview');
        // PERBAIKAN PENTING: Ubah 'confirm' menjadi 'confirmStore'
        Route::post('/confirm', [DendaAdministratifController::class, 'confirmStore'])->name('confirm'); 
        Route::get('/laporan', [DendaAdministratifController::class, 'index'])->name('index');
        Route::get('/laporan/cetak-pdf', [DendaAdministratifController::class, 'cetakPdf'])->name('cetak-pdf');
        Route::get('/laporan/{id}/cetak-pdf', [DendaAdministratifController::class, 'cetakSinglePdf'])->name('cetak-single-pdf');
        Route::get('/laporan/{id}/edit', [DendaAdministratifController::class, 'edit'])->name('edit');
        //Route::patch('/laporan/{id}', [DendaAdministratifController::class, 'update'])->name('update');
        Route::delete('/laporan/{id}', [DendaAdministratifController::class, 'destroy'])->name('destroy');
        

        Route::get('/laporan/filter-pdf', function(Request $request) {
            $kecamatans = RefKecamatan::on('oracle')
                ->where('kd_propinsi', '35')
                ->where('kd_dati2', '18')
                ->orderBy('nm_kecamatan')
                ->get();
            return view('denda_administratif.filter_cetak_pdf', compact('kecamatans'));
        })->name('filter-pdf');
        Route::get('/laporan/cetak-pdf-filtered', [DendaAdministratifController::class, 'cetakFilteredPdf'])->name('cetak-pdf-filtered');
    });
    Route::prefix('daftar-nominatif')->name('dafnom.')->group(function() {
        Route::get('/', [App\Http\Controllers\DafnomController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\DafnomController::class, 'store'])->name('store');
        Route::get('/get-kelurahan/{kd_kecamatan}', [App\Http\Controllers\DafnomController::class, 'getKelurahanByKecamatan'])->name('getKelurahan');
        Route::get('/log-status/{log}', [App\Http\Controllers\DafnomController::class, 'getLogStatus'])->name('log.status');
    });

    Route::prefix('pembatalan-sppt')->name('pembatalan.')->group(function () {
        Route::get('/create', [PembatalanController::class, 'create'])->name('create');
        Route::post('/preview', [PembatalanController::class, 'preview'])->name('preview');
        Route::post('/store', [PembatalanController::class, 'store'])->name('store');
        Route::get('/laporan', [PembatalanController::class, 'index'])->name('index');
        Route::get('/{pembatalan}/edit', [PembatalanController::class, 'edit'])->name('edit');
        Route::post('/{pembatalan}/preview-update', [PembatalanController::class, 'previewUpdate'])->name('preview.update');
        Route::put('/{pembatalan}', [PembatalanController::class, 'update'])->name('update');
        Route::delete('/{pembatalan}', [PembatalanController::class, 'destroy'])->name('destroy');
        Route::get('/{pembatalan}/cetak-pdf', [PembatalanController::class, 'cetakSinglePdf'])->name('cetak-single-pdf');
        Route::get('/filter-cetak-pdf', [PembatalanController::class, 'showFilterCetakPdfForm'])->name('filter-cetak-pdf');
        Route::get('/cetak-pdf-filtered', [PembatalanController::class, 'cetakFilteredPdf'])->name('cetak-pdf-filtered');
    });
    Route::prefix('penggabungan-sppt')->name('penggabungan.')->group(function () {
        Route::get('/create', [App\Http\Controllers\PenggabunganController::class, 'create'])->name('create');
        Route::post('/fetch-data', [App\Http\Controllers\PenggabunganController::class, 'fetchData'])->name('fetch-data'); // AJAX
        Route::post('/preview', [App\Http\Controllers\PenggabunganController::class, 'preview'])->name('preview');
        Route::post('/store', [App\Http\Controllers\PenggabunganController::class, 'store'])->name('store');
        Route::get('/laporan', [App\Http\Controllers\PenggabunganController::class, 'index'])->name('index');
        // Rute untuk edit dan delete bisa ditambahkan nanti jika diperlukan
    });
});

require __DIR__.'/auth.php';