<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([Tabs::make('Menü')->tabs([
                Tab::make('Genel')->schema([
                    Section::make('Menü yerleşimi')->schema([
                        TextInput::make('key')->required()->unique(ignoreRecord: true)->helperText('Örn. header-primary, footer-legal.'),
                        TextInput::make('name')->required(),
                        Select::make('location')->options(['header' => 'Header', 'footer' => 'Footer', 'legal' => 'Yasal bağlantılar'])->required(),
                        Toggle::make('is_active')->label('Aktif')->default(true),
                    ])->columns(2),
                ]),
                Tab::make('Bağlantılar')->schema([
                    Repeater::make('items')->relationship()->orderColumn('sort_order')->reorderableWithButtons()->collapsed()->schema([
                        Select::make('link_type')->options(['internal' => 'Dahili rota', 'external' => 'Harici URL'])->default('internal')->required()->live(),
                        Select::make('route_name')->label('Dahili rota')->options(['home' => 'Ana sayfa', 'products' => 'Ürünler', 'about' => 'Hakkımızda', 'certificates' => 'Sertifikalar', 'contact' => 'İletişim', 'contents' => 'İçerikler'])->visible(fn ($get): bool => $get('link_type') === 'internal'),
                        TextInput::make('url')->label('Harici URL')->url()->maxLength(2048)->visible(fn ($get): bool => $get('link_type') === 'external'),
                        Toggle::make('open_in_new_tab')->label('Yeni sekmede aç'),
                        Toggle::make('is_active')->label('Aktif')->default(true),
                        Repeater::make('translations')->relationship()->minItems(3)->maxItems(3)->defaultItems(3)->schema([
                            Select::make('locale')->options(['tr' => 'Türkçe', 'en' => 'English', 'de' => 'Deutsch'])->required()->distinct(),
                            TextInput::make('label')->label('Menü metni')->required(),
                        ])->columns(2),
                    ])->columns(2),
                ]),
            ])]);
    }
}
