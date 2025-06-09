<?php

namespace App\Filament\Resources\PertemuanResource\Pages;

use App\Models\Kelas;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PertemuanResource;
use Illuminate\Database\Eloquent\Builder;

class ListPertemuans extends ListRecords
{
    protected static string $resource = PertemuanResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Santri tidak membuat pertemuan dari sini
    }

      // Metode getTableQuery Anda yang sudah ada untuk pre-filter dari widget
      protected function getTableQuery(): ?Builder
      {
          $query = parent::getTableQuery();
          
          // Cek apakah ada filter kelas_id yang dikirim via URL dari widget
          // atau dari filter yang aktif di tabel itu sendiri
          $activeKelasId = $this->tableFilters['kelas_id']['value'] ?? null;
          
          if (request()->has('tableFilters.kelas_id.value')) { // Untuk filter dari URL awal
               $activeKelasId = request()->input('tableFilters.kelas_id.value');
          }
  
          if ($query && $activeKelasId) {
              $query->where('kelas_id', $activeKelasId);
          }
          
          return $query;
      }

      // Override metode getTitle untuk judul dinamis
    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        // $this->tableFilters adalah properti Livewire yang menyimpan state filter tabel saat ini
        if (isset($this->tableFilters['kelas_id']['value']) && $this->tableFilters['kelas_id']['value']) {
            $kelasId = $this->tableFilters['kelas_id']['value'];
            $kelas = Kelas::find($kelasId);
            if ($kelas) {
                return "Pertemuan Kelas " . $kelas->nama_kelas;
            }
        }

        // Jika tidak ada filter kelas yang aktif, kembalikan judul default
        return parent::getTitle(); // Biasanya "Pertemuans" atau label plural resource
    }
}
