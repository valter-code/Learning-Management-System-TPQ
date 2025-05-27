<?php

namespace App\Filament\Admin\Resources\SppResource\Pages;

use App\Filament\Admin\Resources\SppResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpp extends EditRecord
{
    protected static string $resource = SppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
