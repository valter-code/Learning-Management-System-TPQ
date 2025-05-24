<?php

namespace App\Filament\Admin\Resources\KegiatanGaleriResource\Pages;

use App\Filament\Admin\Resources\KegiatanGaleriResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKegiatanGaleris extends ListRecords
{
    protected static string $resource = KegiatanGaleriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
