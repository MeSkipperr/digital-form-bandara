<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/dasbor', 'admin.dasbor')->name('dasbor');
    Route::view('/pengguna', 'admin.pengguna.index')->name('pengguna.index');
    Route::view('/alat', 'admin.alat.index')->name('alat.index');
    Route::view('/alat/{alat}', 'admin.alat.show')->name('alat.show');
    Route::view('/alat/{alat}/form', 'admin.alat.form')->name('alat.form.index');
    Route::view('/alat/{alat}/form/{form}', 'admin.alat.task')->name('alat.form.task');
    Route::view('/riwayat', 'admin.riwayat.index')->name('riwayat.index');
});
