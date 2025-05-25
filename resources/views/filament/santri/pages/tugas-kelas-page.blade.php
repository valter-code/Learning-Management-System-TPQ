<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Filter Dropdown Kelas --}}
        <div>
            <label for="kelasFilterPage" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Filter Berdasarkan Kelas:</label>
            <x-filament::input.select wire:model.live="selectedKelasId" id="kelasFilterPage" class="mt-1">
                <option value="">Semua Kelas</option>
                @foreach($this->kelas_filter_options as $kelas)
                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                @endforeach
            </x-filament::input.select>
        </div>

        {{-- Filter Dropdown Pertemuan/Topik --}}
        <div>
            <label for="pertemuanFilterPage" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Filter Berdasarkan Topik/Pertemuan:</label>
            <x-filament::input.select wire:model.live="selectedPertemuanId" id="pertemuanFilterPage" class="mt-1">
                <option value="">Semua Topik</option>
                @foreach($this->pertemuan_filter_options as $pertemuan)
                    <option value="{{ $pertemuan->id }}">
                        {{ $pertemuan->judul_pertemuan }} ({{ $pertemuan->tanggal_pertemuan->format('d M Y') }})
                    </option>
                @endforeach
            </x-filament::input.select>
        </div>
    </div>

    @if($this->daftarPertemuanDenganTugas->isEmpty())
        <div class="text-center py-12">
            <x-heroicon-o-clipboard-document-list class="mx-auto h-12 w-12 text-gray-400"/>
            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                @if(!empty($this->selectedPertemuanId) || !empty($this->selectedKelasId))
                    Tidak ada tugas yang cocok dengan filter yang dipilih.
                @else
                    Belum ada tugas kelas yang tersedia.
                @endif
            </h3>
        </div>
    @else
        <div class="space-y-8">
            @foreach($this->daftarPertemuanDenganTugas as $pertemuan)
                <x-filament::section collapsible collapsed="{{ !$loop->first && empty($this->selectedPertemuanId) }}"> {{-- Buka item pertama jika tidak ada filter pertemuan --}}
                    <x-slot name="heading">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <span class="text-lg font-semibold text-gray-800 dark:text-white">
                                {{ $pertemuan->judul_pertemuan }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 sm:mt-0">
                                Kelas: {{ $pertemuan->kelas->nama_kelas }} | {{ $pertemuan->tanggal_pertemuan->translatedFormat('l, d F Y') }}
                                {{ $pertemuan->waktu_mulai ? ' - Pukul ' . \Carbon\Carbon::parse($pertemuan->waktu_mulai)->format('H:i') : '' }}
                            </span>
                        </div>
                    </x-slot>

                    @if($pertemuan->itemsTugas->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400 px-4 py-2">Tidak ada tugas untuk pertemuan ini.</p>
                    @else
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($pertemuan->itemsTugas as $tugas)
                                <li class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition duration-150">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center min-w-0">
                                            <x-heroicon-s-clipboard-document-list class="h-6 w-6 text-gray-400 dark:text-gray-500 mr-3 flex-shrink-0"/>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                    {{ $tugas->judul_tugas }}
                                                </p>
                                                @if($tugas->deadline_tugas)
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    Tenggat: {{ \Carbon\Carbon::parse($tugas->deadline_tugas)->translatedFormat('d M Y, H:i') }}
                                                </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <x-filament::button
                                                tag="a"
                                                :href="\App\Filament\Santri\Resources\PertemuanResource::getUrl('view', ['record' => $pertemuan->id]) . '#tugas-' . $tugas->id"
                                                icon="heroicon-m-arrow-top-right-on-square"
                                                size="xs"
                                                color="gray"
                                            >
                                                Lihat & Kerjakan
                                            </x-filament::button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-filament::section>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
