<?php

use App\Http\Controllers\ContentController;
use App\Http\Controllers\LocalePreferenceController;
use App\Http\Controllers\LocaleRedirectController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', LocaleRedirectController::class)->name('locale.redirect');
Route::post('/locale', LocalePreferenceController::class)->name('locale.preference');

foreach (config('nuttime.locales') as $locale => $configuration) {
    $paths = $configuration['paths'];

    Route::prefix($locale)->middleware('locale:'.$locale)->name('site.'.$locale.'.')->group(function () use ($paths): void {
        Route::get('/', [SiteController::class, 'home'])->name('home');
        Route::get($paths['products'], [SiteController::class, 'products'])->name('products');
        Route::get($paths['product'], [SiteController::class, 'product'])->name('product');
        Route::get($paths['category'], [SiteController::class, 'category'])->name('category');
        Route::get($paths['about'], fn () => app(SiteController::class)->page('about'))->name('about');
        Route::get($paths['certificates'], fn () => app(SiteController::class)->page('certificates'))->name('certificates');
        Route::get($paths['contact'], [SiteController::class, 'contact'])->name('contact');
        Route::post($paths['contact'], [SiteController::class, 'storeContact'])->middleware('throttle:contact')->name('contact.store');
        Route::get($paths['contents'], [ContentController::class, 'index'])->name('contents');
        Route::get($paths['content'], [ContentController::class, 'show'])->name('content');
    });
}

Route::redirect('/urunlerimiz', '/tr/urunler', 301);
Route::redirect('/hakkimizda', '/tr/hakkimizda', 301);
Route::redirect('/sertifikalarimiz', '/tr/sertifikalar', 301);
Route::redirect('/iletisim', '/tr/iletisim', 301);
Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
