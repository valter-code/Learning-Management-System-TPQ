<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GaleriPublikController;
use App\Http\Controllers\PengumumanPublikController;
use App\Http\Controllers\RegistrasiSantriController;

Route::get('/', function () {
    return view('index');
});

Route::get('/pengumuman', [PengumumanPublikController::class, 'index'])->name('pengumuman.index'); // Kita beri nama 'pengumuman.index'

// Jika Anda ingin halaman detail pengumuman (untuk link "Baca Selengkapnya")
Route::get('/pengumuman/{slug}', [PengumumanPublikController::class, 'show'])->name('pengumuman.show');


Route::get('/galeri', [GaleriPublikController::class, 'index'])->name('galeri.index');

Route::get('tentang', function () {
    return view('tentang');
});

Route::get('kontak', function () {
    return view('kontak');
});

Route::get('/registrasi-santri', [RegistrasiSantriController::class, 'create'])->name('registrasi.santri.create');
Route::post('/registrasi-santri', [RegistrasiSantriController::class, 'store'])->name('registrasi.santri.store');