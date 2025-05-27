<?php

namespace App\Filament\Santri\Resources\RiwayatAbsensiSantriResource\Pages;

use App\Filament\Santri\Resources\RiwayatAbsensiSantriResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatAbsensiSantri extends EditRecord
{
    protected static string $resource = RiwayatAbsensiSantriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
