<?php

namespace App\Filament\Santri\Resources\PertemuanResource\Pages;

use App\Filament\Santri\Resources\PertemuanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPertemuan extends ViewRecord
{
    protected static string $resource = PertemuanResource::class;

    // Santri tidak bisa mengedit dari halaman view ini
    protected function getHeaderActions(): array
    {
        return []; // Kosongkan atau tambahkan aksi yang relevan untuk santri
    }
}