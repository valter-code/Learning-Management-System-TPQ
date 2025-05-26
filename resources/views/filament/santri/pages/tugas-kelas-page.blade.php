<x-filament-panels::page>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 p-4 bg-white dark:bg-gray-900 rounded-lg shadow-lg"> 
        <div>
            <label for="kelasFilterPage" class="block text-sm font-medium text-gray-800 dark:text-gray-200 mb-1">Filter Berdasarkan Kelas:</label>
            <div class="flex rounded-lg shadow-sm mt-1">
                <x-filament::input.select wire:model.live="selectedKelasId" id="kelasFilterPage" class="">
                    <option value="">Semua Kelas</option>
                    @foreach($this->kelas_filter_options as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </x-filament::input.select>
                {{-- Stray </span> removed --}}
            </div>
        </div>

        <div>
            <label for="pertemuanFilterPage" class="block text-sm font-medium text-gray-900 dark:text-gray-200 mb-1">Filter Berdasarkan Topik/Pertemuan:</label>
             <div class="flex rounded-lg shadow-sm mt-1">
                <x-filament::input.select wire:model.live="selectedPertemuanId" id="pertemuanFilterPage" class="!rounded-r-none focus:!border-teal-500 focus:!ring-1 focus:!ring-teal-500 dark:focus:!border-teal-400 dark:focus:!ring-teal-400">
                    <option value="">Semua Topik</option>
                    @foreach($this->pertemuan_filter_options as $pertemuanFilterItem)
                        <option value="{{ $pertemuanFilterItem->id }}">
                            {{ $pertemuanFilterItem->judul_pertemuan }} ({{ $pertemuanFilterItem->tanggal_pertemuan->format('d M Y') }})
                        </option>
                    @endforeach
                </x-filament::input.select>
                {{-- Stray </span> removed --}}
            </div>
        </div>
    </div>

    @if($this->daftarPertemuanDenganTugas->isEmpty())
        <div class="text-center py-12">
            <x-heroicon-o-clipboard-document-list class="mx-auto h-12 w-12 text-gray-900"/>
            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                @if(!empty($this->selectedPertemuanId) || !empty($this->selectedKelasId))
                    Tidak ada materi atau tugas yang cocok dengan filter yang dipilih.
                @else
                    Belum ada materi atau tugas kelas yang tersedia.
                @endif
            </h3>
        </div>
    @else
        {{-- Loop untuk menampilkan setiap pertemuan --}}
        @foreach($this->daftarPertemuanDenganTugas as $pertemuan)
            <x-filament::section
                collapsible
                collapsed="{{ !$loop->first && empty($this->selectedPertemuanId) && empty($this->selectedKelasId) }}"
                {{-- Menambahkan margin bawah ke semua section KECUALI yang terakhir --}}
                class="shadow-lg dark:bg-gray-800 {{ !$loop->last ? 'mb-8' : '' }}"
            >
                <x-slot name="heading">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <span class="text-lg font-semibold text-gray-800 dark:text-white">
                            {{ $pertemuan->judul_pertemuan }}
                        </span>
                        <span class="text-xs text-black dark:text-white mt-1 sm:mt-0">
                            Kelas: {{ $pertemuan->kelas->nama_kelas }} | {{ $pertemuan->tanggal_pertemuan->translatedFormat('l, d F Y') }}
                            {{ $pertemuan->waktu_mulai ? ' - Pukul ' . \Carbon\Carbon::parse($pertemuan->waktu_mulai)->format('H:i') : '' }}
                        </span>
                    </div>
                </x-slot>

                {{-- BAGIAN MATERI --}}
                @if($pertemuan->itemsMateri->isNotEmpty())
                    <div class="mb-6 pt-2">
                        <h4 class="text-md font-semibold text-teal-700 dark:text-teal-500 mb-2 border-b pb-1 border-teal-200 dark:border-teal-700">Materi Pelajaran:</h4>
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($pertemuan->itemsMateri as $materi)
                                <li class="px-1 py-3 sm:px-2">
                                    <div class="flex items-start">
                                        <x-heroicon-s-book-open class="h-5 w-5 text-teal-500 dark:text-teal-400 mr-3 mt-1 flex-shrink-0"/>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $materi->judul_materi }}
                                                <span class="text-xs text-gray-500 dark:text-gray-400">({{ ucfirst($materi->tipe_materi) }})</span>
                                            </p>
                                            @if($materi->deskripsi_materi)
                                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5 prose prose-sm max-w-none">{!! Str::limit(strip_tags($materi->deskripsi_materi), 100) !!}</p>
                                            @endif
                                            @if($materi->tipe_materi === 'link' && $materi->url_link_materi)
                                                <a href="{{ $materi->url_link_materi }}" target="_blank" class="mt-1 inline-block text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                                    Lihat Link Materi
                                                    <x-heroicon-s-arrow-top-right-on-square class="inline h-3 w-3 ml-0.5"/>
                                                </a>
                                            @elseif($materi->tipe_materi === 'file' && $materi->path_file_materi)
                                                <a href="{{ Storage::url($materi->path_file_materi) }}" target="_blank" class="mt-1 inline-block text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                                    Download File Materi
                                                    <x-heroicon-s-arrow-down-tray class="inline h-3 w-3 ml-0.5"/>
                                                </a>
                                            @elseif($materi->tipe_materi === 'text' && $materi->Tugas_text_materi) {{-- Pastikan nama properti ini benar --}}
                                                 <a href="{{ \App\Filament\Santri\Resources\PertemuanResource::getUrl('view', ['record' => $pertemuan->id]) . '#materi-' . $materi->id }}"
                                                   class="mt-1 inline-block text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                                   Lihat Teks Lengkap
                                                   <x-heroicon-s-document-text class="inline h-3 w-3 ml-0.5"/>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                     <p class="text-sm text-gray-500 dark:text-gray-400 px-1 py-2">Tidak ada materi untuk pertemuan ini.</p>
                @endif

                {{-- BAGIAN TUGAS --}}
                @if($pertemuan->itemsTugas->isNotEmpty())
                    <div class="mt-4 pt-2">
                        <h4 class="text-md font-semibold text-teal-700 dark:text-teal-500 mb-2 border-b pb-1 border-teal-200 dark:border-teal-700">Tugas Terkait:</h4>
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($pertemuan->itemsTugas as $tugas)
                                <li class="px-1 py-3 sm:px-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center min-w-0">
                                            <x-heroicon-s-clipboard-document-list class="h-5 w-5 text-teal-500 dark:text-teal-400 mr-3 mt-1 flex-shrink-0"/>
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
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 px-1 py-2 mt-4">Tidak ada tugas untuk pertemuan ini.</p>
                @endif
            </x-filament::section>
        @endforeach
    @endif
</x-filament-panels::page>
