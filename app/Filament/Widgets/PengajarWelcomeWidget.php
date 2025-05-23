<?php

namespace App\Filament\Pengajar\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Pastikan model User di-import

class PengajarWelcomeWidget extends Widget
{
    protected static string $view = 'filament.widgets.pengajar-welcome-widget';

    // Atur agar widget ini mengambil 1 kolom (jika dashboard 2 kolom)
    // atau sesuaikan (misal 6 jika grid 12 kolom)
    protected int | string | array $columnSpan = 1;

    public ?User $user; // Property untuk menyimpan data user

    public function mount(): void
    {
        $this->user = Auth::user();
    }
}