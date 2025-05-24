<x-filament-widgets::widget class="fi-absensi-santri-mandiri-widget">
    <x-filament::section>
        <x-slot name="heading">
            Absensi Kehadiran Hari Ini
        </x-slot>
        <x-slot name="description">
            {{ $this->tanggalHariIni }}
        </x-slot>

        <div class="mt-2">
            {{-- Menghapus bagian yang menampilkan kelas utama santri --}}

            @if(Auth::user()?->role !== \App\Enums\UserRole::SANTRI)
                <p class="text-sm text-gray-500 dark:text-gray-400">Fitur absensi hanya untuk santri.</p>
            @elseif($this->sudahAbsenHariIni)
                {{-- Tampilkan status jika sudah absen --}}
                <div @class([
                    'p-4 rounded-lg text-sm mb-4 shadow',
                    match (strtolower($this->statusAbsenTercatat ?? '')) {
                        'masuk' => 'bg-green-100 dark:bg-green-700/30 text-green-700 dark:text-green-300 border border-green-300 dark:border-green-600',
                        'izin' => 'bg-yellow-100 dark:bg-yellow-700/30 text-yellow-700 dark:text-yellow-300 border border-yellow-300 dark:border-yellow-600',
                        'sakit' => 'bg-orange-100 dark:bg-orange-700/30 text-orange-700 dark:text-orange-300 border border-orange-300 dark:border-orange-600',
                        default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600',
                    }
                ])>
                    Anda sudah melakukan absensi:
                    <span class="font-semibold">{{ $this->statusAbsenTercatat }}</span>
                    @if($this->jamAbsenTercatat)
                        pada pukul {{ $this->jamAbsenTercatat }}.
                    @else
                        hari ini.
                    @endif
                    @if($this->keteranganAbsenTercatat)
                        <p class="mt-1 text-xs italic">Keterangan: {{ $this->keteranganAbsenTercatat }}</p>
                    @endif
                </div>
            @elseif($this->bisaAbsenHariIni)
                {{-- Tombol Pilihan Status dan Form Keterangan --}}
                {{-- Kode ini sebagian besar tetap sama --}}
                <div class="flex flex-wrap gap-3 mb-4">
                    @foreach($this->getStatusOptions() as $statusOption)
                        <x-filament::button
                            :wire:click="'pilihStatus(\'' . $statusOption->value . '\')'"
                            :color="match($statusOption) {
                                \App\Enums\StatusAbsensi::MASUK => 'success',
                                \App\Enums\StatusAbsensi::IZIN => 'warning',
                                \App\Enums\StatusAbsensi::SAKIT => 'danger',
                                default => 'gray'
                            }"
                            :outlined="$this->statusPilihan !== $statusOption->value"
                            tag="button" type="button" size="md"
                            wire:loading.attr="disabled"
                            :wire:target="'pilihStatus(\'' . $statusOption->value . '\'), submitAbsen'"
                        >
                            <div wire:loading wire:target="pilihStatus('{{ $statusOption->value }}'), submitAbsen" class="mr-1">
                                <x-filament::loading-indicator class="h-4 w-4"/>
                            </div>
                            {{ $statusOption->getLabel() }}
                        </x-filament::button>
                    @endforeach
                </div>

                @if ($this->statusPilihan && in_array(\App\Enums\StatusAbsensi::tryFrom($this->statusPilihan), [\App\Enums\StatusAbsensi::IZIN, \App\Enums\StatusAbsensi::SAKIT]))
                    {{-- ... Form Keterangan ... --}}
                @endif
            @else 
                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak dapat melakukan absensi saat ini. Pastikan Anda terdaftar sebagai santri aktif.</p>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
