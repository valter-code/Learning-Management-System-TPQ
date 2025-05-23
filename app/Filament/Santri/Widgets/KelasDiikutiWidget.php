<?php

namespace App\Filament\Santri\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Kelas; // Import model Kelas
use App\Models\Pertemuan; // Import model Pertemuan
use App\Filament\Santri\Resources\PertemuanResource; // Resource Pertemuan di panel Santri
use Illuminate\Support\Facades\Log;


class KelasDiikutiWidget extends Widget
{
    protected static string $view = 'filament.santri.widgets.kelas-diikuti-widget';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2; // Atur urutan jika ada widget lain

    // Properti ini akan otomatis diisi dan bisa diakses di view Blade
    public array $kelasData = [];

    public function mount(): void
    {
        $this->loadKelasData();
    }

    public function loadKelasData(): void
    {
        /** @var \App\Models\User|null $santri */
        $santri = Auth::user();
        $tempKelasData = [];

        if (!$santri || !method_exists($santri, 'kelasYangDiikuti')) {
            Log::warning('KelasDiikutiWidget: Santri tidak login atau tidak ada relasi kelasYangDiikuti.');
            $this->kelasData = [];
            return;
        }

        $daftarKelas = $santri->kelasYangDiikuti()->orderBy('nama_kelas')->get();

        foreach ($daftarKelas as $kelas) {
            $pertemuanBerikutnyaInfo = 'Belum dijadwalkan';
            if (method_exists($kelas, 'pertemuans')) {
                try {
                    $pertemuanBerikutnya = $kelas->pertemuans()
                                             ->where('tanggal_pertemuan', '>=', now()->toDateString())
                                             ->orderBy('tanggal_pertemuan')
                                             ->orderBy('waktu_mulai')
                                             ->first();
                    if ($pertemuanBerikutnya) {
                        $judul = $pertemuanBerikutnya->judul_pertemuan ?? 'Tanpa Judul';
                        $pertemuanBerikutnyaInfo = \Carbon\Carbon::parse($pertemuanBerikutnya->tanggal_pertemuan)->format('d M Y') . ' - ' . $judul;
                    }
                } catch (\Exception $e) {
                    Log::error("Error fetching next meeting for Kelas ID {$kelas->id} in KelasDiikutiWidget: " . $e->getMessage());
                    $pertemuanBerikutnyaInfo = 'Error data pertemuan';
                }
            }
            
            // URL untuk melihat daftar pertemuan kelas ini di panel Santri
            // Pastikan filter di PertemuanResource (Santri) bernama 'kelas_id'
            $pertemuanPageUrl = PertemuanResource::getUrl('index', ['tableFilters[kelas_id][value]' => $kelas->id]);


            $tempKelasData[] = [
                'id' => $kelas->id,
                'name' => "Kelas: " . ($kelas->nama_kelas ?? 'Tanpa Nama'),
                'description' => $kelas->deskripsi ?? 'Tidak ada deskripsi.',
                'studentCount' => method_exists($kelas, 'santris') ? $kelas->santris()->count() : 0, // Jumlah teman sekelas
                'nextMeeting' => $pertemuanBerikutnyaInfo,
                // Fungsi getIconUntukKelas dan getColorUntukKelas bisa Anda pindahkan ke Trait atau Helper jika dipakai di banyak tempat
                // atau definisikan lagi di sini jika hanya untuk widget ini.
                'icon' => $this->getIconUntukKelas($kelas->nama_kelas ?? ''), 
                'color' => $this->getColorUntukKelas($kelas->nama_kelas ?? ''),
                'pertemuanPageUrl' => $pertemuanPageUrl,
            ];
        }
        $this->kelasData = $tempKelasData;
    }

    // Copy-paste atau refactor metode ini dari KelasAjarWidget jika sama
    protected function getIconUntukKelas(string $namaKelas): string
    {
        // Logika untuk ikon berdasarkan nama kelas, contoh:
        if (str_contains(strtolower($namaKelas), 'a')) return 'heroicon-o-academic-cap';
        return 'heroicon-o-book-open';
    }

    protected function getColorUntukKelas(string $namaKelas): string
    {
        // Logika untuk warna berdasarkan nama kelas
        if (str_contains(strtolower($namaKelas), 'a')) return 'bg-blue-500';
        return 'bg-green-500';
    }
}