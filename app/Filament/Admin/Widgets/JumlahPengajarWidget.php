<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Enums\UserRole; 

class JumlahPengajarWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $jumlahPengajar = User::where('role', UserRole::PENGAJAR)->count();

        return [

            Stat::make('Total Admin Aktif', User::where('role', UserRole::ADMIN)->count())
                ->description('Jumlah Admin yang terdaftar dan aktif')
                ->icon('heroicon-o-users')
                ->color('info'),
            
            Stat::make('Total Staff Akademik Aktif', User::where('role', UserRole::AKADEMIK)->count())
                ->description('Jumlah Staff Akademik yang terdaftar dan aktif')
                ->icon('heroicon-o-users')
                ->color('info'),

            Stat::make('Total Staff Pengajar Aktif', $jumlahPengajar)
                ->description('Jumlah keseluruhan staf pengajar aktif')
                ->icon('heroicon-o-identification') 
                ->color('success'),

            Stat::make('Total Santri Aktif', User::where('role', UserRole::SANTRI)->count())
                ->description('Jumlah santri yang terdaftar dan aktif')
                ->icon('heroicon-o-users')
                ->color('info'),
        ];
    }

    /**
     * Mengatur apakah widget ini harus ditampilkan.
     * Hanya tampil jika yang login adalah Admin atau Akademik (sesuai kebutuhan Anda).
     */
    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK);
    }
}
