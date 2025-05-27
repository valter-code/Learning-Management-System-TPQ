<?php

namespace App\Filament\Admin\Resources\SppResource\Pages;

use App\Filament\Admin\Resources\SppResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\User;
use App\Models\Spp;
use App\Enums\UserRole;
use App\Enums\StatusSpp;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ListSpps extends ListRecords
{
    protected static string $resource = SppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['pencatat_id'] = Auth::id(); // Diubah ke pencatat_id
                    if (isset($data['status_pembayaran']) && $data['status_pembayaran'] === StatusSpp::SUDAH_BAYAR->value && empty($data['tanggal_bayar'])) {
                        $data['tanggal_bayar'] = now();
                    }
                    return $data;
                }),
            Actions\Action::make('generateSppBulanIni')
                ->label('Generate SPP Bulan Ini')
                ->icon('heroicon-o-document-plus')
                ->color('success')
                ->action(function () {
                    $bulanIni = Carbon::now()->month;
                    $tahunIni = Carbon::now()->year;
                    $jumlahSppDefault = config('sistem.jumlah_spp_default', 150000); 

                    Log::info("Memulai generate SPP untuk Bulan: {$bulanIni}, Tahun: {$tahunIni} oleh User ID: " . Auth::id());

                    $santriAktif = User::where('role', UserRole::SANTRI)->get();
                    
                    Log::info("Jumlah santri aktif ditemukan: " . $santriAktif->count());

                    if ($santriAktif->isEmpty()) { /* ... notifikasi ... */ return; }

                    $generatedCount = 0;
                    $skippedCount = 0;

                    foreach ($santriAktif as $santri) {
                        Log::info("Memproses santri ID: {$santri->id} - Nama: {$santri->name}");
                        $sppExists = Spp::where('santri_id', $santri->id)
                                        ->where('bulan', $bulanIni)
                                        ->where('tahun', $tahunIni)
                                        ->exists();

                        if (!$sppExists) {
                            Log::info("Membuat SPP baru untuk santri ID: {$santri->id}");
                            Spp::create([
                                'santri_id' => $santri->id,
                                'bulan' => $bulanIni,
                                'tahun' => $tahunIni,
                                'jumlah_bayar' => $jumlahSppDefault,
                                'status_pembayaran' => StatusSpp::BELUM_BAYAR->value,
                                'pencatat_id' => Auth::id(), // Diubah ke pencatat_id
                            ]);
                            $generatedCount++;
                        } else {
                            Log::info("SPP sudah ada untuk santri ID: {$santri->id} bulan ini.");
                            $skippedCount++;
                        }
                    }
                    // ... (logika notifikasi generate) ...
                    $this->dispatch('refreshTable');
                })
                ->requiresConfirmation()
                ->modalHeading('Generate SPP untuk Bulan Ini?')
                ->modalDescription('Aksi ini akan membuat data tagihan SPP untuk semua santri aktif yang belum memiliki tagihan di bulan ' . Carbon::now()->translatedFormat('F Y') . '. Lanjutkan?'),
        ];
    }
}
