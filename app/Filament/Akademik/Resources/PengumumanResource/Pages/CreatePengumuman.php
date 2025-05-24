<?php

namespace App\Filament\Akademik\Resources\PengumumanResource\Pages;

use App\Filament\Admin\Resources\PengumumanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Log;   
use Illuminate\Support\Str;           

class CreatePengumuman extends CreateRecord
{
    protected static string $resource = PengumumanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        Log::info('CreatePengumuman - mutateFormDataBeforeCreate - Data Awal:', $data);
        Log::info('CreatePengumuman - mutateFormDataBeforeCreate - Auth ID:', ['auth_id' => Auth::id()]);

        $data['user_id'] = Auth::id(); // Mengisi user_id

        // Jika Anda tidak pakai Spatie Sluggable dan ingin slug dari judul
        if (empty($data['slug']) && !empty($data['judul'])) {
             $data['slug'] = Str::slug($data['judul']);
        }

        Log::info('CreatePengumuman - mutateFormDataBeforeCreate - Data Akhir:', $data);
        return $data;
    }

    // Opsional: Redirect kembali ke halaman index setelah create
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}