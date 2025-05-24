<?php

namespace App\Filament\Admin\Resources\PendaftarSantriResource\Pages;

use App\Filament\Admin\Resources\PendaftarSantriResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPendaftarSantris extends ListRecords
{
    protected static string $resource = PendaftarSantriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
