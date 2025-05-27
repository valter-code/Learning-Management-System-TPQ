<?php

namespace App\Filament\Santri\Pages;

use Filament\Pages\Page;
use App\Models\User;
use App\Models\Kelas; // Import Kelas
use App\Models\PertemuanTugasItem;
use App\Models\PengumpulanTugas;
use App\Enums\StatusPengumpulanTugasEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon; // Import Carbon

class DaftarTugasSantriPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static string $view = 'filament.santri.pages.daftar-tugas-santri-page';
    protected static ?string $navigationLabel = 'Tugas Anda';
    protected static ?string $title = 'Daftar Tugas Anda';
    protected static ?string $slug = 'daftar-tugas-anda';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Kelas';


    public string $filterStatus = '';
    public ?string $filterKelasId = ''; // Properti baru untuk filter kelas
    public Collection $semuaTugasDenganStatus;
    public Collection $kelasYangDiikuti; // Untuk mengisi opsi filter kelas

    // Simpan filter di URL
    protected $queryString = [
        'filterStatus' => ['except' => ''],
        'filterKelasId' => ['except' => ''],
    ];

    public function mount(): void
    {
        /** @var User $santri */
        $santri = Auth::user();
        if ($santri && method_exists($santri, 'kelasYangDiikuti')) {
            $this->kelasYangDiikuti = $santri->kelasYangDiikuti()->orderBy('nama_kelas')->get();
        } else {
            $this->kelasYangDiikuti = collect();
        }
        $this->loadTugasData();
    }

    protected function loadTugasData(): void
    {
        /** @var User $santri */
        $santri = Auth::user();
        if (!$santri) {
            $this->semuaTugasDenganStatus = collect();
            return;
        }

        $kelasIdsSantri = $this->kelasYangDiikuti->pluck('id')->all();

        if (empty($kelasIdsSantri)) {
            $this->semuaTugasDenganStatus = collect();
            return;
        }

        $queryTugas = PertemuanTugasItem::query()
            ->whereHas('pertemuan.kelas', function ($q) use ($kelasIdsSantri) {
                $q->whereIn('id', $kelasIdsSantri); // Ambil tugas dari semua kelas yang diikuti santri
            })
            ->with(['pertemuan.kelas', 'pengumpulanTugas' => function ($q) use ($santri) {
                $q->where('santri_id', $santri->id);
            }])
            ->orderByDesc(
                \App\Models\Pertemuan::select('tanggal_pertemuan') // Namespace lengkap untuk Pertemuan
                    ->whereColumn('pertemuan.id', 'pertemuan_tugas_items.pertemuan_id')
                    ->orderByDesc('tanggal_pertemuan')
                    ->limit(1)
            )
            ->orderBy('created_at', 'desc');

        // Terapkan filter 
        if (!empty($this->filterKelasId)) {
            $queryTugas->whereHas('pertemuan', function ($q) {
                $q->where('kelas_id', $this->filterKelasId);
            });
        }

        // Terapkan filter status
        if ($this->filterStatus) {
            if ($this->filterStatus === 'ditugaskan') {
                // Tugas yang belum memiliki entri pengumpulan oleh santri ini
                $queryTugas->whereDoesntHave('pengumpulanTugas', function ($q) use ($santri) {
                    $q->where('santri_id', $santri->id);
                });
            } else {
                // Tugas yang memiliki entri pengumpulan dengan status tertentu
                $queryTugas->whereHas('pengumpulanTugas', function ($q) use ($santri) {
                    $q->where('santri_id', $santri->id)
                      ->where('status_pengumpulan', $this->filterStatus);
                });
            }
        }
        
        $tugasItems = $queryTugas->get(); 

        $this->semuaTugasDenganStatus = $tugasItems->map(function ($tugasItem) {
            $pengumpulan = $tugasItem->pengumpulanTugas->first();
            $status = 'Ditugaskan';
            $nilai = null;
            $warna = 'gray'; // Default untuk 'Ditugaskan'

            if ($pengumpulan) {
                $statusEnum = $pengumpulan->status_pengumpulan;
                $status = $statusEnum->getLabel();
                $nilai = $pengumpulan->nilai;
                $warna = $statusEnum->getColor();
                if ($statusEnum == StatusPengumpulanTugasEnum::DIKUMPULKAN && $tugasItem->deadline_tugas && now()->gt($tugasItem->deadline_tugas)) {
                    $status = 'Terlambat Dikumpulkan';
                    $warna = 'warning';
                }
            } elseif ($tugasItem->deadline_tugas && now()->gt($tugasItem->deadline_tugas)) {
                $status = 'Terlewat'; 
                $warna = 'danger';
            }

            return (object) [
                'id_tugas_item' => $tugasItem->id,
                'judul_tugas' => $tugasItem->judul_tugas,
                'pertemuan_id' => $tugasItem->pertemuan_id,
                'judul_pertemuan' => $tugasItem->pertemuan->judul_pertemuan,
                'nama_kelas' => $tugasItem->pertemuan->kelas->nama_kelas,
                'deadline' => $tugasItem->deadline_tugas ? Carbon::parse($tugasItem->deadline_tugas)->translatedFormat('d M Y, H:i') : 'Tidak ada tenggat',
                'status_pengumpulan_label' => $status,
                'status_pengumpulan_warna' => $warna,
                'nilai' => $nilai,
                'poin_maksimal' => $tugasItem->poin_maksimal ?? 100,
            ];
        });
        
    }
    
    public function updatedFilterStatus(): void
    {
        $this->loadTugasData();
    }

    public function updatedFilterKelasId(): void // Metode untuk update filter kelas
    {
        $this->loadTugasData();
    }

    public function getStatusFilterOptionsProperty(): array
    {
        return [
            '' => 'Semua Status',
            'ditugaskan' => 'Ditugaskan (Belum Dikerjakan)',
            StatusPengumpulanTugasEnum::DIKUMPULKAN->value => StatusPengumpulanTugasEnum::DIKUMPULKAN->getLabel(),
            StatusPengumpulanTugasEnum::TERLAMBAT->value => StatusPengumpulanTugasEnum::TERLAMBAT->getLabel(),
            StatusPengumpulanTugasEnum::DINILAI->value => StatusPengumpulanTugasEnum::DINILAI->getLabel(),
        ];
    }

    // Computed property untuk opsi filter kelas
    public function getKelasFilterOptionsProperty(): Collection
    {
        return $this->kelasYangDiikuti;
    }
}
