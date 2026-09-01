<?php

namespace App\Filament\Resources\Media\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dosya')->schema([
                    FileUpload::make('path')->label('Dosya')->required()->disk('public')->directory('media')->preserveFilenames()->storeFileNamesIn('original_name')->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'application/pdf', 'video/mp4'])->maxSize(10240)->helperText('PNG şeffaflığı korunur. Orijinal dosya saklanır.'),
                    TextInput::make('folder')->label('Klasör')->maxLength(120),
                    TextInput::make('title')->label('Başlık')->maxLength(160),
                    KeyValue::make('alt_texts')->label('Alt metinler')->helperText('tr, en ve de anahtarlarıyla erişilebilir alt metin girin.'),
                ])->columns(2),
            ]);
    }
}
