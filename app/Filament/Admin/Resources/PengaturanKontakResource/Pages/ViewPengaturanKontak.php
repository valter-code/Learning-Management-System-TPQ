<?php

namespace App\Filament\Admin\Resources\PengaturanKontakResource\Pages;

use App\Filament\Admin\Resources\PengaturanKontakResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPengaturanKontak extends ViewRecord
{
    protected static string $resource = PengaturanKontakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
