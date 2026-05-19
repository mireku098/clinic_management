<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\ServiceResult;
use App\Observers\ServiceResultObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \App\Models\ServiceResult::observe(\App\Observers\ServiceResultObserver::class);

        $appUrl = config('app.url');
        if (! empty($appUrl)) {
            URL::forceRootUrl(rtrim($appUrl, '/'));

            if (strpos($appUrl, 'https://') === 0) {
                URL::forceScheme('https');
            }
        }
    }
}
