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

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Helper function to get image URL (handles both Cloudinary URLs and local storage)
        if (!function_exists('getImageUrl')) {
            function getImageUrl($path)
            {
                if (empty($path)) {
                    return null;
                }
                
                // If it's already a full URL (Cloudinary), return as-is
                if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                    return $path;
                }
                
                // Otherwise, use storage URL for local files
                return \Illuminate\Support\Facades\Storage::url($path);
            }
        }
    }
}