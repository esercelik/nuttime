<?php

namespace App\Filament\Resources\Media\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dosya')->schema([
                    FileUpload::make('path')->label('Dosya')->required()->disk('public')->directory('media')->storeFileNamesIn('original_name')->getUploadedFileNameForStorageUsing(fn (TemporaryUploadedFile $file): string => Str::uuid().'.'.$file->extension())->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'application/pdf', 'video/mp4'])->maxSize(10240)->helperText('Dosya adı güvenli biçimde üretilir; PNG şeffaflığı dönüştürülmeden korunur.'),
                    TextInput::make('folder')->label('Klasör')->maxLength(120),
                    TextInput::make('title')->label('Başlık')->maxLength(160),
                    KeyValue::make('alt_texts')->label('Alt metinler')->helperText('tr, en ve de anahtarlarıyla erişilebilir alt metin girin.'),
                ])->columns(2),
            ]);
    }
}
