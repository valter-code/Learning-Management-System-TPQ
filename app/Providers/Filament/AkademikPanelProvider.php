<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Admin\Resources\SppResource;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use App\Filament\Admin\Resources\PengajarResource;
use App\Filament\Pages\Shared\AturKontakDanAlamat;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Admin\Resources\KegiatanGaleriResource;
use Filament\Http\Middleware\DisableBladeIconComponents;
use App\Filament\Admin\Resources\PendaftarSantriResource;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Filament\Pengajar\Resources\RiwayatAbsensiResource;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Admin\Resources\RiwayatAbsensiPengajarResource;

class AkademikPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('akademik')
            ->path('akademik')
            ->login()
            ->sidebarFullyCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->resources([
                KegiatanGaleriResource::class,
                PengajarResource::class,
                PendaftarSantriResource::class,
                RiwayatAbsensiPengajarResource::class,
                SppResource::class, // Tambahkan resource SPP
            ])
            ->discoverResources(in: app_path('Filament/Akademik/Resources'), for: 'App\\Filament\\Akademik\\Resources')
            ->discoverPages(in: app_path('Filament/Akademik/Pages'), for: 'App\\Filament\\Akademik\\Pages')
            ->pages([
                Pages\Dashboard::class,
                // AturKontakDanAlamat::class, // Daftarkan di sini
            ])
            ->discoverWidgets(in: app_path('Filament/Akademik/Widgets'), for: 'App\\Filament\\Akademik\\Widgets')
            ->widgets([
                
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
