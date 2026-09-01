<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
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
                Repeater::make('translations')->relationship()->schema([
                    Select::make('locale')->label('Dil')->options(['tr' => 'Türkçe', 'en' => 'English', 'de' => 'Deutsch'])->required()->distinct(),
                    TextInput::make('name')->label('Kategori adı')->required()->maxLength(255),
                    TextInput::make('slug')->label('Dil bazlı slug')->required()->maxLength(255),
                    Textarea::make('description')->label('Açıklama')->rows(4),
                    TextInput::make('meta_title')->label('SEO başlığı')->maxLength(60),
                    Textarea::make('meta_description')->label('SEO açıklaması')->maxLength(160),
                ])->columns(2)->defaultItems(3)->minItems(3)->maxItems(3),
            ]);
    }
}
