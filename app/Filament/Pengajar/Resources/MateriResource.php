<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MateriResource\Pages;
use App\Filament\Resources\MateriResource\RelationManagers;
use App\Models\Materi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MateriResource extends Resource
{
    protected static ?string $model = Materi::class; // Atau model yang sesuai

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack'; // Ikon asli bisa dikomentari

    // ... (kode resource lainnya) ...

    /**
     * Metode ini akan mencegah resource muncul di navigasi.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false; // Set ke false untuk menyembunyikan dari navigasi
    }
}