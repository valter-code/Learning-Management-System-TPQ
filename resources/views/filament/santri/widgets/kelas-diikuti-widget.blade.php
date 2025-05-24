<x-filament-widgets::widget class="fi-kelas-diikuti-widget">
    <x-filament::section>
        <x-slot name="heading">
            Kelas yang Anda Ikuti
        </x-slot>

        @if (count($this->kelasData) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($this->kelasData as $kelas)
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm hover:shadow-lg p-6 flex flex-col justify-between transition-shadow duration-300">
                        <div>
                        <div class="flex items-center gap-3 text-xl font-semibold text-gray-800 dark:text-white">
                                    <x-dynamic-component 
                                        :component="$kelas['icon']" 
                                        class="w-7 h-7 text-teal-600 dark:text-teal-400" /> 
                                    <span>{{ $kelas['name'] }}</span> 
                                </div>
                            <p class="text-gray-600 dark:text-gray-300 mt-2 text-sm line-clamp-3 min-h-[3.75rem]">{{ $kelas['description'] }}</p>
                        </div>

                        <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-users class="w-4 h-4 text-gray-400 dark:text-gray-500"/>
                                <span>{{ $kelas['studentCount'] }} Santri di Kelas Ini</span>
                            </div>
                            <div class="flex items-center gap-2 mt-1.5">
                                <x-heroicon-o-calendar-days class="w-4 h-4 text-gray-400 dark:text-gray-500"/>
                                <span>Pertemuan Berikutnya: <span class="font-medium">{{ $kelas['nextMeeting'] ?: 'Belum dijadwalkan' }}</span></span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-filament::button
                                tag="a"
                                :href="$kelas['pertemuanPageUrl']"
                                icon="heroicon-o-arrow-right-circle"
                                color="primary"
                            >
                                Lihat Pertemuan Kelas
                            </x-filament::button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="col-span-full text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="flex flex-col items-center">
                    <x-heroicon-o-academic-cap class="w-16 h-16 text-gray-400 dark:text-gray-500 mb-4"/>
                    <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Anda belum terdaftar di kelas manapun.</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Silakan hubungi staf akademik jika ada kesalahan.</p>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>