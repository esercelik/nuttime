<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([Section::make('Mesaj yönetimi')->schema([
                Toggle::make('is_read')->label('Okundu'),
                Toggle::make('is_answered')->label('Cevaplandı'),
                Toggle::make('archived_at')->label('Arşivle')->formatStateUsing(fn ($state): bool => filled($state))->dehydrateStateUsing(fn (bool $state): ?\Carbon\Carbon => $state ? now() : null),
                Textarea::make('internal_note')->label('Dahili not')->rows(5)->maxLength(4000),
            ])]);
    }
}
