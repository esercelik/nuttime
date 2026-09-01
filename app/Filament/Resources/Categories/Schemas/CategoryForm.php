<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Kategori adı')->required(),
                TextInput::make('slug')->label('SEO URL')->required()->unique(ignoreRecord: true)->rule('regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'),
                Textarea::make('description')->label('Kategori açıklaması')->helperText('Kategori sayfasında görünür; özgün ve faydalı yazın.'),
                FileUpload::make('image')->label('Görsel')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->disk('public')->directory('categories')->maxSize(5120),
                TextInput::make('image_alt')->label('Görsel alt metni')->maxLength(160),
                TextInput::make('seo_title')->label('SEO başlığı')->maxLength(60)->helperText('Boşsa kategori adı kullanılır.'),
                Textarea::make('seo_description')->label('SEO açıklaması')->maxLength(160)->helperText('Boşsa kategori açıklaması kullanılır.'),
                TextInput::make('seo_canonical')->label('Canonical URL')->url()->maxLength(2048),
                Toggle::make('is_active')->label('Aktif')->default(true),
                TextInput::make('sort_order')->label('Sıralama')->numeric()->default(0),
            ]);
    }
}
