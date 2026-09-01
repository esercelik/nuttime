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
                    TextInput::make('slug')->label('SEO URL')->required()->unique(ignoreRecord: true)->rule('regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/')->helperText('Kısa, küçük harfli ve Türkçe karaktersiz olmalıdır.'),
                    Select::make('category_id')->label('Kategori')->relationship('category', 'name')->searchable()->preload(),
                    TextInput::make('sku')->label('SKU')->unique(ignoreRecord: true),
                    Textarea::make('short_description')->label('Kısa açıklama')->rows(3),
                    Textarea::make('description')->label('Açıklama')->rows(6),
                ])->columns(2),
                Section::make('Görseller')->schema([
                    FileUpload::make('main_image')->label('Ana görsel')->image()->imageResizeMode('contain')->imageResizeTargetWidth(2000)->imageResizeUpscale(false)->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->disk('public')->directory('products')->maxSize(5120),
                    TextInput::make('main_image_alt')->label('Ana görsel alt metni')->maxLength(160)->helperText('Boşsa ürün adı ve kategoriden otomatik oluşturulur.'),
                    FileUpload::make('additional_images')->label('Ek görseller')->image()->multiple()->imageResizeMode('contain')->imageResizeTargetWidth(2000)->imageResizeUpscale(false)->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->disk('public')->directory('products')->maxSize(5120),
                ])->columns(2),
                Section::make('Yayın ve e-ticarete hazırlık')->schema([
                    Toggle::make('is_active')->label('Aktif')->default(true), Toggle::make('is_featured')->label('Öne çıkan'),
                    TextInput::make('sort_order')->label('Sıralama')->numeric()->default(0), TextInput::make('price')->numeric()->prefix('₺'),
                    TextInput::make('compare_price')->numeric()->prefix('₺'), TextInput::make('stock')->numeric()->integer(), Toggle::make('stock_tracking')->label('Stok takibi'),
                ])->columns(3),
                Section::make('SEO')->schema([
                    TextInput::make('seo_title')->label('SEO başlığı')->maxLength(60)->helperText('Boşsa ürün adı kullanılır.'),
                    Textarea::make('seo_description')->label('SEO açıklaması')->maxLength(160)->helperText('Boşsa ürün açıklamasından oluşturulur.'),
                    TextInput::make('seo_canonical')->label('Canonical URL')->url()->maxLength(2048)->helperText('Yalnızca farklı bir asıl URL gerekiyorsa doldurun.'),
                    Textarea::make('previous_slugs')->label('Eski sluglar')->rows(2)->helperText('Birden fazla eski slugı virgülle ayırın; 301 yönlendirmesi için kullanılır.'),
                ]),
            ]);
    }
}
