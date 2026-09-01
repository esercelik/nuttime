<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Content;
use App\Models\Media;
use App\Models\PageSection;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CatalogOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Toplam ürün', Product::count())->color('gray'),
            Stat::make('Aktif ürün', Product::active()->count())->color('success'),
            Stat::make('Öne çıkan', Product::where('is_featured', true)->count())->color('warning'),
            Stat::make('Toplam kategori', Category::count())->color('info'),
            Stat::make('Yayındaki içerik', Content::published()->count())->color('success'),
            Stat::make('Ana sayfa bölümü', PageSection::query()->published()->where('page_key', 'home')->count())->color('info'),
            Stat::make('Medya dosyası', Media::count())->color('gray'),
            Stat::make('Okunmamış mesaj', ContactMessage::query()->where('is_read', false)->whereNull('archived_at')->count())->color('warning'),
            Stat::make('Süresi yaklaşan belge', Certificate::query()->whereNotNull('expires_at')->whereBetween('expires_at', [today(), today()->addDays(30)])->count())->color('danger'),
        ];
    }
}
