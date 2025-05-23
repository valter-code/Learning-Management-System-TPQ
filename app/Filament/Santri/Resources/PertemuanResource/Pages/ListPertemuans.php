<?php

namespace App\Filament\Santri\Resources\PertemuanResource\Pages; // Pastikan namespace ini benar

use Filament\Actions;
use App\Models\Pertemuan;
use App\Models\Kelas; // Import Kelas
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Santri\Resources\PertemuanResource;
use Illuminate\Contracts\Support\Htmlable; // Untuk return type getTitle

class ListPertemuans extends ListRecords
{
    protected static string $resource = PertemuanResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Santri tidak membuat pertemuan
    }

    protected function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery(); // Query dasar dari resource

        // Ambil nilai filter 'kelas_id' dari parameter URL
        // yang sudah di-set sebagai default oleh SelectFilter
        $activeKelasId = $this->tableFilters['kelas_id']['value'] ?? null;

        // Jika tidak ada di tableFilters (misalnya load pertama sebelum Livewire init penuh),
        // coba ambil langsung dari request. Ini sebagai fallback.
        if (!$activeKelasId && request()->has('tableFilters.kelas_id.value')) {
            $activeKelasId = request()->input('tableFilters.kelas_id.value');
        }

        if ($query && $activeKelasId) {
            // Panggil qualifyColumn pada objek $query
            $query->where($query->qualifyColumn('kelas_id'), $activeKelasId); // <-- PERBAIKAN
        } else {
            /** @var \App\Models\User $santri */
            $santri = auth()->user();
            if ($santri && method_exists($santri, 'kelasYangDiikuti')) {
                $kelasIdsSantri = $santri->kelasYangDiikuti()->pluck('kelas.id')->all();
                if (!empty($kelasIdsSantri)) {
                    // Panggil qualifyColumn pada objek $query
                    $query->whereIn($query->qualifyColumn('kelas_id'), $kelasIdsSantri); // <-- PERBAIKAN
                } else {
                    $query->whereRaw('1 = 0'); 
                }
            }
        }
        
        return $query;
    }

    public function getTitle(): string | Htmlable
    {
        if (isset($this->tableFilters['kelas_id']['value']) && !empty($this->tableFilters['kelas_id']['value'])) {
            $kelasId = $this->tableFilters['kelas_id']['value'];
            $kelas = Kelas::find($kelasId);
            if ($kelas) {
                return "Pertemuan Kelas " . $kelas->nama_kelas;
            }
        }
        // Fallback jika tidak ada filter kelas yang aktif, atau jika ingin judul umum
        // return "Daftar Pertemuan Anda"; 
        return static::getResource()::getPluralModelLabel(); // Default: "Pertemuans"
    }
}