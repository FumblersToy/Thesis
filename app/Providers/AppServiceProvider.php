<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Force HTTPS for all URLs in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');
            
            // Force Vite to use manifest (production mode)
            Vite::useManifestFilename('manifest.json');
        }
    }
}