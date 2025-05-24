<?php

namespace App\Filament\Admin\Resources\KegiatanGaleriResource\Pages;

use App\Enums\StatusPublikasiGaleri;
use App\Filament\Admin\Resources\KegiatanGaleriResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Enums\StatusPublikasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class EditKegiatanGaleri extends EditRecord
{
    protected static string $resource = KegiatanGaleriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        Log::info('EditKegiatanGaleri - mutateFormDataBeforeSave - Data Awal dari Form:', $data);
        
        // Ambil semua data asli dari record untuk perbandingan dan fallback
        $originalRecordData = $this->record->attributesToArray();

        // Pastikan user_id selalu diupdate dengan user yang melakukan edit
        $data['user_id'] = Auth::id();

        // Handle slug: hanya update jika nama_kegiatan berubah DAN slug dikosongkan,
        // atau jika slug memang belum ada.
        // Jika nama_kegiatan tidak berubah, atau slug sudah ada dan tidak dikosongkan, pertahankan slug lama.
        if (!empty($data['nama_kegiatan'])) {
            if ($data['nama_kegiatan'] !== $this->record->nama_kegiatan || empty($data['slug_kegiatan'])) {
                 $data['slug_kegiatan'] = Str::slug($data['nama_kegiatan']);
            } elseif (empty($data['slug_kegiatan']) && !empty($this->record->slug_kegiatan)) {
                // Jika slug dikosongkan di form padahal sebelumnya ada, dan nama tidak berubah,
                // biarkan slug lama atau regenerate berdasarkan nama. Untuk konsistensi, kita regenerate.
                 $data['slug_kegiatan'] = Str::slug($data['nama_kegiatan']);
            } else {
                // Jika nama tidak berubah dan slug tidak dikosongkan, gunakan slug yang ada di form (atau dari record jika form kosong)
                $data['slug_kegiatan'] = filled($data['slug_kegiatan']) ? $data['slug_kegiatan'] : $this->record->slug_kegiatan;
            }
        } else {
            // Jika nama_kegiatan dikosongkan, slug juga dikosongkan (atau pertahankan slug lama jika itu behavior yg diinginkan)
            $data['slug_kegiatan'] = null; 
        }


        // Logika untuk status_publikasi dan tanggal_publikasi saat edit
        if (array_key_exists('is_published', $data)) { // Cek apakah toggle 'is_published' ada di data form
            if ($data['is_published'] === true) {
                $data['status_publikasi'] = StatusPublikasiGaleri::TERBIT->value;
                // Jika 'Publikasikan' dicentang DAN 'Tanggal Publikasi' dikosongkan di form,
                // ATAU jika sebelumnya belum pernah publish (tanggal_publikasi masih null di record),
                // maka set tanggal_publikasi ke waktu sekarang.
                if (empty($data['tanggal_publikasi']) || is_null($this->record->tanggal_publikasi)) {
                    $data['tanggal_publikasi'] = now();
                }
                // Jika user mengisi tanggal_publikasi secara manual di form, nilai itu yang akan dipakai (sudah ada di $data['tanggal_publikasi']).
            } else { // Jika is_published === false
                $data['status_publikasi'] = StatusPublikasiGaleri::DRAFT->value;
                // Opsional: Jika 'Publikasikan' TIDAK dicentang,
                // Anda bisa memilih untuk mengosongkan tanggal_publikasi.
                // $data['tanggal_publikasi'] = null;
            }
        } else {
            // Jika 'is_published' tidak ada di $data (misalnya toggle tidak ada di form karena suatu kondisi),
            // pertahankan status_publikasi dan tanggal_publikasi dari record.
            $data['status_publikasi'] = $this->record->status_publikasi->value; // Ambil dari record
            $data['tanggal_publikasi'] = $this->record->tanggal_publikasi;     // Ambil dari record
        }
        unset($data['is_published']); // Hapus field virtual

        // Memastikan field lain yang mungkin tidak ada di form (jika form tidak lengkap)
        // tidak mengosongkan nilai yang sudah ada di database.
        // Ini lebih aman dilakukan dengan memastikan form memuat semua field yang relevan.
        // Namun, sebagai fallback:
        foreach ($originalRecordData as $key => $value) {
            if (!array_key_exists($key, $data) && $key !== 'is_published') { // Jangan timpa jika sudah ada di $data, kecuali is_published
                // $data[$key] = $value; // Ini bisa berisiko jika ada field yang memang sengaja dikosongkan
            }
        }
        
        Log::info('EditKegiatanGaleri - mutateFormDataBeforeSave - Data Akhir untuk Disimpan:', $data);
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // Mengisi form dengan data yang ada, termasuk state untuk toggle 'is_published'
    protected function fillForm(): void
    {
        // Ambil semua data dari record yang bisa di-fill ke form
        $formData = $this->record->attributesToArray();
        
        // Set state untuk toggle 'is_published' berdasarkan status_publikasi record
        // Pastikan status_publikasi adalah instance Enum atau bisa dikonversi
        $statusPublikasi = $this->record->status_publikasi;
        if (!$statusPublikasi instanceof StatusPublikasiGaleri && !is_null($statusPublikasi)) {
            $statusPublikasi = StatusPublikasiGaleri::tryFrom((string)$statusPublikasi);
        }
        $formData['is_published'] = $statusPublikasi === StatusPublikasiGaleri::TERBIT;
        
        // Isi form dengan data yang sudah disiapkan
        $this->form->fill($formData);
        Log::info('EditKegiatanGaleri - fillForm - Form Data Filled:', $this->form->getState());
    }
}
