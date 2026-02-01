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

            if (str_contains($host, 'trycloudflare.com') || str_contains($host, 'ngrok-free.app')) {
                // 1. Paksa semua Link/Route jadi HTTPS
                URL::forceScheme('https');

                // 2. Paksa Form Submission dianggap HTTPS (PENTING UNTUK MENGHILANGKAN PERINGATAN)
                $this->app['request']->server->set('HTTPS', 'on');
            }
        }
}