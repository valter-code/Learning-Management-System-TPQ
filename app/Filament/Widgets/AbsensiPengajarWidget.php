<?php

namespace App\Filament\Pengajar\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use App\Models\AbsenPengajar;
use App\Models\User;
use App\Enums\StatusAbsensi; // Import Enum
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AbsensiPengajarWidget extends Widget 
{
    
    protected static string $view = 'filament.widgets.absensi-pengajar-widget';
    // Jika ingin widget tidak reload saat navigasi (Livewire v3 default)
    // protected bool $shouldPersist = true;

    
    protected int | string | array $columnSpan = 1; 
    

    
    public string $tanggalHariIni = '';
    public ?StatusAbsensi $statusPilihan = null; // Menggunakan Enum
    public string $keterangan = '';

    public bool $sudahAbsenHariIni = false;
    public ?StatusAbsensi $statusAbsenTercatat = null; // Menggunakan Enum
    public ?string $keteranganAbsenTercatat = null;
    public ?string $waktuMasukTercatat = null;

    // Untuk mengatur kolom grid di dashboard (opsional)
    // protected int | string | array $columnSpan = 'full'; // Atau sesuaikan, misal 1, 2, 'md' => 2

    // Listener untuk refresh jika ada event dari luar (opsional)
    // protected $listeners = ['absensiUpdated' => '$refresh'];

    public function mount(): void
    {
        $this->tanggalHariIni = Carbon::now()->translatedFormat('l, d F Y'); // Format lebih lengkap
        $this->loadStatusAbsensi();
    }

    public function loadStatusAbsensi(): void
    {
        /** @var User|null $pengajar */
        $pengajar = Auth::user();
        if (!$pengajar) {
            Log::warning('AbsensiPengajarWidget: Tidak ada pengguna yang login.');
            $this->resetAbsensiState();
            return;
        }

        $absen = AbsenPengajar::where('pengajar_id', $pengajar->id)
                              ->whereDate('tanggal', today())
                              ->first();

        if ($absen) {
            $this->sudahAbsenHariIni = true;
            $this->statusAbsenTercatat = $absen->status; // Ini sudah objek Enum
            $this->keteranganAbsenTercatat = $absen->keterangan;
            $this->waktuMasukTercatat = Carbon::parse($absen->created_at)->format('H:i');
            Log::info('AbsensiPengajarWidget: Status ditemukan.', ['status' => $this->statusAbsenTercatat?->value, 'keterangan' => $this->keteranganAbsenTercatat]);
        } else {
            $this->resetAbsensiState();
            Log::info('AbsensiPengajarWidget: Tidak ada record absensi ditemukan untuk hari ini.');
        }

        // Reset input sementara
        $this->statusPilihan = null;
        $this->keterangan = '';
        
        // Untuk memastikan komponen Livewire di-render ulang jika perlu (biasanya otomatis)
        // $this->dispatch('$refresh'); // Jika ada masalah dengan re-render
    }

    protected function resetAbsensiState(): void
    {
        $this->sudahAbsenHariIni = false;
        $this->statusAbsenTercatat = null;
        $this->keteranganAbsenTercatat = null;
        $this->waktuMasukTercatat = null;
    }

    public function pilihStatus(string $statusValue): void
    {
        if ($this->sudahAbsenHariIni) {
            return;
        }

        $this->statusPilihan = StatusAbsensi::tryFrom($statusValue);
        $this->keterangan = ''; // Reset keterangan

        if ($this->statusPilihan === StatusAbsensi::MASUK) {
            $this->submitAbsen(); // Langsung submit jika pilih 'Masuk'
        }
        // Perubahan properti publik akan otomatis memicu re-render Livewire
    }

    public function submitAbsen(): void
    {
        if ($this->sudahAbsenHariIni || !$this->statusPilihan) {
            Notification::make()->title('Aksi tidak valid atau Anda sudah absen.')->warning()->send();
            return;
        }

        /** @var User|null $pengajar */
        $pengajar = Auth::user();
        if (!$pengajar) {
            Notification::make()->title('Gagal')->body('Pengguna tidak ditemukan.')->danger()->send();
            return;
        }

        // Validasi keterangan untuk Izin atau Sakit
        if (in_array($this->statusPilihan, [StatusAbsensi::IZIN, StatusAbsensi::SAKIT]) && empty(trim($this->keterangan))) {
            Notification::make()
                ->title('Keterangan Diperlukan')
                ->body("Silakan isi keterangan untuk status {$this->statusPilihan->getLabel()}.")
                ->warning() // Lebih cocok warning daripada danger untuk validasi
                ->send();
            return;
        }

        Log::info('AbsensiPengajarWidget: Mencatat absensi untuk pengajar ID: ' . $pengajar->id, ['status' => $this->statusPilihan->value, 'keterangan' => $this->keterangan]);

        try {
            AbsenPengajar::updateOrCreate(
                ['pengajar_id' => $pengajar->id, 'tanggal' => today()], // Kondisi pencarian
                [ // Data untuk diisi atau diupdate
                    'status' => $this->statusPilihan,
                    'waktu_masuk' => ($this->statusPilihan === StatusAbsensi::MASUK) ? now() : null, // Hanya isi waktu_masuk jika status MASUK
                    'keterangan' => filled($this->keterangan) ? $this->keterangan : null,
                ]
            );

            Log::info('AbsensiPengajarWidget: Absensi berhasil dicatat.');
            Notification::make()
                ->title("Absensi {$this->statusPilihan->getLabel()} berhasil dicatat")
                ->success()
                ->send();

            $this->loadStatusAbsensi(); // Muat ulang status untuk update tampilan

        } catch (\Exception $e) {
            Log::error('AbsensiPengajarWidget: Gagal mencatat absensi.', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            Notification::make()
                ->title('Gagal Mencatat Absensi')
                ->body('Terjadi kesalahan internal: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    // Helper untuk mendapatkan semua opsi status absensi untuk view
    public function getStatusOptions(): array
    {
        return StatusAbsensi::cases();
    }
}