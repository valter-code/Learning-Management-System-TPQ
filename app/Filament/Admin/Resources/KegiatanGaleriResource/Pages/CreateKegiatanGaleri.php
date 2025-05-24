<?php

namespace App\Filament\Admin\Resources\KegiatanGaleriResource\Pages;

use App\Filament\Admin\Resources\KegiatanGaleriResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Enums\StatusPublikasiGaleri; // Menggunakan nama Enum dari pesan error
use Illuminate\Support\Facades\Log;

class CreateKegiatanGaleri extends CreateRecord
{
    protected static string $resource = KegiatanGaleriResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        Log::info('CreateKegiatanGaleri - mutateFormDataBeforeCreate - Data Awal dari Form:', $data);
        Log::info('CreateKegiatanGaleri - mutateFormDataBeforeCreate - Auth ID:', ['auth_id' => Auth::id()]);

        $data['user_id'] = Auth::id();

        if (empty($data['slug_kegiatan']) && !empty($data['nama_kegiatan'])) {
             $data['slug_kegiatan'] = Str::slug($data['nama_kegiatan']);
        }

        // Logika utama untuk status_publikasi dan tanggal_publikasi saat create
        // Pastikan $data['is_published'] benar-benar boolean true jika dicentang
        if (isset($data['is_published']) && $data['is_published'] === true) {
            // Menggunakan StatusPublikasiGaleri sesuai pesan error
            $data['status_publikasi'] = StatusPublikasiGaleri::TERBIT->value; 
            // Jika tanggal_publikasi dikosongkan di form, set ke waktu sekarang
            if (empty($data['tanggal_publikasi'])) {
                $data['tanggal_publikasi'] = now();
            }
        } else {
            // Jika is_published tidak dicentang atau tidak ada di data, set ke DRAFT
            // Menggunakan StatusPublikasiGaleri sesuai pesan error
            $data['status_publikasi'] = StatusPublikasiGaleri::DRAFT->value; 
            // $data['tanggal_publikasi'] = null; // Opsional: pastikan tanggal publikasi null jika draft
        }
        // Hapus field virtual 'is_published' karena tidak ada di tabel
        unset($data['is_published']);
        
        Log::info('CreateKegiatanGaleri - mutateFormDataBeforeCreate - Data Akhir untuk Disimpan:', $data);
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
