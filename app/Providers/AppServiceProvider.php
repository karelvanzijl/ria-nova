<?php

namespace App\Providers;

use App\Containers\Localization\Models\Language;
use App\Observers\LanguageObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Language::observe(LanguageObserver::class);
    }
}
