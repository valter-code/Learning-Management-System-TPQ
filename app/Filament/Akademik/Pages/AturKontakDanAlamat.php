<?php

namespace App\Filament\Akademik\Pages;

use App\Filament\Pages\Shared\BaseAturKontakDanAlamat;

class AturKontakDanAlamat extends BaseAturKontakDanAlamat
{
    // Konfigurasi khusus panel Akademik
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Konten Website';
    protected static ?string $navigationLabel = 'Kontak & Alamat';
    protected static ?int $navigationSort = 6;
    protected static ?string $slug = 'pengaturan-kontak-alamat';

    /**
     * Override title jika perlu
     */
    public function getTitle(): string
    {
        return 'Pengaturan Kontak & Alamat Website (Akademik)';
    }

    /**
     * Bisa tambahkan method khusus akademik jika diperlukan
     */
    protected function getHeaderActions(): array
    {
        return [
            // Bisa tambahkan action khusus akademik di sini
        ];
    }
}