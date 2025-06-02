<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Pages\Shared\BaseAturVisiMisiSejarah; // Menggunakan base class baru

class AturVisiMisiSejarah extends BaseAturVisiMisiSejarah
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text'; // Icon yang sesuai
    protected static ?string $navigationGroup = 'Konten Website';
    protected static ?string $navigationLabel = 'Visi, Misi & Sejarah';
    protected static ?int $navigationSort = 2; // Sesuaikan urutan
    protected static ?string $slug = 'pengaturan-visi-misi-sejarah';

    public function getTitle(): string
    {
        return 'Pengaturan Visi, Misi & Sejarah (Admin)';
    }

    // public static function getNavigationBadge(): ?string
    // {
    //     $emptyFields = 0;
    //     // Pastikan instance dibuat dengan benar untuk memanggil method non-statis
    //     $instance = new static(); 
    //     $currentData = $instance->getCurrentData();
        
    //     // Gunakan SETTING_KEYS dari base class jika memungkinkan atau definisikan secara eksplisit
    //     // Jika SETTING_KEYS di base class adalah public/protected static, bisa diakses
    //     // Jika tidak, definisikan keys yang relevan di sini
    //     $relevantKeys = ['web_vision', 'web_mission', 'web_brief_history'];

    //     foreach ($relevantKeys as $key) {
    //         if (empty($currentData[$key] ?? '')) {
    //             $emptyFields++;
    //         }
    //     }
        
    //     return $emptyFields > 0 ? (string) $emptyFields : null;
    // }

    // public static function getNavigationBadgeColor(): ?string
    // {
    //     return static::getNavigationBadge() ? 'warning' : null;
    // }
}