<?php

namespace App\Filament\Resources\RiwayatAbsensiResource\Pages;

use App\Filament\Resources\RiwayatAbsensiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatAbsensi extends EditRecord
{
    protected static string $resource = RiwayatAbsensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
