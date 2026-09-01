<?php

namespace App\Filament\Resources\Contents\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('İçerik')->schema([
                    TextInput::make('title')->label('Başlık')->required()->maxLength(255),
                    TextInput::make('slug')->label('SEO URL')->required()->unique(ignoreRecord: true)->rule('regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'),
                    Textarea::make('excerpt')->label('Özet')->rows(3)->maxLength(400),
                    Textarea::make('body')->label('İçerik')->rows(12),
                ])->columns(2),
                Section::make('Kapak ve yayın')->schema([
                    FileUpload::make('cover_image')->label('Kapak görseli')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->disk('public')->directory('contents')->maxSize(5120),
                    TextInput::make('cover_image_alt')->label('Kapak görseli alt metni')->maxLength(160),
                    Select::make('status')->label('Durum')->options(['draft' => 'Taslak', 'published' => 'Yayınlandı'])->default('draft')->required(),
                    DateTimePicker::make('published_at')->label('Yayın tarihi')->seconds(false),
                ])->columns(2),
                Section::make('SEO')->schema([
                    TextInput::make('seo_title')->label('SEO başlığı')->maxLength(60)->helperText('Boşsa içerik başlığı kullanılır.'),
                    Textarea::make('seo_description')->label('SEO açıklaması')->maxLength(160)->helperText('Boşsa özetten oluşturulur.'),
                    TextInput::make('seo_canonical')->label('Canonical URL')->url()->maxLength(2048),
                ]),
            ]);
    }
}
