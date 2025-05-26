<x-filament-widgets::widget class="fi-filament-info-widget">
    <x-filament::section>
        <x-slot name="heading">
            Kelas yang Anda Ajar
        </x-slot>

        @php
            // Mengakses property yang sudah di-compute dari kelas Widget
            // $this merujuk pada instance KelasAjarWidget
            $daftarKelasProperty = $this->getKelasDataProperty();
        @endphp

        @if (count($daftarKelasProperty) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($daftarKelasProperty as $kelas)
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm hover:shadow-lg p-6 flex flex-col justify-between transition-shadow duration-300">
                        <div>
                            <div class="flex items-center gap-3 text-xl font-semibold text-gray-800 dark:text-white">
                                {{-- Menampilkan ikon dari data $kelas --}}
                                <div class="text-2xl"> 
                                    <i class="{{ $kelas['icon'] }}"></i> {{-- Kelas ikon sudah termasuk warna dari helper PHP $kelas['icon'] --}}
                                </div>
                                <x-dynamic-component 
                                        :component="$kelas['icon']" 
                                        class="w-7 h-7 text-teal-600 dark:text-teal-400" /> 
                                <span>{{ $kelas['name'] }}</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 mt-2 text-sm line-clamp-3 min-h-[3.75rem]">{{ $kelas['description'] }}</p>
                        </div>

                        <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-users text-gray-400 dark:text-gray-500"></i>
                                <span>{{ $kelas['studentCount'] }} Santri Terdaftar</span>
                            </div>
                            <div class="flex items-center gap-2 mt-1.5">
                                <i class="fas fa-calendar-alt text-gray-400 dark:text-gray-500"></i>
                                <span>Pertemuan Berikutnya: <span class="font-medium">{{ $kelas['nextMeeting'] ?: 'Belum dijadwalkan' }}</span></span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="{{ $kelas['meetingPageUrl'] }}"
                               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl bg-primary-500 hover:bg-primary-600 text-white shadow-sm transition focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                <i class="fas fa-arrow-right"></i>
                                <span>Masuk & Kelola Pertemuan</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Tampilan jika tidak ada kelas yang diajar --}}
            <div class="col-span-full text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="flex flex-col items-center">
                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mb-4" xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h12A2.25 2.25 0 0 0 20.25 14.25V3M3.75 3H20.25M3.75 3H1.5m2.25 0V1.5m16.5 0V3m2.25 0V1.5m-2.25 0h.008v.008h-.008V3Zm-4.5 0h.008v.008h-.008V3Zm-4.5 0h.008v.008h-.008V3Zm-4.5 0h.008v.008h-.008V3Zm-2.25 5.25h16.5" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Anda belum terdaftar mengajar di kelas manapun.</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Silakan hubungi administrator untuk informasi lebih lanjut.</p>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>