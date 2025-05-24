<?php

namespace App\Filament\Admin\Resources\PendaftarSantriResource\Pages;

use App\Filament\Admin\Resources\PendaftarSantriResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPendaftarSantri extends EditRecord
{
    protected static string $resource = PendaftarSantriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
