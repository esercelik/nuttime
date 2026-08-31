<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('contact', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        View::composer('layouts.app', function ($view): void {
            $view->with('settings', Schema::hasTable('site_settings') ? SiteSetting::current()->toArray() : []);
        });
    }
}
