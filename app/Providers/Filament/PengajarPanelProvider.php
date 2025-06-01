<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Resources\SppResource;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\View;
use App\Filament\Widgets\KelasAjarWidget;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
// use App\Filament\Widgets\AbsensiHariIniWidget2;
// use App\Filament\Pengajar\Widgets\AbsensiPengajarWidget;
use Filament\Http\Middleware\AuthenticateSession;
use App\Filament\Akademik\Resources\SantriResource;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pengajar\Widgets\AbsensiPengajarWidget;
use App\Filament\Pengajar\Widgets\PengajarWelcomeWidget;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Filament\Pengajar\Resources\RiwayatAbsensiResource;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use League\Flysystem\Visibility;

class PengajarPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('pengajar')
            ->path('pengajar')
            ->login()
            ->sidebarFullyCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Sky,
            ])
            // ->css('resources/css/filament/pengajar/theme.css') // Sesuaikan path
            ->resources([
                SantriResource::class,
                RiwayatAbsensiResource::class,
            ])
           ->plugins([
                FilamentEditProfilePlugin::make()
                    ->slug('profil-saya') 
                    ->setTitle('Profil Saya')
                    ->setNavigationLabel('Profil Saya')
                    ->canAccess(fn () => auth()->check()) 
                    ->shouldRegisterNavigation(false) // Tampilkan di navigasi utama (opsional, bisa false jika hanya via user menu)
                    ->shouldShowEmailForm() 
                    ->shouldShowDeleteAccountForm(false) 
                    ->shouldShowBrowserSessionsForm() 
                    ->shouldShowAvatarForm() // Aktifkan upload avatar
                    
                    // ->customProfileComponents([])
            ])
            ->userMenuItems([ 
                'profile' => MenuItem::make()
                    ->label(fn() => 'Profil Saya') // Label bisa dinamis: auth()->user()->name
                    ->url(fn (): string => EditProfilePage::getUrl()) 
                    ->icon('heroicon-m-user-circle')
                    ->visible(fn (): bool => auth()->check()), // Hanya tampil jika user login
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

//     public function boot(): void
// {
//     Filament::serving(function () {
//         Filament::registerRenderHook(
//             // Hook 'panels::styles.after' lebih baik untuk menyisipkan <style>
//             // atau 'panels::head.end'
//             'panels::styles.after', 
//             fn (): string => View::make('filament.custom-styles-pengajar')->render()
//         );
//     });
// }
}
