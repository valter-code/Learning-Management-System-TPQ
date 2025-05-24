<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use App\Filament\Widgets\KelasAjarWidget;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use App\Filament\Akademik\Resources\SantriResource;
use Illuminate\Routing\Middleware\SubstituteBindings;
// use App\Filament\Widgets\AbsensiHariIniWidget2;
// use App\Filament\Pengajar\Widgets\AbsensiPengajarWidget;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pengajar\Widgets\AbsensiPengajarWidget;
use App\Filament\Pengajar\Widgets\PengajarWelcomeWidget;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Filament\Pengajar\Resources\RiwayatAbsensiResource;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class PengajarPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('pengajar')
            ->path('pengajar')
            ->login()
            ->colors([
                'primary' => Color::Sky,
            ])
            ->resources([
                SantriResource::class,
                
                RiwayatAbsensiResource::class, // Pastikan Anda sudah membuat resource ini
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                //PengajarWelcomeWidget::class,
                KelasAjarWidget::class,             // Widget baru Anda
                AbsensiPengajarWidget::class, // Daftarkan widget di sini
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
