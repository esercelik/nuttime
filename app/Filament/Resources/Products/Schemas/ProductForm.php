<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ürün bilgileri')->schema([
                    TextInput::make('name')->label('Ürün adı')->required()->maxLength(255),
                    TextInput::make('slug')->required()->unique(ignoreRecord: true),
                    Select::make('category_id')->label('Kategori')->relationship('category', 'name')->searchable()->preload(),
                    TextInput::make('sku')->label('SKU')->unique(ignoreRecord: true),
                    Textarea::make('short_description')->label('Kısa açıklama')->rows(3),
                    Textarea::make('description')->label('Açıklama')->rows(6),
                ])->columns(2),
                Section::make('Görseller')->schema([
                    FileUpload::make('main_image')->label('Ana görsel')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->disk('public')->directory('products')->maxSize(5120),
                    FileUpload::make('additional_images')->label('Ek görseller')->image()->multiple()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->disk('public')->directory('products')->maxSize(5120),
                ])->columns(2),
                Section::make('Yayın ve e-ticarete hazırlık')->schema([
                    Toggle::make('is_active')->label('Aktif')->default(true), Toggle::make('is_featured')->label('Öne çıkan'),
                    TextInput::make('sort_order')->label('Sıralama')->numeric()->default(0), TextInput::make('price')->numeric()->prefix('₺'),
                    TextInput::make('compare_price')->numeric()->prefix('₺'), TextInput::make('stock')->numeric()->integer(), Toggle::make('stock_tracking')->label('Stok takibi'),
                ])->columns(3),
                Section::make('SEO')->schema([TextInput::make('seo_title')->label('SEO başlığı'), Textarea::make('seo_description')->label('SEO açıklaması')]),
            ]);
    }
}
