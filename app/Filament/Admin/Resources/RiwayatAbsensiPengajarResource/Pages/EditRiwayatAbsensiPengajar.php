<?php

namespace App\Filament\Admin\Resources\RiwayatAbsensiPengajarResource\Pages;

use App\Filament\Admin\Resources\RiwayatAbsensiPengajarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatAbsensiPengajar extends EditRecord
{
    protected static string $resource = RiwayatAbsensiPengajarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
