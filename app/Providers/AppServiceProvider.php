<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\Content;
use App\Models\Media;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\PageSection;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\Slider;
use App\Models\SliderItem;
use App\Observers\CmsAuditObserver;
use App\Policies\CmsPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('contact', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));

        foreach ([Category::class, Certificate::class, Content::class, Media::class, Menu::class, MenuItem::class, PageSection::class, Product::class, SiteSetting::class, Slider::class, SliderItem::class] as $model) {
            Gate::policy($model, CmsPolicy::class);
            $model::observe(CmsAuditObserver::class);
        }

        View::composer('layouts.app', function ($view): void {
            $view->with('settings', Schema::hasTable('site_settings') ? SiteSetting::current()->toArray() : []);
        });
    }
}
