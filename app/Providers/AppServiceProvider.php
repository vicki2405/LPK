<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind dynamic public path for shared hosting without symlink (cPanel/DirectAdmin support)
        $this->app->bind('path.public', function () {
            // Check if explicitly defined in config or .env
            $customPath = config('filesystems.custom_public_path') ?: env('CUSTOM_PUBLIC_PATH');
            if ($customPath) {
                return $customPath;
            }

            // Check if public_html exists as a sibling directory (standard cPanel structure)
            $siblingPublicHtml = base_path('../public_html');
            if (is_dir($siblingPublicHtml)) {
                return realpath($siblingPublicHtml);
            }

            // Check if public_html exists inside base_path
            $insidePublicHtml = base_path('public_html');
            if (is_dir($insidePublicHtml)) {
                return realpath($insidePublicHtml);
            }

            return base_path('public');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
