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
    public ?string $statusPilihan = null;
    public string $keterangan = '';

    public bool $bisaAbsenHariIni = false; 
    public bool $sudahAbsenHariIni = false;
    public ?string $statusAbsenTercatat = null;
    public ?string $keteranganAbsenTercatat = null;
    public ?string $jamAbsenTercatat = null;

    public function mount(): void
    {
        $this->tanggalHariIni = Carbon::now()->translatedFormat('l, d F Y');
        $this->loadStatusAbsensiHarian();
    }

    public function loadStatusAbsensiHarian(): void
    {
        /** @var User|null $santri */
        $santri = Auth::user();
        
        $this->bisaAbsenHariIni = false;
        $this->sudahAbsenHariIni = false;
        $this->statusAbsenTercatat = null;
        $this->keteranganAbsenTercatat = null;
        $this->jamAbsenTercatat = null;
        $this->statusPilihan = null;
        $this->keterangan = '';

        if (!$santri || $santri->role !== UserRole::SANTRI) {
            return;
        }
        
        // Santri bisa absen jika dia adalah santri (sudah dicek di atas)
        // dan belum absen hari ini.
        // Tidak ada lagi pengecekan kelas di sini.

        $absensi = AbsensiSantri::where('santri_id', $santri->id)
                                  ->whereDate('tanggal_absensi', today())
                                  ->first();
        if ($absensi) {
            $this->sudahAbsenHariIni = true;
            // $this->bisaAbsenHariIni tetap false karena sudah absen
            $this->statusAbsenTercatat = $absensi->status_kehadiran->getLabel();
            $this->keteranganAbsenTercatat = $absensi->keterangan;
            $this->jamAbsenTercatat = Carbon::parse($absensi->created_at)->format('H:i');
        } else {
            $this->sudahAbsenHariIni = false;
            $this->bisaAbsenHariIni = true; // Bisa absen jika belum
        }
    }

    public function pilihStatus(string $statusValue): void
    {
        if (!$this->bisaAbsenHariIni) return;

        $this->statusPilihan = $statusValue;
        $this->keterangan = '';

        if (StatusAbsensi::tryFrom($statusValue) === StatusAbsensi::MASUK) {
            $this->submitAbsen();
        }
    }

    public function submitAbsen(): void
    {
        /** @var User|null $santri */
        $santri = Auth::user();

        if (!$this->bisaAbsenHariIni || !$this->statusPilihan || !$santri) {
            Notification::make()->title('Aksi tidak valid.')->warning()->send();
            return;
        }

        $statusEnum = StatusAbsensi::tryFrom($this->statusPilihan);
        if (!$statusEnum) { /* ... validasi ... */ return; }
        if (in_array($statusEnum, [StatusAbsensi::IZIN, StatusAbsensi::SAKIT]) && empty(trim($this->keterangan))) { /* ... validasi ... */ return; }

        try {
            AbsensiSantri::updateOrCreate(
                [ 
                    'santri_id' => $santri->id,
                    'tanggal_absensi' => today(),
                ],
                [ 
                    // 'kelas_id' => null, // Dihapus dari fillable, jadi tidak perlu diset
                    'pengajar_id' => null, 
                    'status_kehadiran' => $statusEnum->value,
                    'keterangan' => filled($this->keterangan) ? $this->keterangan : null,
                ]
            );
            Notification::make()->title("Absensi '{$statusEnum->getLabel()}' berhasil dicatat untuk hari ini")->success()->send();
            $this->loadStatusAbsensiHarian(); 
        } catch (\Exception $e) {
            Log::error('AbsensiSantriMandiriWidget: Gagal mencatat absensi harian.', ['error' => $e->getMessage(), 'santri_id' => $santri->id]);
            Notification::make()->title('Gagal Mencatat Absensi')->body('Terjadi kesalahan internal. Silakan coba lagi.')->danger()->send();
        }
    }

    public function getStatusOptions(): array
    {
        return [
            StatusAbsensi::MASUK,
            StatusAbsensi::IZIN,
            StatusAbsensi::SAKIT,
        ];
    }
}
