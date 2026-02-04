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
        // Deteksi apakah sedang dibuka lewat Cloudflare atau Ngrok
        $host = request()->getHost();

        // Cukup cek kata 'ngrok' atau 'cloudflare', tidak perlu akhiran .app/.dev
        if (str_contains($host, 'trycloudflare') || str_contains($host, 'ngrok')) {
            
            // 1. Paksa semua Link/Route jadi HTTPS
            URL::forceScheme('https');

            // 2. Paksa Form Submission dianggap HTTPS
            $this->app['request']->server->set('HTTPS', 'on');
        }
    }
}