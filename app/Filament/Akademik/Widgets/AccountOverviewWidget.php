<?php

namespace App\Filament\Akademik\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 
use App\Enums\UserRole; 

class AccountOverviewWidget extends Widget
{
    protected static string $view = 'filament.akademik.widgets.account-overview-widget'; 

    
    protected int | string | array $columnSpan = 'full'; 

    protected static ?int $sort = -3; // Urutan widget di dashboard (angka kecil lebih atas)

    public ?User $user;
    public ?string $userRoleLabel = null;

    public function mount(): void
    {
        $this->user = Auth::user();
        if ($this->user && $this->user->role instanceof UserRole) {
            $this->userRoleLabel = $this->user->role->getLabel();
        } elseif ($this->user && is_string($this->user->role)) {
            // Fallback jika role adalah string biasa
            $this->userRoleLabel = ucfirst($this->user->role);
        }
    }
}
