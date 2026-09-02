<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Support\MediaLibraryOptions;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ürün bilgileri')->schema([
                    TextInput::make('name')->label('Ürün adı')->required()->maxLength(255),
                    TextInput::make('slug')->label('SEO URL')->required()->unique(ignoreRecord: true)->rule('regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/')->helperText('Kısa, küçük harfli ve Türkçe karaktersiz olmalıdır.'),
                    Select::make('category_id')->label('Kategori')->relationship('category', 'name')->searchable()->preload(),
                    TextInput::make('sku')->label('SKU')->unique(ignoreRecord: true),
                    Textarea::make('short_description')->label('Kısa açıklama')->rows(3),
                    Textarea::make('description')->label('Açıklama')->rows(6),
                ])->columns(2),
                Section::make('Görseller')->schema([
                    Select::make('main_image')->label('Ana görsel')->options(fn (): array => MediaLibraryOptions::images())->searchable(),
                    Select::make('social_image')->label('Sosyal paylaşım görseli')->options(fn (): array => MediaLibraryOptions::images())->searchable(),
                    TextInput::make('main_image_alt')->label('Ana görsel alt metni')->maxLength(160)->helperText('Boşsa ürün adı ve kategoriden otomatik oluşturulur.'),
                    Select::make('additional_images')->label('Ek görseller')->options(fn (): array => MediaLibraryOptions::images())->multiple()->searchable(),
                ])->columns(2),
                Section::make('İçerik ve e-ticarete hazırlık')->schema([
                    Toggle::make('is_active')->label('Aktif')->default(true), Toggle::make('is_featured')->label('Öne çıkan'),
                    TextInput::make('sort_order')->label('Sıralama')->numeric()->default(0), TextInput::make('weight_grams')->label('Gramaj')->numeric()->suffix('g'),
                    TextInput::make('primary_ingredient_percentage')->label('Ana içerik oranı')->numeric()->suffix('%')->minValue(0)->maxValue(100),
                    TagsInput::make('feature_tags')->label('Özellik etiketleri')->helperText('Örn. Şeker ilavesiz, Vegan.'),
                    KeyValue::make('nutrition_facts')->label('Besin değerleri')->helperText('Değerleri güvenli anahtar/değer çiftleri olarak girin.'),
                    KeyValue::make('packaging_details')->label('Ambalaj bilgileri')->helperText('Gruplu görünüm için anahtarları jar.net_weight veya carton.units biçiminde girin.'),
                    TextInput::make('price')->numeric()->prefix('₺'),
                    TextInput::make('compare_price')->numeric()->prefix('₺'), TextInput::make('stock')->numeric()->integer(), Toggle::make('stock_tracking')->label('Stok takibi'),
                ])->columns(3),
                Section::make('SEO')->schema([
                    TextInput::make('seo_title')->label('SEO başlığı')->maxLength(60)->helperText('Boşsa ürün adı kullanılır.'),
                    Textarea::make('seo_description')->label('SEO açıklaması')->maxLength(160)->helperText('Boşsa ürün açıklamasından oluşturulur.'),
                    TextInput::make('seo_canonical')->label('Canonical URL')->url()->maxLength(2048)->helperText('Yalnızca farklı bir asıl URL gerekiyorsa doldurun.'),
                    TagsInput::make('previous_slugs')->label('Eski sluglar')->helperText('Her eski slug için kalıcı 301 yönlendirmesi oluşturulur.'),
                ]),
                Section::make('Dil bazlı içerik')->description('Eksik içerik English, ardından Türkçe değerine döner.')->schema([
                    Repeater::make('translations')->relationship()->schema([
                        Select::make('locale')->label('Dil')->options(collect(config('nuttime.locales'))->mapWithKeys(fn (array $locale, string $key): array => [$key => $locale['label']])->all())->required()->distinct(),
                        TextInput::make('name')->label('Ürün adı')->required()->maxLength(255),
                        TextInput::make('slug')->label('Dil bazlı slug')->required()->maxLength(255),
                        Textarea::make('short_description')->label('Kısa açıklama')->rows(3),
                        Textarea::make('description')->label('Açıklama')->rows(5),
                        Textarea::make('ingredients')->label('İçindekiler')->rows(3),
                        Textarea::make('allergen_information')->label('Alerjenler')->rows(3),
                        TextInput::make('meta_title')->label('SEO başlığı')->maxLength(60),
                        Textarea::make('meta_description')->label('SEO açıklaması')->maxLength(160),
                    ])->columns(2)->defaultItems(count(config('nuttime.locales')))->minItems(count(config('nuttime.locales')))->maxItems(count(config('nuttime.locales'))),
                ]),
            ]);
    }
}
