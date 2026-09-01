<?php

namespace App\Filament\Resources\Certificates\Schemas;

use App\Support\MediaLibraryOptions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
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
                TextInput::make('issuer')->label('Veren kuruluş')->maxLength(160),
                TextInput::make('certificate_number')->label('Sertifika numarası')->maxLength(120),
                DatePicker::make('issued_at')->label('Veriliş tarihi'),
                DatePicker::make('expires_at')->label('Bitiş tarihi')->after('issued_at'),
                Select::make('image')->label('Sertifika görseli')->options(fn (): array => MediaLibraryOptions::images())->searchable(),
                Select::make('document_file')->label('Belge (PDF)')->options(fn (): array => MediaLibraryOptions::documents())->searchable(),
                TextInput::make('document_url')->label('Belge bağlantısı')->url()->maxLength(2048),
                Toggle::make('is_active')->label('Aktif')->default(true), TextInput::make('sort_order')->label('Sıralama')->numeric()->default(0),
                Repeater::make('translations')->relationship()->minItems(3)->maxItems(3)->defaultItems(3)->schema([
                    Select::make('locale')->label('Dil')->options(['tr' => 'Türkçe', 'en' => 'English', 'de' => 'Deutsch'])->required()->distinct(),
                    TextInput::make('name')->label('Sertifika adı')->required(),
                    Textarea::make('description')->label('Açıklama')->rows(3),
                ])->columns(2),
            ]);
    }
}
