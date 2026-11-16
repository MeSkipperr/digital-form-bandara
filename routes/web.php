<?php

use App\Http\Controllers\Admin\AlatController;
use App\Http\Controllers\Admin\DasborController;
use App\Http\Controllers\Admin\KategoriAlatController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\RiwayatPengecekanController;
use App\Http\Controllers\Admin\TemplateFormController;
use App\Http\Controllers\Admin\TugasController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dasbor', DasborController::class)->name('dasbor');
    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
    Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
    Route::put('/pengguna/{pengguna}', [PenggunaController::class, 'update'])->name('pengguna.update');
    Route::get('/alat', [AlatController::class, 'index'])->name('alat.index');
    Route::post('/alat', [AlatController::class, 'store'])->name('alat.store');
    Route::get('/alat/{alat}', [AlatController::class, 'show'])->name('alat.show');
    Route::put('/alat/{alat}', [AlatController::class, 'update'])->name('alat.update');
    Route::delete('/alat/{alat}', [AlatController::class, 'destroy'])->name('alat.destroy');
    Route::get('/alat/{alat}/form', [AlatController::class, 'formIndex'])->name('alat.form.index');
    Route::get('/alat/{alat}/form/{form}', [AlatController::class, 'task'])->whereNumber('form')->name('alat.form.task');
    Route::get('/form', [TemplateFormController::class, 'index'])->name('form.index');
    Route::post('/form', [TemplateFormController::class, 'store'])->name('form.store');
    Route::put('/form/{templateForm}', [TemplateFormController::class, 'update'])->name('form.update');
    Route::delete('/form/{templateForm}', [TemplateFormController::class, 'destroy'])->name('form.destroy');
    Route::scopeBindings()->group(function () {
        Route::get('/form/{templateForm}/tugas', [TugasController::class, 'index'])->name('form.tugas.index');
        Route::post('/form/{templateForm}/tugas', [TugasController::class, 'store'])->name('form.tugas.store');
        Route::put('/form/{templateForm}/tugas/{tugas}', [TugasController::class, 'update'])->name('form.tugas.update');
        Route::delete('/form/{templateForm}/tugas/{tugas}', [TugasController::class, 'destroy'])->name('form.tugas.destroy');
    });
    Route::post('/kategori-alat', [KategoriAlatController::class, 'store'])->name('kategori-alat.store');
    Route::put('/kategori-alat/{kategoriAlat}', [KategoriAlatController::class, 'update'])->name('kategori-alat.update');
    Route::delete('/kategori-alat/{kategoriAlat}', [KategoriAlatController::class, 'destroy'])->name('kategori-alat.destroy');
    Route::get('/riwayat', [RiwayatPengecekanController::class, 'index'])->name('riwayat.index');
});
