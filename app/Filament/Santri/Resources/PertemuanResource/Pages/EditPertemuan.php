<?php

namespace App\Filament\Santri\Resources\PertemuanResource\Pages;

use App\Filament\Santri\Resources\PertemuanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPertemuan extends EditRecord
{
    protected static string $resource = PertemuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
