<?php

namespace App\Filament\Resources\Sliders\Schemas;

use App\Support\MediaLibraryOptions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SliderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([Tabs::make('Slider')->tabs([
                Tab::make('Genel')->schema([
                    Section::make('Slider ayarları')->schema([
                        TextInput::make('key')->required()->unique(ignoreRecord: true)->helperText('Ana sayfa için sabit anahtar: home.'),
                        TextInput::make('name')->required(),
                        Select::make('status')->options(['draft' => 'Taslak', 'published' => 'Yayında', 'archived' => 'Arşiv'])->default('published')->required(),
                        Toggle::make('is_active')->default(true),
                        TextInput::make('settings.autoplay_ms')->numeric()->minValue(2000)->maxValue(30000)->label('Otomatik geçiş (ms)')->default(6500),
                        Toggle::make('settings.loop')->label('Döngü')->default(true),
                        Toggle::make('settings.show_arrows')->label('Oklar')->default(true),
                        Toggle::make('settings.show_counter')->label('Sayaç')->default(true),
                        Toggle::make('settings.show_progress')->label('Progress çizgisi')->default(true),
                        Toggle::make('settings.swipe')->label('Kaydırma (swipe)')->default(true),
                    ])->columns(2),
                ]),
                Tab::make('Slaytlar')->schema([
                    Repeater::make('items')->relationship()->orderColumn('sort_order')->reorderableWithButtons()->collapsed()->itemLabel(fn (array $state): ?string => $state['background_image'] ?? 'Yeni slayt')->schema([
                        Select::make('product_id')->relationship('product', 'name')->searchable()->preload()->label('Bağlı ürün'),
                        Select::make('status')->options(['draft' => 'Taslak', 'published' => 'Yayında', 'archived' => 'Arşiv'])->default('published')->required(),
                        Toggle::make('is_active')->label('Aktif')->default(true),
                        Select::make('background_image')->options(fn (): array => MediaLibraryOptions::images())->searchable()->required(),
                        Select::make('mobile_background_image')->options(fn (): array => MediaLibraryOptions::images())->searchable(),
                        Select::make('product_image')->options(fn (): array => MediaLibraryOptions::images())->searchable()->helperText('Şeffaf PNG/WEBP kaynak dosyası değiştirilmeden kullanılır.'),
                        Select::make('decoration_image')->options(fn (): array => MediaLibraryOptions::images())->searchable(),
                        Select::make('mobile_decoration_image')->options(fn (): array => MediaLibraryOptions::images())->searchable(),
                        TextInput::make('background_color')->regex('/^#[0-9A-Fa-f]{6}$/')->placeholder('#1A2606'),
                        TextInput::make('text_color')->regex('/^#[0-9A-Fa-f]{6}$/')->placeholder('#FFFAF0'),
                        TextInput::make('accent_color')->regex('/^#[0-9A-Fa-f]{6}$/')->placeholder('#EED17F'),
                        DateTimePicker::make('published_from')->label('Yayın başlangıcı'),
                        DateTimePicker::make('published_until')->label('Yayın bitişi')->after('published_from'),
                        Repeater::make('translations')->relationship()->minItems(3)->maxItems(3)->defaultItems(3)->schema([
                            Select::make('locale')->options(['tr' => 'Türkçe', 'en' => 'English', 'de' => 'Deutsch'])->required()->distinct(),
                            TextInput::make('eyebrow')->label('Üst etiket'),
                            TextInput::make('title')->label('Başlık')->required(),
                            Textarea::make('description')->label('Açıklama')->rows(3),
                            TextInput::make('cta_label')->label('CTA metni'),
                            TextInput::make('cta_url')->label('CTA bağlantısı')->maxLength(2048),
                        ])->columns(2),
                    ])->columns(2),
                ]),
            ])]);
    }
}
