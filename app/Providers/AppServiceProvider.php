<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
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
        Schema::defaultStringLength(191);
        \App\Models\Category::orderBy('name')->get();
        view()->composer('*', function ($view) {
            $view->with('categories', \App\Models\Category::orderBy('name')->get());
        });
        \App\Models\Product::observe(\App\Observers\ProductObserver::class);
    }
}
