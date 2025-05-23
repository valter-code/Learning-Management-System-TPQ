<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PengumumanPublikController;

Route::get('/', function () {
    return view('index');
});

Route::get('/pengumuman', [PengumumanPublikController::class, 'index'])->name('pengumuman.index'); // Kita beri nama 'pengumuman.index'

// Jika Anda ingin halaman detail pengumuman (untuk link "Baca Selengkapnya")
Route::get('/pengumuman/{slug}', [PengumumanPublikController::class, 'show'])->name('pengumuman.show');


Route::get('galeri', function () {
    return view('galeri');
})->name('galeri');
