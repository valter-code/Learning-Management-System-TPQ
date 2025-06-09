<?php

namespace App\Filament\Resources\RiwayatAbsensiPengajarResource\Pages;

use App\Filament\Resources\RiwayatAbsensiPengajarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatAbsensiPengajars extends ListRecords
{
    protected static string $resource = RiwayatAbsensiPengajarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
