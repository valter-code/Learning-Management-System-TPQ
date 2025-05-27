<?php

namespace App\Filament\Santri\Pages;

use Filament\Pages\Page;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Pertemuan;
use App\Models\PertemuanTugasItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class TugasKelasPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string $view = 'filament.santri.pages.tugas-kelas-page';
    protected static ?string $navigationLabel = 'Tugas & Materi Kelas';
    protected static ?string $title = 'Tugas & Materi Kelas';
    protected static ?string $slug = 'tugas-kelas';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Kelas';

    public ?string $selectedKelasId = ''; // Filter untuk kelas
    public ?string $selectedPertemuanId = ''; // Filter untuk pertemuan
    
    public Collection $kelasUntukFilter;
    public Collection $pertemuanUntukFilter;
    public Collection $daftarPertemuanDenganTugas;

    protected $queryString = [
        'selectedKelasId' => ['except' => ''],
        'selectedPertemuanId' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->loadKelasOptions();
        $this->loadPertemuanOptions(); // Muat opsi pertemuan awal
        $this->loadData();
    }

    protected function loadKelasOptions(): void
    {
        /** @var User $santri */
        $santri = Auth::user();
        if ($santri && method_exists($santri, 'kelasYangDiikuti')) {
            $this->kelasUntukFilter = $santri->kelasYangDiikuti()->orderBy('nama_kelas')->get(['kelas.id', 'nama_kelas']);
        } else {
            $this->kelasUntukFilter = collect();
        }
    }

    protected function loadPertemuanOptions(): void
    {
        /** @var User $santri */
        $santri = Auth::user();
        if (!$santri) {
            $this->pertemuanUntukFilter = collect();
            return;
        }

        $query = Pertemuan::query()
            ->whereHas('kelas.santris', function ($query) use ($santri) {
                $query->where('users.id', $santri->id);
            });

        if (!empty($this->selectedKelasId)) {
            $query->where('kelas_id', $this->selectedKelasId);
        }
        
        $this->pertemuanUntukFilter = $query->orderBy('tanggal_pertemuan', 'desc')
                                           ->orderBy('waktu_mulai', 'desc')
                                           ->get(['id', 'judul_pertemuan', 'tanggal_pertemuan']);
    }
    
    protected function loadData(): void
    {
        /** @var User $santri */
        $santri = Auth::user();
        if (!$santri) {
            $this->daftarPertemuanDenganTugas = collect();
            return;
        }

        $pertemuanQuery = Pertemuan::query()
            ->whereHas('kelas.santris', function ($query) use ($santri) {
                $query->where('users.id', $santri->id);
            })
            ->with(['itemsTugas' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }, 'kelas'])
            ->orderBy('tanggal_pertemuan', 'desc')
            ->orderBy('waktu_mulai', 'desc');

        if (!empty($this->selectedKelasId)) {
            $pertemuanQuery->where('kelas_id', $this->selectedKelasId);
        }

        if (!empty($this->selectedPertemuanId)) {
            $pertemuanQuery->where('id', $this->selectedPertemuanId);
        }

        $this->daftarPertemuanDenganTugas = $pertemuanQuery->get();
    }

    public function updatedSelectedKelasId(): void
    {
        $this->selectedPertemuanId = ''; // Reset filter pertemuan saat kelas berubah
        $this->loadPertemuanOptions(); // Muat ulang opsi pertemuan berdasarkan kelas baru
        $this->loadData();
    }

    public function updatedSelectedPertemuanId(): void
    {
        $this->loadData();
    }

    // Computed property untuk opsi filter kelas (digunakan di Blade)
    public function getKelasFilterOptionsProperty(): Collection
    {
        return $this->kelasUntukFilter;
    }

    // Computed property untuk opsi filter pertemuan (digunakan di Blade)
    public function getPertemuanFilterOptionsProperty(): Collection
    {
        return $this->pertemuanUntukFilter;
    }
}
