<x-filament-widgets::widget class="fi-absensi-pengajar-widget">
    <x-filament::section>
        <x-slot name="heading">
            Absensi Hari Ini
        </x-slot>
        <x-slot name="description">
            {{ $this->tanggalHariIni }}
        </x-slot>

        <div class="mt-2">
            @if($this->sudahAbsenHariIni && $this->statusAbsenTercatat)
                {{-- ... (bagian ini tetap sama seperti sebelumnya) ... --}}
                <div @class([
                    'p-4 rounded-lg text-sm mb-4',
                    match ($this->statusAbsenTercatat->getColor()) {
                        'success' => 'bg-green-100 dark:bg-green-700 text-green-700 dark:text-green-200',
                        'warning' => 'bg-yellow-100 dark:bg-yellow-700 text-yellow-700 dark:text-yellow-200',
                        'danger' => 'bg-red-100 dark:bg-red-700 text-red-700 dark:text-red-200',
                        default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200',
                    }
                ])>
                    Anda sudah tercatat: <span class="font-semibold">{{ $this->statusAbsenTercatat->getLabel() }}</span>
                    @if($this->waktuMasukTercatat && $this->statusAbsenTercatat === \App\Enums\StatusAbsensi::MASUK)
                        pada pukul {{ $this->waktuMasukTercatat }}.
                    @else
                        hari ini.
                    @endif
                    @if($this->keteranganAbsenTercatat)
                        <p class="mt-1 text-xs italic">Keterangan: {{ $this->keteranganAbsenTercatat }}</p>
                    @endif
                </div>
            @else
                <div class="flex flex-wrap gap-3 mb-4">
                    @foreach($this->getStatusOptions() as $statusOption)
                        @php
                            $isCurrentStatusSelected = $this->statusPilihan === $statusOption;
                            $isMasuk = $statusOption === \App\Enums\StatusAbsensi::MASUK;
                            
                            // Untuk Masuk, selalu panggil pilihStatus (yang kemudian submit)
                            // Untuk Izin/Sakit, jika sudah dipilih, panggil submitAbsen, jika belum, panggil pilihStatus
                            $clickAction = $isMasuk ? 'pilihStatus(\'' . $statusOption->value . '\')' : ($isCurrentStatusSelected ? 'submitAbsen' : 'pilihStatus(\'' . $statusOption->value . '\')');
                            $buttonText = $isMasuk ? $statusOption->getLabel() : ($isCurrentStatusSelected ? 'Kirim ' . $statusOption->getLabel() : $statusOption->getLabel());
                        @endphp
                        <x-filament::button
                            :wire:click="$clickAction"
                            :color="$statusOption->getColor()"
                            :outlined="!$isCurrentStatusSelected && !$isMasuk" {{-- Masuk tidak perlu outlined jika dipilih --}}
                            :disabled="$this->sudahAbsenHariIni"
                            tag="button"
                            type="button"
                            size="md"
                            wire:loading.attr="disabled"
                            :wire:target="$clickAction"
                        >
                            <div wire:loading wire:target="{{$clickAction}}" class="mr-1">
                                <x-filament::loading-indicator class="h-4 w-4"/>
                            </div>
                            {{ $buttonText }}
                        </x-filament::button>
                    @endforeach
                </div>

                {{-- Form Keterangan (muncul jika Izin atau Sakit dipilih dan BELUM jadi tombol Kirim) --}}
                @if ($this->statusPilihan && in_array($this->statusPilihan, [\App\Enums\StatusAbsensi::IZIN, \App\Enums\StatusAbsensi::SAKIT]))
                    <div class="mb-4 space-y-3">
                        <div>
                            <label for="keterangan_absen_{{ $this->getId() }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                Keterangan {{ $this->statusPilihan->getLabel() }} <span class="text-danger-500">*</span>
                            </label>
                            <textarea
                                wire:model.defer="keterangan"
                                id="keterangan_absen_{{ $this->getId() }}"
                                rows="3"
                                class="block w-full fi-input-text rounded-lg shadow-sm transition duration-75 focus:ring-1 focus:ring-inset disabled:opacity-70 dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600 focus:border-primary-500 focus:ring-primary-500 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                                placeholder="Tulis keterangan Anda di sini..."
                            ></textarea>
                            @error('keterangan') <span class="text-sm text-danger-600">{{ $message }}</span> @enderror
                        </div>
                        {{-- Tombol Kirim Absen terpisah DIHAPUS --}}
                    </div>
                @endif
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>