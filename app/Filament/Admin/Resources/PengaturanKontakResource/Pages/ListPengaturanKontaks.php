<?php

namespace App\Filament\Admin\Resources\PengaturanKontakResource\Pages;

use App\Filament\Admin\Resources\PengaturanKontakResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengaturanKontaks extends ListRecords
{
    protected static string $resource = PengaturanKontakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
