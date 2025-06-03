<?php

namespace App\Filament\Admin\Resources\SppResource\Pages;

use App\Filament\Admin\Resources\SppResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\User;
use App\Models\Spp;
use App\Models\Setting; // Pastikan Setting model di-import
use App\Enums\UserRole;
use App\Enums\StatusSpp;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Facades\DB;

class ListSpps extends ListRecords
{
    protected static string $resource = SppResource::class;

    public ?string $currentSppDefaultAmount = '';
    // Anda bisa menambahkan properti ini jika ingin menampilkan info rekening di header, tapi tidak wajib untuk form
    // public ?string $currentNamaBank = '';
    // public ?string $currentNomorRekening = '';
    // public ?string $currentAtasNamaRekening = '';


    public function mount(): void
    {
        parent::mount();
        $this->currentSppDefaultAmount = Setting::where('key', 'sistem.jumlah_spp_default')->first()?->value ?? config('sistem.jumlah_spp_default', '150000');
        // Jika properti di atas didefinisikan, isi di sini:
        // $this->currentNamaBank = Setting::where('key', 'pembayaran.nama_bank')->first()?->value ?? '';
        // $this->currentNomorRekening = Setting::where('key', 'pembayaran.nomor_rekening')->first()?->value ?? '';
        // $this->currentAtasNamaRekening = Setting::where('key', 'pembayaran.atas_nama_rekening')->first()?->value ?? '';
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('editBiayaSppBulanan')
                // ... (kode editBiayaSppBulanan tetap sama) ...
                ->label('Edit Biaya SPP Default')
                ->icon('heroicon-o-currency-dollar')
                ->color('primary')
                ->modalHeading('Atur Biaya SPP Bulanan Default')
                ->modalDescription('Biaya ini akan digunakan saat generate tagihan SPP dan akan mengupdate SEMUA tagihan yang BELUM DIBAYAR untuk TAHUN INI.')
                ->form([
                    Placeholder::make('info_spp_saat_ini')
                        ->label('Biaya SPP Default Saat Ini')
                        ->content(fn (): string => 'Rp ' . number_format((float)($this->currentSppDefaultAmount ?? 0), 0, ',', '.')),
                    TextInput::make('jumlah_spp_baru')
                        ->label('Biaya SPP Baru (Rp)')
                        ->numeric()->required()->prefix('Rp')
                        ->default((float)($this->currentSppDefaultAmount ?? 150000))
                        ->helperText('Masukkan hanya angka, contoh: 150000'),
                ])
                ->action(function (array $data) {
                    $newAmount = (float) $data['jumlah_spp_baru'];
                    DB::beginTransaction();
                    try {
                        Setting::updateOrCreate(
                            ['key' => 'sistem.jumlah_spp_default'],
                            ['value' => $newAmount]
                        );
                        $this->currentSppDefaultAmount = (string)$newAmount;
                        Log::info("Biaya SPP Default di settings diubah menjadi: {$newAmount} oleh User ID: " . Auth::id());
                        $tahunIni = Carbon::now()->year;
                        $updatedCount = Spp::whereIn('status_pembayaran', [StatusSpp::BELUM_BAYAR->value, StatusSpp::TERLAMBAT->value])
                            ->where('tahun', $tahunIni)
                            ->update(['jumlah_bayar' => $newAmount, 'biaya_bulanan' => $newAmount]);
                        Log::info("Sebanyak {$updatedCount} record SPP yang belum bayar/terlambat untuk tahun {$tahunIni} diupdate jumlah dan biaya bulanannya menjadi {$newAmount}.");
                        Notification::make()
                            ->title('Biaya SPP Default Diperbarui')
                            ->body("Biaya default diubah menjadi Rp " . number_format($newAmount, 0, ',', '.') . ". Sebanyak {$updatedCount} tagihan SPP (termasuk biaya bulanan) yang belum dibayar/terlambat untuk tahun ini juga telah diupdate.")
                            ->success()
                            ->send();
                        DB::commit();
                        return redirect(static::getUrl());
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error("Gagal update biaya SPP default atau tagihan: " . $e->getMessage());
                        Notification::make()
                            ->title('Gagal Memperbarui Biaya SPP')
                            ->body("Terjadi kesalahan: " . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            // --- AKSI BARU UNTUK EDIT INFO REKENING ---
            Actions\Action::make('editInformasiRekening')
                ->label('Edit Info Rekening')
                ->icon('heroicon-o-banknotes')
                ->color('success') // Atau warna lain yang sesuai
                ->modalHeading('Atur Informasi Rekening Pembayaran')
                ->modalDescription('Informasi ini akan digunakan pada email tagihan SPP.')
                ->form([
                    TextInput::make('nama_bank')
                        ->label('Nama Bank')
                        ->default(fn () => Setting::where('key', 'pembayaran.nama_bank')->first()?->value ?? '')
                        ->required()
                        ->helperText('Contoh: Bank Syariah Indonesia (BSI), Bank Mandiri'),
                    TextInput::make('nomor_rekening')
                        ->label('Nomor Rekening')
                        ->default(fn () => Setting::where('key', 'pembayaran.nomor_rekening')->first()?->value ?? '')
                        ->required()
                        ->helperText('Contoh: 1234567890'),
                    TextInput::make('atas_nama_rekening')
                        ->label('Atas Nama Rekening')
                        ->default(fn () => Setting::where('key', 'pembayaran.atas_nama_rekening')->first()?->value ?? '')
                        ->required()
                        ->helperText('Contoh: Yayasan Pendidikan Amanah Umat'),
                ])
                ->action(function (array $data) {
                    DB::beginTransaction();
                    try {
                        Setting::updateOrCreate(
                            ['key' => 'pembayaran.nama_bank'],
                            ['value' => $data['nama_bank']]
                        );
                        Setting::updateOrCreate(
                            ['key' => 'pembayaran.nomor_rekening'],
                            ['value' => $data['nomor_rekening']]
                        );
                        Setting::updateOrCreate(
                            ['key' => 'pembayaran.atas_nama_rekening'],
                            ['value' => $data['atas_nama_rekening']]
                        );

                        Log::info("Informasi rekening pembayaran diupdate oleh User ID: " . Auth::id() . ". Data: " . json_encode($data));
                        DB::commit();

                        Notification::make()
                            ->title('Informasi Rekening Diperbarui')
                            ->body('Detail rekening bank untuk pembayaran telah berhasil disimpan.')
                            ->success()
                            ->send();
                        
                        // Jika Anda mendefinisikan properti di atas, update juga di sini
                        // $this->currentNamaBank = $data['nama_bank'];
                        // $this->currentNomorRekening = $data['nomor_rekening'];
                        // $this->currentAtasNamaRekening = $data['atas_nama_rekening'];
                        // return redirect(static::getUrl()); // Tidak perlu redirect jika tidak ada perubahan UI langsung di halaman ListSpps
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error("Gagal update informasi rekening: " . $e->getMessage(), ['exception' => $e]);
                        Notification::make()
                            ->title('Gagal Memperbarui Informasi Rekening')
                            ->body("Terjadi kesalahan: " . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            // --- AKHIR AKSI BARU ---

            Actions\Action::make('sinkronkanSppTahunIni')
                 // ... (kode sinkronkanSppTahunIni tetap sama) ...
                ->label('Sinkronkan SPP Tahun Ini')
                ->action(function () {
                    $tahunIni = Carbon::now()->year;
                    $bulanSaatIni = Carbon::now()->month;
                    $namaBulanIni = Carbon::now()->locale('id')->monthName;
                    $jumlahSppDefault = (float)(Setting::where('key', 'sistem.jumlah_spp_default')->first()?->value ?? config('sistem.jumlah_spp_default', 150000));
                    $userId = Auth::id();
                    Log::info("Memulai sinkronisasi SPP untuk Tahun: {$tahunIni} oleh User ID: {$userId} dengan biaya default: {$jumlahSppDefault}");
                    $santriAktif = User::where('role', UserRole::SANTRI)->get();
                    if ($santriAktif->isEmpty()) {
                        Notification::make()->title('Info Sinkronisasi SPP')
                            ->body("Tidak ada santri aktif yang ditemukan.")->warning()->send();
                        return;
                    }
                    $generatedCount = 0; $updatedCount = 0; $skippedCount = 0;
                    DB::beginTransaction();
                    try {
                        foreach ($santriAktif as $santri) {
                            for ($bulan = 1; $bulan <= $bulanSaatIni; $bulan++) {
                                $sppRecord = Spp::where('santri_id', $santri->id)
                                                ->where('bulan', $bulan)->where('tahun', $tahunIni)->first();
                                if ($sppRecord) {
                                    $needsUpdate = false;
                                    if (in_array($sppRecord->status_pembayaran, [StatusSpp::BELUM_BAYAR, StatusSpp::TERLAMBAT])) {
                                        if (is_null($sppRecord->biaya_bulanan) || $sppRecord->biaya_bulanan == 0) {
                                            $sppRecord->biaya_bulanan = $jumlahSppDefault; $needsUpdate = true;
                                        }
                                        if (is_null($sppRecord->jumlah_bayar) || $sppRecord->jumlah_bayar == 0) {
                                            $sppRecord->jumlah_bayar = $jumlahSppDefault; $needsUpdate = true;
                                        }
                                    }
                                    if ($needsUpdate) {
                                        $sppRecord->pencatat_id = $userId; $sppRecord->save(); $updatedCount++;
                                    } else { $skippedCount++; }
                                } else {
                                    Spp::create([
                                        'santri_id' => $santri->id, 'bulan' => $bulan, 'tahun' => $tahunIni,
                                        'biaya_bulanan' => $jumlahSppDefault, 'jumlah_bayar' => $jumlahSppDefault,
                                        'status_pembayaran' => StatusSpp::BELUM_BAYAR->value, 'pencatat_id' => $userId,
                                    ]);
                                    $generatedCount++;
                                }
                            }
                        }
                        DB::commit();
                        $messageParts = [];
                        if ($generatedCount > 0) { $messageParts[] = "Berhasil membuat {$generatedCount} data SPP baru"; }
                        if ($updatedCount > 0) { $messageParts[] = "memperbarui {$updatedCount} data SPP yang ada"; }
                        $notificationTitle = 'Sinkronisasi SPP Selesai'; $notificationBody = "";
                        if (!empty($messageParts)) {
                            $notificationBody = implode(" dan ", $messageParts) . " hingga bulan {$namaBulanIni} {$tahunIni}.";
                             if ($skippedCount > 0) { $notificationBody .= " {$skippedCount} data SPP lainnya sudah sesuai."; }
                            Notification::make()->title($notificationTitle)->body($notificationBody)->success()->send();
                        } elseif ($skippedCount > 0) {
                            Notification::make()->title('Info Sinkronisasi SPP')
                                ->body("Semua data SPP untuk santri aktif hingga bulan {$namaBulanIni} {$tahunIni} sudah lengkap dan sesuai ({$skippedCount} data).")->info()->send();
                        } else {
                            Notification::make()->title('Tidak Ada Perubahan')
                                ->body("Tidak ada data SPP baru yang dibuat atau diperbarui (kemungkinan tidak ada santri aktif atau semua sudah sesuai).")->warning()->send();
                        }
                        return redirect(static::getUrl());
                    } catch (\Exception $e) {
                        DB::rollBack(); Log::error("Gagal sinkronisasi SPP: " . $e->getMessage(), ['exception' => $e]);
                        Notification::make()->title('Gagal Sinkronisasi SPP')->body("Terjadi kesalahan: " . $e->getMessage())->danger()->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Sinkronkan Tagihan SPP Tahun Ini?')
                ->modalDescription('Aksi ini akan memeriksa, membuat data tagihan SPP baru jika belum ada, dan memperbaiki data SPP yang sudah ada (jika biaya bulanan/tagihan masih Rp 0 dan belum bayar) untuk semua santri aktif dari bulan Januari hingga bulan saat ini. Lanjutkan?'),
            
            Actions\CreateAction::make()
                 // ... (kode CreateAction tetap sama) ...
                 ->mutateFormDataUsing(function (array $data): array {
                    $data['pencatat_id'] = Auth::id(); 
                    if (isset($data['status_pembayaran']) && $data['status_pembayaran'] === StatusSpp::SUDAH_BAYAR->value && empty($data['tanggal_bayar'])) {
                        $data['tanggal_bayar'] = now();
                    }
                    if (empty($data['biaya_bulanan']) && !empty($data['jumlah_bayar'])) {
                        $data['biaya_bulanan'] = $data['jumlah_bayar'];
                    } elseif (!empty($data['biaya_bulanan']) && empty($data['jumlah_bayar'])) {
                        $data['jumlah_bayar'] = $data['biaya_bulanan'];
                    }
                    return $data;
                }),
        ];
    }
}