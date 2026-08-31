<?php

namespace App\Filament\Widgets;

use App\Models\Category;
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
        ];
    }
}
