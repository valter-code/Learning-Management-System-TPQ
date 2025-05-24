<?php

namespace App\Http\Controllers;

use App\Models\KegiatanGaleri;
use App\Enums\StatusPublikasiGaleri; 
use Illuminate\Http\Request;

class GaleriPublikController extends Controller
{
    public function index(Request $request)
    {
        $selectedKegiatanId = $request->input('kegiatan_id');

        // Ambil semua KegiatanGaleri yang sudah terbit untuk opsi filter dropdown
        $semuaKegiatanGaleriUntukFilter = KegiatanGaleri::query()
                                        ->where('status_publikasi', StatusPublikasiGaleri::TERBIT->value)
                                        ->whereNotNull('tanggal_publikasi')
                                        ->where('tanggal_publikasi', '<=', now())
                                        ->orderBy('nama_kegiatan', 'asc')
                                        ->get();

        // Query untuk KegiatanGaleri yang akan ditampilkan di halaman
        $queryKegiatanGaleri = KegiatanGaleri::query()
                                ->where('status_publikasi', StatusPublikasiGaleri::TERBIT->value)
                                ->whereNotNull('tanggal_publikasi')
                                ->where('tanggal_publikasi', '<=', now())
                                ->with(['fotos' => function ($query) { 
                                    $query->orderBy('urutan_foto', 'asc');
                                    // kalo ingin membatasi jumlah foto yang ditampilkan per kegiatan di halaman ini
                                    // $query->limit(6); 
                                }])
                                ->orderBy('tanggal_publikasi', 'desc');

        // Jika ada kegiatan galeri yang dipilih dari filter, tampilkan hanya kegiatan ini
        if ($selectedKegiatanId) {
            $queryKegiatanGaleri->where('id', $selectedKegiatanId);
        }

        // Paginasi 
        $daftarTampilKegiatanGaleri = $queryKegiatanGaleri->paginate(5);

        return view('galeri', [
            
            'daftarTampilKegiatanGaleri' => $daftarTampilKegiatanGaleri, 
            'semuaKegiatanGaleriUntukFilter' => $semuaKegiatanGaleriUntukFilter, 
            'selectedKegiatanId' => $selectedKegiatanId
        ]);
    }
}
