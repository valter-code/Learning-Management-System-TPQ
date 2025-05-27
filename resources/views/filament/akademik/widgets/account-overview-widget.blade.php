<x-filament-widgets::widget class="fi-info-akun-widget">
    <x-filament::section>
        @if ($this->user)
            <div class="flex items-center justify-between gap-x-3">
                <div class="flex items-center gap-x-3">
                    {{-- Avatar Pengguna --}}
                    <x-filament-panels::avatar.user :user="$this->user" class="!w-10 !h-10 rounded-full" />

                    {{-- Nama dan Peran Pengguna --}}
                    <div class="flex-1">
                        <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            Selamat Datang
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $this->user->name }}
                            @if($this->userRoleLabel)
                                <span class="text-xs text-gray-400 dark:text-gray-500">({{ $this->userRoleLabel }})</span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Tombol Sign Out --}}
                @if (filament()->getLogoutUrl())
                    <form action="{{ filament()->getLogoutUrl() }}" method="post" class="my-auto">
                        @csrf
                        <x-filament::button
                            type="submit"
                            color="gray"
                            icon="heroicon-m-arrow-left-on-rectangle"
                            icon-alias="panels::widgets.account.logout-button"
                            labeled-from="sm"
                            tag="button"
                            size="sm"
                        >
                            Sign out
                        </x-filament::button>
                    </form>
                @endif
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Informasi akun tidak tersedia.
            </p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
