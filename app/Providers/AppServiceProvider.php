<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
        View::composer('*', function ($view) {
            $view->with('settings', Setting::getMultiple([
                'contact_address',
                'contact_phone',
                'contact_email',
                'contact_maps_iframe',
                // 'misi',
                // 'sejarah_singkat',
                // 'visi',
                'web_mission',
                'web_brief_history',
                'web_vision'
            ]));
        });

    }
}
