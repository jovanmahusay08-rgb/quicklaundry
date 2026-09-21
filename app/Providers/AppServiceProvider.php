<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\RateLimiter::for('registration-sms', function (\Illuminate\Http\Request $request) {
            return [
                \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by('registration-sms-minute:'.$request->ip()),
                \Illuminate\Cache\RateLimiting\Limit::perHour(10)->by('registration-sms-hour:'.$request->ip()),
            ];
        });
        \Illuminate\Support\Facades\RateLimiter::for('registration-phone-code', fn (\Illuminate\Http\Request $request) =>
            \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by('registration-phone-code:'.$request->ip())
        );

        Schema::defaultStringLength(191);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
