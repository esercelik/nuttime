<?php

namespace App\Filament\Resources\PageSections\Schemas;

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

class PageSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([Tabs::make('Bölüm')->tabs([
                Tab::make('Genel')->schema([
                    Section::make('Yayın ve görünüm')->schema([
                        TextInput::make('page_key')->required()->default('home')->helperText('Ana sayfa için home.'),
                        TextInput::make('key')->required()->helperText('Örn. banners, brand_story, final_cta.'),
                        Select::make('type')->required()->live()->options(['banner' => 'Banner', 'intro' => 'Giriş', 'featured_products' => 'Öne çıkan ürünler', 'categories' => 'Kategoriler', 'story' => 'Hikâye', 'certificates' => 'Sertifikalar', 'factory' => 'Fabrika', 'social' => 'Sosyal medya', 'cta' => 'Çağrı alanı', 'custom' => 'Güvenli özel varyant']),
                        TextInput::make('variant')->helperText('Önceden tanımlı görünüm varyantı.'),
                        Select::make('status')->options(['draft' => 'Taslak', 'published' => 'Yayında', 'archived' => 'Arşiv'])->default('published')->required(),
                        Toggle::make('is_active')->label('Aktif')->default(true),
                        DateTimePicker::make('published_from')->label('Yayın başlangıcı'),
                        DateTimePicker::make('published_until')->label('Yayın bitişi')->after('published_from'),
                    ])->columns(2),
                ]),
                Tab::make('İçerik ve medya')->schema([
                    Section::make('Görseller ve tokenlar')->schema([
                        Select::make('desktop_image')->options(fn (): array => MediaLibraryOptions::images())->searchable(),
                        Select::make('mobile_image')->options(fn (): array => MediaLibraryOptions::images())->searchable(),
                        TextInput::make('video_url')->url()->maxLength(2048),
                        TextInput::make('background_color')->regex('/^#[0-9A-Fa-f]{6}$/')->placeholder('#171714'),
                        TextInput::make('text_color')->regex('/^#[0-9A-Fa-f]{6}$/')->placeholder('#F7F3E8'),
                        TextInput::make('accent_color')->regex('/^#[0-9A-Fa-f]{6}$/')->placeholder('#D8B768'),
                        TextInput::make('settings.limit')->numeric()->minValue(1)->maxValue(12)->visible(fn ($get): bool => in_array($get('type'), ['featured_products', 'categories'], true))->helperText('Gösterilecek maksimum kayıt sayısı.'),
                        Toggle::make('settings.overlay')->visible(fn ($get): bool => $get('type') === 'banner')->helperText('Banner üzerinde koyu katman göster.'),
                        Select::make('settings.network')->options(['instagram' => 'Instagram', 'facebook' => 'Facebook', 'youtube' => 'YouTube'])->visible(fn ($get): bool => $get('type') === 'social'),
                        Select::make('settings.variant')->options(['editorial' => 'Editoryal', 'compact' => 'Kompakt'])->visible(fn ($get): bool => $get('type') === 'custom'),
                    ])->columns(2),
                ]),
                Tab::make('Diller')->schema([
                    Repeater::make('translations')->relationship()->minItems(3)->maxItems(3)->defaultItems(3)->schema([
                        Select::make('locale')->options(['tr' => 'Türkçe', 'en' => 'English', 'de' => 'Deutsch'])->required()->distinct(),
                        TextInput::make('eyebrow')->label('Üst başlık'),
                        TextInput::make('title')->label('Başlık'),
                        Textarea::make('description')->label('Açıklama')->rows(4),
                        TextInput::make('button_label')->label('Buton metni'),
                        TextInput::make('button_url')->label('Buton bağlantısı')->maxLength(2048),
                    ])->columns(2),
                ]),
            ])]);
    }
}
