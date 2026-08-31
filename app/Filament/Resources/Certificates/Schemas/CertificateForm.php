<?php

namespace App\Filament\Resources\Certificates\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Sertifika adı')->required()->maxLength(160),
                Textarea::make('description')->label('Açıklama')->rows(4),
                FileUpload::make('image')->label('Sertifika görseli')->image()->disk('public')->directory('certificates')->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(5120),
                FileUpload::make('document_file')->label('Belge (PDF)')->disk('public')->directory('certificates/documents')->acceptedFileTypes(['application/pdf'])->maxSize(10240),
                TextInput::make('document_url')->label('Belge bağlantısı')->url()->maxLength(2048),
                Toggle::make('is_active')->label('Aktif')->default(true), TextInput::make('sort_order')->label('Sıralama')->numeric()->default(0),
            ]);
    }
}
