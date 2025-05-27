<?php

namespace App\Filament\Santri\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use App\Models\AbsensiSantri;
use App\Models\User;
use App\Enums\StatusAbsensi;
use App\Enums\UserRole; 
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AbsensiSantriMandiriWidget extends Widget
{
    protected static string $view = 'filament.santri.widgets.absensi-santri-mandiri-widget';
    protected int | string | array $columnSpan = 1;
    protected static ?int $sort = 1;

    public string $tanggalHariIni = '';
    public ?StatusAbsensi $statusPilihan = null; 
    public string $keterangan = '';

    public bool $sudahAbsenHariIni = false;
    public ?StatusAbsensi $statusAbsenTercatat = null; 
    public ?string $keteranganAbsenTercatat = null;
    public ?string $jamAbsenTercatat = null;

    protected Carbon $currentDate; 

    public function mount(): void
    {
        // Inisialisasi tanggal hari ini sekali di mount
        // Ini adalah tanggal yang akan digunakan untuk semua operasi "hari ini"
        $this->currentDate = Carbon::today(); 
        $this->tanggalHariIni = $this->currentDate->translatedFormat('l, d F Y');
        
        Log::debug("ABSENSI_DEBUG: [Mount] Widget diinisialisasi. Current local time: " . Carbon::now()->toDateTimeString() . " | currentDate (Carbon::today()): " . $this->currentDate->toDateString());
        
        $this->loadStatusAbsensiHarian();
    }

    public function loadStatusAbsensiHarian(): void
    {
        /** @var User|null $santri */
        $santri = Auth::user();
        
        $this->resetAbsensiState(); 
        
        if (!$santri || $santri->role !== UserRole::SANTRI) {
            Log::warning('ABSENSI_DEBUG: [loadStatusAbsensiHarian] Tidak ada santri yang login atau peran tidak sesuai.');
            return;
        }
        
        // Safety check, memastikan $this->currentDate selalu ada
        if (!isset($this->currentDate)) {
             $this->currentDate = Carbon::today(); 
             Log::warning('ABSENSI_DEBUG: [loadStatusAbsensiHarian] $currentDate tidak terinisialisasi, menginisialisasi ulang.');
        }

        Log::debug("ABSENSI_DEBUG: [loadStatusAbsensiHarian] Mencari absensi untuk santri ID: {$santri->id} pada tanggal: {$this->currentDate->toDateString()}");

        $absensi = AbsensiSantri::where('santri_id', $santri->id)
                                  ->whereDate('tanggal_absensi', $this->currentDate) 
                                  ->first();
        
        if ($absensi) {
            $this->sudahAbsenHariIni = true;
            $this->statusAbsenTercatat = $absensi->status_kehadiran;
            $this->keteranganAbsenTercatat = $absensi->keterangan;
            $this->jamAbsenTercatat = $absensi->waktu_masuk ? 
                                      $absensi->waktu_masuk->format('H:i') : 
                                      Carbon::parse($absensi->created_at)->format('H:i');
            Log::debug("ABSENSI_DEBUG: [loadStatusAbsensiHarian] Absensi DITEMUKAN untuk tanggal {$this->currentDate->toDateString()}. Status: {$this->statusAbsenTercatat->getLabel()}, Waktu Absen Tercatat: {$this->jamAbsenTercatat}, Record ID: {$absensi->id}");
            Log::debug("ABSENSI_DEBUG: [loadStatusAbsensiHarian] Record ditemukan di DB: tanggal_absensi={$absensi->tanggal_absensi->toDateString()}, created_at={$absensi->created_at->toDateTimeString()}");
        } else {
            $this->sudahAbsenHariIni = false; 
            Log::debug("ABSENSI_DEBUG: [loadStatusAbsensiHarian] TIDAK ADA absensi ditemukan untuk tanggal {$this->currentDate->toDateString()}. Santri bisa absen.");
        }
    }

    protected function resetAbsensiState(): void
    {
        $this->sudahAbsenHariIni = false;
        $this->statusAbsenTercatat = null;
        $this->keteranganAbsenTercatat = null;
        $this->jamAbsenTercatat = null;
        $this->statusPilihan = null;
        $this->keterangan = '';
    }

    public function pilihStatus(string $statusValue): void
    {
        Log::debug("ABSENSI_DEBUG: [pilihStatus] Dipanggil. \$sudahAbsenHariIni: " . ($this->sudahAbsenHariIni ? 'true' : 'false') . ", Status pilihan: " . $statusValue);

        if ($this->sudahAbsenHariIni) {
            Notification::make()->title('Anda sudah mencatat absensi untuk hari ini.')->warning()->send();
            return;
        }

        $this->statusPilihan = StatusAbsensi::tryFrom($statusValue); 
        $this->keterangan = ''; 

        if ($this->statusPilihan === StatusAbsensi::MASUK) {
            $this->submitAbsen();
        }
    }

    public function submitAbsen(): void
    {
        Log::debug("ABSENSI_DEBUG: [submitAbsen] Dipanggil. \$sudahAbsenHariIni: " . ($this->sudahAbsenHariIni ? 'true' : 'false') . ", Status pilihan: " . ($this->statusPilihan?->value ?? 'N/A'));

        if ($this->sudahAbsenHariIni || !$this->statusPilihan) {
            Notification::make()->title('Aksi tidak valid atau Anda sudah absen.')->warning()->send();
            return;
        }

        /** @var User|null $santri */
        $santri = Auth::user();
        if (!$santri) {
            Notification::make()->title('Gagal')->body('Pengguna tidak ditemukan.')->danger()->send();
            return;
        }

        if (in_array($this->statusPilihan, [StatusAbsensi::IZIN, StatusAbsensi::SAKIT]) && empty(trim($this->keterangan))) {
            Notification::make()
                ->title('Keterangan Diperlukan')
                ->body("Silakan isi keterangan untuk status {$this->statusPilihan->getLabel()}.")
                ->warning()
                ->send();
            return;
        }

        Log::info('ABSENSI_DEBUG: [submitAbsen] Mencatat absensi untuk santri ID: ' . $santri->id, ['status' => $this->statusPilihan->value, 'keterangan' => $this->keterangan]);

        try {
            if (!isset($this->currentDate)) {
                $this->currentDate = Carbon::today(); 
            }

            $waktuMasukUntukDb = ($this->statusPilihan === StatusAbsensi::MASUK) ? Carbon::now() : null; 

            Log::debug("ABSENSI_DEBUG: [submitAbsen] Mencoba updateOrCreate untuk santri ID: {$santri->id} pada tanggal: {$this->currentDate->toDateString()} dengan waktu masuk: " . ($waktuMasukUntukDb ? $waktuMasukUntukDb->format('H:i:s') : 'null'));

            AbsensiSantri::updateOrCreate(
                [ 
                    'santri_id' => $santri->id,
                    'tanggal_absensi' => $this->currentDate->toDateString(), 
                ],
                [ 
                    // 'pertemuan_id' => null, // BARIS INI DIHAPUS KARENA asumsi kolomnya tidak ada
                    'pengajar_id' => null, 
                    'status_kehadiran' => $this->statusPilihan, 
                    'keterangan' => filled($this->keterangan) ? $this->keterangan : null,
                    'waktu_masuk' => $waktuMasukUntukDb, 
                ]
            );
            
            Notification::make()->title("Absensi '{$this->statusPilihan->getLabel()}' berhasil dicatat untuk hari ini")->success()->send();
            Log::debug("ABSENSI_DEBUG: [submitAbsen] Absensi berhasil dicatat.");
            
            $this->loadStatusAbsensiHarian(); 
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000' || str_contains($e->getMessage(), 'Duplicate entry')) { 
                Log::error('ABSENSI_DEBUG: [submitAbsen] Santri sudah absen hari ini (Unique constraint violation).', [
                    'error' => $e->getMessage(), 
                    'santri_id' => $santri->id, 
                    'date' => $this->currentDate->toDateString()
                ]);
                Notification::make()->title('Sudah Absen')->body('Anda sudah mencatat absensi untuk hari ini.')->warning()->send();
            } else {
                Log::error('ABSENSI_DEBUG: [submitAbsen] Gagal mencatat absensi harian (Query Error).', [
                    'error' => $e->getMessage(), 
                    'santri_id' => $santri->id, 
                    'date' => $this->currentDate->toDateString(),
                    'trace' => $e->getTraceAsString()
                ]);
                Notification::make()->title('Gagal Mencatat Absensi')->body('Terjadi kesalahan database. Silakan coba lagi.')->danger()->send();
            }
        } catch (\Exception $e) {
            Log::error('ABSENSI_DEBUG: [submitAbsen] Kesalahan umum saat mencatat absensi harian.', [
                'error' => $e->getMessage(), 
                'santri_id' => $santri->id, 
                'date' => $this->currentDate->toDateString(),
                'trace' => $e->getTraceAsString()
            ]);
            Notification::make()->title('Gagal Mencatat Absensi')->body('Terjadi kesalahan internal. Silakan coba lagi.')->danger()->send();
        }
    }

    public function getStatusOptions(): array
    {
        return StatusAbsensi::cases();
    }
}