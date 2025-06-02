<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Pages\Shared\BaseAturKontakDanAlamat;

class AturKontakDanAlamat1 extends BaseAturKontakDanAlamat
{
    // Konfigurasi khusus panel Admin
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Konten Website';
    protected static ?string $navigationLabel = 'Kontak & Alamat';
    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'pengaturan-kontak-alamat';

    /**
     * Override title jika perlu
     */
    public function getTitle(): string
    {
        return 'Pengaturan Kontak & Alamat Website (Admin)';
    }

    /**
     * Bisa tambahkan method khusus admin jika diperlukan
     */
    public static function getNavigationBadge(): ?string
    {
        // Contoh: tampilkan badge jika ada data kosong
        $emptyFields = 0;
        $instance = new static();
        $currentData = $instance->getCurrentData();
        
        foreach (['contact_address', 'contact_phone', 'contact_email'] as $key) {
            if (empty($currentData[$key] ?? '')) {
                $emptyFields++;
            }
        }
        
        return $emptyFields > 0 ? (string) $emptyFields : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() ? 'warning' : null;
    }
}