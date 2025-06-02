<?php

namespace App\Filament\Admin\Resources\PengaturanKontakResource\Pages;

use App\Filament\Admin\Resources\PengaturanKontakResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengaturanKontak extends EditRecord
{
    protected static string $resource = PengaturanKontakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
