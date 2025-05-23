<x-filament-widgets::widget class="fi-pengajar-welcome-widget">
    <x-filament::section>
        <div class="flex items-center gap-x-3">
            @if($this->user)
                <x-filament-panels::avatar.user :user="$this->user" class="!w-10 !h-10 rounded-full" />
                <div class="flex-1">
                    <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        Welcome
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $this->user->name }}
                    </p>
                </div>
                @if(filament()->auth()->check())
                <form action="{{ filament()->getLogoutUrl() }}" method="post" class="flex-shrink-0">
                    @csrf
                    <x-filament::button
                        type="submit"
                        color="gray"
                        icon="heroicon-m-arrow-left-on-rectangle"
                        icon-alias="panels::widgets.account.logout-button" {{-- Anda bisa menggunakan alias ikon default atau ikon Anda sendiri --}}
                        labeled-from="sm"
                        tag="button"
                    >
                        Sign out
                    </x-filament::button>
                </form>
            @endif

            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">User not found.</p>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>