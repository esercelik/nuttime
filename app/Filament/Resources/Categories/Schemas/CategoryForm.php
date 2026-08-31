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
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Textarea::make('description')->label('Açıklama'),
                FileUpload::make('image')->label('Görsel')->image()->disk('public')->directory('categories')->maxSize(5120),
                Toggle::make('is_active')->label('Aktif')->default(true),
                TextInput::make('sort_order')->label('Sıralama')->numeric()->default(0),
            ]);
    }
}
