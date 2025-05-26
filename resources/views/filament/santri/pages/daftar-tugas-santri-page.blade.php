<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 p-4 bg-white dark:bg-gray-900 rounded-lg shadow-lg"> {{-- Latar belakang seperti section pertemuan --}}
        <div>
            <label for="kelasFilterPage" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Filter Berdasarkan Kelas:</label>
            <div class="flex rounded-lg shadow-sm mt-1">
                <x-filament::input.select wire:model.live="selectedKelasId" id="kelasFilterPage" class="!rounded-r-none focus:!border-teal-500 focus:!ring-1 focus:!ring-teal-500 dark:focus:!border-teal-400 dark:focus:!ring-teal-400">
                    <option value="">Semua Kelas</option>
                    @foreach($this->kelas_filter_options as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </x-filament::input.select>
                </span>
            </div>
        </div>

        <div>
            <label for="statusFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Filter Status Tugas:</label>
            <x-filament::input.select wire:model.live="filterStatus" id="statusFilter" class="mt-1">
                @foreach($this->status_filter_options as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </x-filament::input.select>
        </div>
    </div>
    
    @if($this->semuaTugasDenganStatus->isEmpty())
        <div class="text-center py-12">
            <x-heroicon-o-document-magnifying-glass class="mx-auto h-12 w-12 text-gray-400"/>
            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                @if((!empty($this->filterStatus) && $this->filterStatus !== '') || (!empty($this->filterKelasId) && $this->filterKelasId !== ''))
                    Tidak ada tugas yang cocok dengan filter yang dipilih.
                @else
                    Anda belum memiliki tugas.
                @endif
            </h3>
        </div>
    @else
        <div class="bg-white dark:bg-gray-900 shadow-md rounded-lg overflow-hidden">
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($this->semuaTugasDenganStatus as $tugas)
                    <li class="px-4 py-4 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-900/50 transition duration-150">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <a href="{{ \App\Filament\Santri\Resources\PertemuanResource::getUrl('view', ['record' => $tugas->pertemuan_id]) . '#tugas-' . $tugas->id_tugas_item }}"
                                   class="text-sm font-semibold text-teal-600 dark:text-teal-400 hover:underline truncate">
                                    {{ $tugas->judul_tugas }}
                                </a>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Dari: {{ $tugas->judul_pertemuan }} (Kelas: {{ $tugas->nama_kelas }})
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Tenggat: {{ $tugas->deadline }}
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0 text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    @if($tugas->status_pengumpulan_label === 'Dinilai' || $tugas->status_pengumpulan_label === \App\Enums\StatusPengumpulanTugasEnum::DINILAI->getLabel())
                                        Nilai: {{ $tugas->nilai ?? 'N/A' }}/{{$tugas->poin_maksimal}}
                                    @else
                                        {{ $tugas->poin_maksimal ? '.../'.$tugas->poin_maksimal : ''}}
                                    @endif
                                </p>
                                <span @class([
                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                    'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' => $tugas->status_pengumpulan_warna === 'gray',
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-300' => $tugas->status_pengumpulan_warna === 'warning',
                                    'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-300' => $tugas->status_pengumpulan_warna === 'danger',
                                    'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-300' => $tugas->status_pengumpulan_warna === 'primary',
                                    'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-300' => $tugas->status_pengumpulan_warna === 'success',
                                ])>
                                    {{ $tugas->status_pengumpulan_label }}
                                </span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        {{-- Paginasi jika mengimplementasikannya di PHP --}}
        {{-- @if ($this->semuaTugasDenganStatus instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-6">
                {{ $this->semuaTugasDenganStatus->links() }}
            </div>
        @endif --}}
    @endif
</x-filament-panels::page>
