<?php

namespace App\Filament\Akademik\Pages; // Namespace disesuaikan untuk Akademik

use App\Filament\Pages\Shared\BaseAturVisiMisiSejarah; // Menggunakan base class baru

class AturVisiMisiSejarah extends BaseAturVisiMisiSejarah
{
    // Konfigurasi khusus panel Akademik
    protected static ?string $navigationIcon = 'heroicon-o-document-text'; // Icon yang sesuai
    protected static ?string $navigationGroup = 'Konten Website';
    protected static ?string $navigationLabel = 'Visi, Misi & Sejarah';
    protected static ?int $navigationSort = 2; // Sesuaikan urutan
    protected static ?string $slug = 'pengaturan-visi-misi-sejarah';


    public function getTitle(): string
    {
        return 'Pengaturan Visi, Misi & Sejarah (Akademik)';
    }

    // Anda bisa memilih untuk memiliki badge yang berbeda atau tidak sama sekali di panel Akademik
    // Jika sama, bisa copy dari Admin/AturVisiMisiSejarah.php
    // Contoh: Tanpa badge
    // public static function getNavigationBadge(): ?string
    // {
    //     return null;
    // }
}