<?php

namespace App\Providers;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Content;
use App\Models\Media;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemTranslation;
use App\Models\PageSection;
use App\Models\PageSectionTranslation;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\Slider;
use App\Models\SliderItem;
use App\Models\SliderItemTranslation;
use App\Models\User;
use App\Observers\CmsAuditObserver;
use App\Policies\AuditLogPolicy;
use App\Policies\CmsPolicy;
use App\Policies\ContactMessagePolicy;
use App\Policies\SiteSettingPolicy;
use App\Policies\UserPolicy;
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
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(ContactMessage::class, ContactMessagePolicy::class);
        Gate::policy(SiteSetting::class, SiteSettingPolicy::class);
        Gate::policy(AuditLog::class, AuditLogPolicy::class);

        foreach ([Category::class, Certificate::class, Content::class, Media::class, Menu::class, MenuItem::class, MenuItemTranslation::class, PageSection::class, PageSectionTranslation::class, Product::class, Slider::class, SliderItem::class, SliderItemTranslation::class] as $model) {
            Gate::policy($model, CmsPolicy::class);
            $model::observe(CmsAuditObserver::class);
        }

        View::composer('layouts.app', function ($view): void {
            $view->with('settings', Schema::hasTable('site_settings') ? SiteSetting::current()->toArray() : []);
        });
    }
}
