<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([Section::make('İletişim mesajı')->schema([
                TextEntry::make('name')->label('Ad soyad'),
                TextEntry::make('email')->label('E-posta'),
                TextEntry::make('phone')->label('Telefon')->placeholder('-'),
                TextEntry::make('subject')->label('Konu')->placeholder('-'),
                TextEntry::make('locale')->label('Dil'),
                TextEntry::make('created_at')->label('Tarih')->dateTime(),
                TextEntry::make('message')->label('Mesaj')->columnSpanFull(),
                TextEntry::make('internal_note')->label('Dahili not')->placeholder('-')->columnSpanFull(),
            ])->columns(2)]);
    }
}
