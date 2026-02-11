<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        ServiceResult::observe(ServiceResultObserver::class);
    }
}
