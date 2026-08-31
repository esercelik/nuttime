<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/urunlerimiz', [SiteController::class, 'products'])->name('products');
Route::get('/urunler/{slug}', [SiteController::class, 'product'])->name('product');
Route::get('/kategori/{slug}', [SiteController::class, 'category'])->name('category');
Route::get('/hakkimizda', fn () => app(SiteController::class)->page('about'))->name('about');
Route::get('/sertifikalarimiz', fn () => app(SiteController::class)->page('certificates'))->name('certificates');
Route::get('/iletisim', [SiteController::class, 'contact'])->name('contact');
Route::post('/iletisim', [SiteController::class, 'storeContact'])->middleware('throttle:contact')->name('contact.store');
Route::prefix('{locale}')->whereIn('locale', ['en', 'de'])->middleware('locale')->group(function () {
    Route::get('/', [SiteController::class, 'home']);
    Route::get('/products', [SiteController::class, 'products']);
    Route::get('/products/{slug}', [SiteController::class, 'product']);
    Route::get('/category/{slug}', [SiteController::class, 'category']);
    Route::get('/about-us', fn () => app(SiteController::class)->page('about'));
    Route::get('/certificates', fn () => app(SiteController::class)->page('certificates'));
    Route::get('/contact', [SiteController::class, 'contact']);
    Route::post('/contact', [SiteController::class, 'storeContact'])->middleware('throttle:contact');
});
Route::redirect('/en/ourproducts/', '/en/products', 301);
Route::redirect('/de/ourprodukte/', '/de/produkte', 301);
Route::redirect('/de/kommunikation/', '/de/kontakt', 301);
Route::get('/robots.txt', fn () => response("User-agent: *\nAllow: /\nDisallow: /admin\n", 200, ['Content-Type' => 'text/plain']));
