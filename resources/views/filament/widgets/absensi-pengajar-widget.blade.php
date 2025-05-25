<x-filament-widgets::widget class="fi-absen-pengajar-widget">
    <x-filament::section>
        <x-slot name="heading">
            Absensi Kehadiran Hari Ini
        </x-slot>
        <x-slot name="description">
            {{ $this->tanggalHariIni }}
        </x-slot>

        <div class="mt-2">
            {{-- Kondisi jika bukan pengajar --}}
            @if(Auth::user()?->role !== \App\Enums\UserRole::PENGAJAR)
                <p class="text-sm text-gray-500 dark:text-gray-400">Fitur absensi hanya untuk pengajar.</p>
            {{-- Kondisi jika pengajar sudah melakukan absensi hari ini --}}
            @elseif($this->sudahAbsenHariIni)
                <div @class([
                    'p-4 rounded-lg text-sm mb-4 shadow',
                    match (strtolower( ($this->statusAbsenTercatat instanceof \App\Enums\StatusAbsensi) ? $this->statusAbsenTercatat->value : str_replace(' ', '_', $this->statusAbsenTercatat ?? '') ) ) {
                        \App\Enums\StatusAbsensi::MASUK->value => 'bg-green-100 dark:bg-green-700/30 text-green-700 dark:text-green-300 border border-green-300 dark:border-green-600',
                        \App\Enums\StatusAbsensi::IZIN->value => 'bg-yellow-100 dark:bg-yellow-700/30 text-yellow-700 dark:text-yellow-300 border border-yellow-300 dark:border-yellow-600',
                        \App\Enums\StatusAbsensi::SAKIT->value => 'bg-orange-100 dark:bg-orange-700/30 text-orange-700 dark:text-orange-300 border border-orange-300 dark:border-orange-600',
                        'masuk' => 'bg-green-100 dark:bg-green-700/30 text-green-700 dark:text-green-300 border border-green-300 dark:border-green-600',
                        default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600',
                    }
                ])>
                    Anda sudah melakukan absensi:
                    <span class="font-semibold">{{ ($this->statusAbsenTercatat instanceof \App\Enums\StatusAbsensi) ? $this->statusAbsenTercatat->getLabel() : $this->statusAbsenTercatat }}</span>
                    @if($this->waktuMasukTercatat)
                        pada pukul {{ $this->waktuMasukTercatat }}.
                    @else
                        hari ini.
                    @endif
                    @if($this->keteranganAbsenTercatat)
                        <p class="mt-1 text-xs italic">Keterangan: {{ $this->keteranganAbsenTercatat }}</p>
                    @endif
                </div>
            {{-- Kondisi jika pengajar belum absen (maka tampilkan tombol aksi) --}}
            @else
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

                {{-- Form Keterangan (muncul jika Izin atau Sakit dipilih) --}}
                @php
                    // Konversi $this->statusPilihan ke objek Enum jika itu adalah string value
                    $statusPilihanEnum = null;
                    if ($this->statusPilihan) {
                        if ($this->statusPilihan instanceof \App\Enums\StatusAbsensi) {
                            $statusPilihanEnum = $this->statusPilihan;
                        } else {
                            $statusPilihanEnum = \App\Enums\StatusAbsensi::tryFrom((string)$this->statusPilihan);
                        }
                    }
                @endphp
                @if ($statusPilihanEnum && in_array($statusPilihanEnum, [\App\Enums\StatusAbsensi::IZIN, \App\Enums\StatusAbsensi::SAKIT]))
                    <div class="mb-4 space-y-3">
                        <div>
                            <label for="keterangan_absen_pengajar_{{ $this->getId() }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                Keterangan {{ $statusPilihanEnum->getLabel() }}
                                @if(in_array($statusPilihanEnum, [\App\Enums\StatusAbsensi::IZIN, \App\Enums\StatusAbsensi::SAKIT]))
                                    <span class="text-danger-500">*</span>
                                @endif
                            </label>
                            <textarea
                                wire:model.defer="keterangan"
                                id="keterangan_absen_pengajar_{{ $this->getId() }}"
                                rows="3"
                                class="block w-full fi-input-text rounded-lg shadow-sm transition duration-75 focus:ring-1 focus:ring-inset disabled:opacity-70
                                       bg-white text-gray-900 border-gray-300
                                       dark:bg-gray-800 dark:text-white dark:border-gray-600 {{-- PERUBAHAN DI SINI: dark:bg-black menjadi dark:bg-gray-800 --}}
                                       focus:border-primary-500 focus:ring-primary-500
                                       dark:focus:border-primary-500 dark:focus:ring-primary-500
                                       placeholder-gray-400 dark:placeholder-gray-500"
                                placeholder="Tulis keterangan Anda di sini..."
                            ></textarea>
                        </div>
                        <x-filament::button
                            wire:click="submitAbsen"
                            :color="match($statusPilihanEnum) {
                                \App\Enums\StatusAbsensi::IZIN => 'warning',
                                \App\Enums\StatusAbsensi::SAKIT => 'danger',
                                default => 'primary'
                            }"
                            tag="button" type="button" size="md"
                            wire:loading.attr="disabled" wire:target="submitAbsen"
                            class="mt-3"
                        >
                            <div wire:loading wire:target="submitAbsen" class="mr-1">
                                <x-filament::loading-indicator class="h-4 w-4"/>
                            </div>
                            Kirim Absen {{ $statusPilihanEnum->getLabel() }}
                        </x-filament::button>
                    </div>
                @endif
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
