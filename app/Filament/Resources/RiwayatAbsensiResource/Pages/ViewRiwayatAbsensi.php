<?php

namespace App\Filament\Pengajar\Resources\RiwayatAbsensiResource\Pages;

use App\Filament\Pengajar\Resources\RiwayatAbsensiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRiwayatAbsensi extends ViewRecord
{
    protected static string $resource = RiwayatAbsensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tidak ada action edit/delete untuk pengajar
        ];
    }
}