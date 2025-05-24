<?php

namespace App\Filament\Admin\Resources\AkademikResource\Pages;

use App\Filament\Admin\Resources\AkademikResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAkademiks extends ListRecords
{
    protected static string $resource = AkademikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
