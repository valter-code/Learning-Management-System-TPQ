<?php

namespace App\Filament\Resources\PertemuanResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PertemuanResource;

class CreatePertemuan extends CreateRecord
{
    protected static string $resource = PertemuanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        Log::info('CreatePertemuan - mutateFormDataBeforeCreate - Data Awal:', $data);
        Log::info('CreatePertemuan - mutateFormDataBeforeCreate - Auth ID:', ['auth_id' => Auth::id()]);

        $data['user_id'] = Auth::id(); // Mengisi user_id

        // Mengambil active_kelas_id dari URL jika ada (seperti yang sudah Anda setup)
        if ($this->data && isset($this->data['active_kelas_id'])) { // Cek dari properti data jika sudah di-pass
             $data['kelas_id'] = $this->data['active_kelas_id'];
        } elseif (request()->filled('active_kelas_id')) { // Fallback ke request jika belum di properti data
            $data['kelas_id'] = request()->query('active_kelas_id');
        }

        Log::info('CreatePertemuan - mutateFormDataBeforeCreate - Data Akhir:', $data);
        return $data;
    }

    // Opsional: Arahkan kembali ke index dengan filter kelas yang sama
    protected function getRedirectUrl(): string
    {
        $kelasIdFilterValue = null;
        if (request()->filled('active_kelas_id')) {
            $kelasIdFilterValue = request()->query('active_kelas_id');
        } elseif (isset($this->record) && $this->record->kelas_id) { // Setelah record dibuat
             $kelasIdFilterValue = $this->record->kelas_id;
        }

        if ($kelasIdFilterValue) {
            return static::getResource()::getUrl('index', ['tableFilters[kelas_id][value]' => $kelasIdFilterValue]);
        }
        return static::getResource()::getUrl('index');
    }

    // Untuk menangani parameter 'active_kelas_id' dari URL dan memasukkannya ke data form awal
    protected function fillForm(): void
    {
        parent::fillForm(); // Panggil metode parent dulu
        if (request()->filled('active_kelas_id')) {
            $this->form->fill([
                'kelas_id' => request()->query('active_kelas_id'),
            ]);
        }
    }
}