<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Request;

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
        // LOGIKA HYBRID:
        // Kita cek URL browser saat ini (Current Request)
        
        // 1. Jika URL mengandung 'ngrok', berarti sedang diakses dari HP/Internet
        if (str_contains(request()->getHost(), 'ngrok')) {
            URL::forceScheme('https');
        }

        // 2. Jika URL adalah 127.0.0.1 atau localhost, dia akan melewati if di atas
        // dan tetap menggunakan HTTP biasa (Mode Laptop).
    }
}