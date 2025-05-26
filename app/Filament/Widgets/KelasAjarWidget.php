<?php

namespace App\Filament\Widgets; // Sesuaikan namespace jika widget Anda ada di panel Pengajar

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\Kelas; // Pastikan path ke model Kelas benar
use App\Models\User; // Pastikan path ke model User (Pengajar) benar
use Illuminate\Support\Facades\Log; // Untuk logging jika terjadi error

class KelasAjarWidget extends Widget
{
    protected static string $resourceSlug = 'pertemuan'; 
    protected static string $view = 'filament.widgets.kelas-ajar-widget'; // Sesuaikan path view jika perlu

    protected int | string | array $columnSpan = 'full'; // Widget mengambil lebar penuh

    protected static ?int $sort = 1;

    public function getKelasDataProperty(): array
    {
        /** @var \App\Models\User|null $loggedInPengajar */
        $loggedInPengajar = Auth::user();
        $kelasData = [];

        if (!$loggedInPengajar) {
            Log::warning('KelasAjarWidget: Tidak ada pengajar yang login.');
            return [];
        }

        $daftarKelas = collect();
        // Prioritaskan relasi many-to-many jika itu sistem utama Anda
        if (method_exists($loggedInPengajar, 'mengajarDiKelas')) {
            Log::info('KelasAjarWidget: Mencoba mengambil kelas via mengajarDiKelas() untuk Pengajar ID: ' . $loggedInPengajar->id);
            $daftarKelas = $loggedInPengajar->mengajarDiKelas()->orderBy('nama_kelas')->get();
            if ($daftarKelas->isNotEmpty()) {
                Log::info('KelasAjarWidget: Ditemukan ' . $daftarKelas->count() . ' kelas via mengajarDiKelas().');
            }
        }
        
        // Fallback ke wali kelas jika sistem Anda masih mendukungnya ATAU jika mengajarDiKelas kosong
        if ($daftarKelas->isEmpty() && method_exists($loggedInPengajar, 'kelasSebagaiWali')) {
             Log::info('KelasAjarWidget: Tidak ada kelas dari mengajarDiKelas(), mencoba via kelasSebagaiWali() untuk Pengajar ID: ' . $loggedInPengajar->id);
            $daftarKelas = $loggedInPengajar->kelasSebagaiWali()->orderBy('nama_kelas')->get();
            if ($daftarKelas->isNotEmpty()) {
                Log::info('KelasAjarWidget: Ditemukan ' . $daftarKelas->count() . ' kelas via kelasSebagaiWali().');
            }
        }
        
        if ($daftarKelas->isEmpty()) {
            Log::error('KelasAjarWidget: Pengajar ID ' . $loggedInPengajar->id . ' tidak terhubung ke kelas manapun via relasi yang ada.');
        }

        foreach ($daftarKelas as $kelas) {
            $studentCount = 0;
            Log::info("KelasAjarWidget: Memproses Kelas ID {$kelas->id} - Nama: {$kelas->nama_kelas}");
            try {
                // Menggunakan relasi 'santris' (plural) dari model Kelas
                if (method_exists($kelas, 'santris')) {
                    $studentCount = $kelas->santris()->count();
                    Log::info("KelasAjarWidget: Kelas ID {$kelas->id} - Jumlah Santri (via santris()): {$studentCount}");
                    // Jika count masih 0, coba dump relasinya untuk melihat apakah ada record
                    if ($studentCount === 0 && $kelas->santris()->exists()) {
                        Log::warning("KelasAjarWidget: Kelas ID {$kelas->id} - santris()->count() adalah 0, tetapi santris()->exists() adalah true. Cek data pivot.");
                    } elseif ($studentCount === 0) {
                        Log::info("KelasAjarWidget: Kelas ID {$kelas->id} - Tidak ada santri ditemukan via relasi santris().");
                    }
                } else {
                    Log::warning("KelasAjarWidget: Relasi 'santris' tidak ditemukan pada model Kelas ID {$kelas->id}.");
                }
            } catch (\Exception $e) {
                Log::error("Error counting students for Kelas ID {$kelas->id} in KelasAjarWidget: " . $e->getMessage());
            }

            $panelId = 'pengajar';
            $resourceSlug = 'pertemuan'; // Atau 'pertemuans' sesuai konfigurasi Anda

            $meetingPageUrl = '#';
            try {
                $meetingPageUrl = route("filament.{$panelId}.resources.{$resourceSlug}.index", ['tableFilters[kelas_id][value]' => $kelas->id]);
            } catch (\Exception $e) {
                Log::error("Error generating meetingPageUrl for Kelas ID {$kelas->id} in KelasAjarWidget: " . $e->getMessage());
            }

            $kelasData[] = [
                'id' => $kelas->id,
                'name' => "Kelas: " . ($kelas->nama_kelas ?? 'Tanpa Nama'),
                'description' => $kelas->deskripsi ?? 'Tidak ada deskripsi.',
                'studentCount' => $studentCount,
                'nextMeeting' => $this->getPertemuanBerikutnyaInfo($kelas),
                'icon' => $this->getIconUntukKelas($kelas->nama_kelas ?? ''),
                'color' => $this->getColorUntukKelas($kelas->nama_kelas ?? ''),
                'meetingPageUrl' => $meetingPageUrl
            ];
        }

        return $kelasData;
    }

    protected function getPertemuanBerikutnyaInfo(Kelas $kelas): string
    {
        if (method_exists($kelas, 'pertemuans')) {
            try {
                $pertemuanBerikutnya = $kelas->pertemuans()
                                         ->where('tanggal_pertemuan', '>=', now()->toDateString())
                                         ->orderBy('tanggal_pertemuan')
                                         ->orderBy('waktu_mulai')
                                         ->first();
                if ($pertemuanBerikutnya) {
                    $judul = $pertemuanBerikutnya->judul_pertemuan ?? $pertemuanBerikutnya->judul_pertemuans ?? 'Tanpa Judul';
                    return \Carbon\Carbon::parse($pertemuanBerikutnya->tanggal_pertemuan)->format('d M Y') . ' - ' . $judul;
                }
            } catch (\Exception $e) {
                Log::error("Error fetching next meeting for Kelas ID {$kelas->id} in KelasAjarWidget: " . $e->getMessage());
                return 'Error data pertemuan';
            }
        }
        return 'Belum dijadwalkan';
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

    // protected function getIconUntukKelas(string $namaKelasAngka): string
    // {
    //     $angkaKelas = (int) $namaKelasAngka;
    //     if ($angkaKelas >= 7 && $angkaKelas <= 9) return 'fas fa-pencil-alt text-green-500';
    //     if ($angkaKelas >= 10 && $angkaKelas <= 12) return 'fas fa-book-open text-blue-500';
    //     if ($angkaKelas < 7) return 'fas fa-child text-yellow-500';
    //     return 'fas fa-chalkboard-teacher text-gray-500';
    // }

    // protected function getColorUntukKelas(string $namaKelasAngka): string
    // {
    //     $angkaKelas = (int) $namaKelasAngka;
    //     if ($angkaKelas >= 7 && $angkaKelas <= 9) return 'bg-green-500';
    //     if ($angkaKelas >= 10 && $angkaKelas <= 12) return 'bg-blue-500';
    //     if ($angkaKelas < 7) return 'bg-yellow-600';
    //     return 'bg-gray-500';
    // }
}
