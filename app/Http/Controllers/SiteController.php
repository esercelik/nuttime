<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Support\CmsContentRepository;
use App\Support\LocalizedContent;
use App\Support\LocalizedUrl;
use App\Support\SeoMetadata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

final class SiteController extends Controller
{
    public function __construct(
        private SeoMetadata $seoMetadata,
        private LocalizedContent $localizedContent,
        private LocalizedUrl $localizedUrl,
        private CmsContentRepository $cmsContent,
    ) {}

    public function home(): View
    {
        $products = $this->catalog();
        $settings = $this->settings();

        return view('pages.home', [
            'products' => $products,
            'heroSlides' => $this->cmsContent->homeSlider(app()->getLocale()) ?: $this->heroSlides($products),
            'heroSliderSettings' => $this->cmsContent->homeSliderSettings(),
            'homeSections' => $this->cmsContent->homeSections(app()->getLocale()),
            'categories' => $this->categories(),
            'certificates' => $this->certificates(),
            'factory' => $this->factoryLocation($settings),
            'settings' => $settings,
            'seo' => $this->seo('meta.home', $this->localizedUrl->route('home'), $settings, [
                $this->seoMetadata->organization($settings),
                $this->seoMetadata->website(),
                $this->seoMetadata->localBusiness($settings),
            ]),
        ]);
    }

    public function products(): View
    {
        $settings = $this->settings();

        return view('products.index', [
            'products' => $this->catalog(),
            'settings' => $settings,
            'seo' => $this->seo('meta.products', $this->localizedUrl->route('products'), $settings),
        ]);
    }

    public function product(string $slug): View|RedirectResponse
    {
        $product = collect($this->catalog())->firstWhere('slug', $slug);

        if (! $product) {
            $product = collect($this->catalog())->first(fn (array $item): bool => in_array($slug, $item['previous_slugs'] ?? [], true));

            if ($product) {
                return redirect()->to($this->localizedUrl->route('product', null, ['slug' => $product['slug']]), 301);
            }
        }

        abort_unless($product, 404);

        $settings = $this->settings();
        $url = $this->localizedUrl->route('product', null, ['slug' => $product['slug']]);
        $breadcrumbs = $this->seoMetadata->breadcrumbs([
            ['name' => __('site.nav.home'), 'url' => $this->localizedUrl->route('home')],
            ['name' => __('site.nav.products'), 'url' => $this->localizedUrl->route('products')],
            ['name' => $product['name'], 'url' => $url],
        ]);

        return view('products.show', [
            'product' => $product,
            'related' => collect($this->catalog())->reject(fn (array $item): bool => $item['slug'] === $product['slug'])->take(2)->all(),
            'settings' => $settings,
            'breadcrumbs' => $breadcrumbs['itemListElement'],
            'seo' => $this->seoMetadata->page(
                $product['seo_title'] ?: $product['name'],
                $product['seo_description'] ?: $product['description'],
                $url,
                $settings,
                [$breadcrumbs, $this->seoMetadata->product($product, $settings, $url)],
                'product',
                $product['image'],
                $this->localizedUrl->alternatives('product', $product['slugs']),
            ),
        ]);
    }

    public function category(string $slug): View
    {
        $category = collect($this->categories())->firstWhere('slug', $slug);
        abort_unless($category, 404);

        $products = collect($this->catalog())->where('category_id', $category['id'])->values()->all();
        $settings = $this->settings();
        $url = $this->localizedUrl->route('category', null, ['slug' => $category['slug']]);
        $breadcrumbs = $this->seoMetadata->breadcrumbs([
            ['name' => __('site.nav.home'), 'url' => $this->localizedUrl->route('home')],
            ['name' => __('site.nav.categories'), 'url' => $this->localizedUrl->route('products')],
            ['name' => $category['name'], 'url' => $url],
        ]);

        return view('pages.category', [
            'category' => $category,
            'products' => $products,
            'breadcrumbs' => $breadcrumbs['itemListElement'],
            'seo' => $this->seoMetadata->page(
                $category['seo_title'] ?: $category['name'],
                $category['seo_description'] ?: $category['description'],
                $url,
                $settings,
                [$breadcrumbs],
                'website',
                $category['image'],
                $this->localizedUrl->alternatives('category', $category['slugs']),
            ),
        ]);
    }

    public function page(string $page): View
    {
        $settings = $this->settings();
        $key = $page === 'certificates' ? 'meta.certificates' : 'meta.about';

        return view('pages.static', [
            'page' => $page,
            'certificates' => $page === 'certificates' ? $this->certificates() : [],
            'seo' => $this->seo($key, $this->localizedUrl->route($page), $settings),
        ]);
    }

    public function contact(): View
    {
        $settings = $this->settings();

        return view('pages.contact', [
            'factory' => $this->factoryLocation($settings),
            'settings' => $settings,
            'seo' => $this->seo('meta.contact', $this->localizedUrl->route('contact'), $settings, [
                $this->seoMetadata->localBusiness($settings),
            ]),
        ]);
    }

    public function storeContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'message' => ['required', 'string', 'max:4000'],
            'website' => ['nullable', 'size:0'],
        ]);

        ContactMessage::create([
            ...collect($validated)->only(['name', 'email', 'message'])->all(),
            'phone' => $request->string('phone')->toString() ?: null,
            'subject' => $request->string('subject')->toString() ?: null,
            'locale' => app()->getLocale(),
        ]);

        return back()->with('success', __('site.contact.success'));
    }

    /** @return array<int, array<string, mixed>> */
    private function catalog(): array
    {
        if (Schema::hasTable('products')) {
            $products = Product::query()->with(['category.translations', 'translations'])->active()->orderBy('sort_order')->get();

            if ($products->isNotEmpty()) {
                return $products->map(fn (Product $product): array => $this->localizedContent->product($product) + [
                    'image' => $product->main_image ? asset('storage/'.$product->main_image) : $this->fallbackProductImage($product->name, $product->category?->name),
                ])->all();
            }
        }

        return $this->fallbackProducts();
    }

    /** @return array<int, array<string, mixed>> */
    private function categories(): array
    {
        if (Schema::hasTable('categories')) {
            $categories = Category::query()->with('translations')->where('is_active', true)->orderBy('sort_order')->get();

            if ($categories->isNotEmpty()) {
                return $categories->map(fn (Category $category): array => $this->localizedContent->category($category) + [
                    'image' => $category->image ? asset('storage/'.$category->image) : $this->fallbackProductImage($category->name, $category->name),
                ])->all();
            }
        }

        return [];
    }

    /** @return array<int, array<string, mixed>> */
    private function fallbackProducts(): array
    {
        $products = [
            'findik-kremasi' => ['name' => ['tr' => 'Fındık Ezmesi', 'en' => 'Hazelnut Butter', 'de' => 'Haselnusscreme'], 'description' => ['tr' => 'Özenle seçilmiş fındıklarla hazırlanan pürüzsüz, yoğun ve dengeli lezzet.', 'en' => 'A smooth, rich and balanced spread made with carefully selected hazelnuts.', 'de' => 'Eine cremige, intensive und ausgewogene Spezialität aus sorgfältig ausgewählten Haselnüssen.'], 'slug' => ['tr' => 'findik-kremasi', 'en' => 'hazelnut-butter', 'de' => 'haselnusscreme'], 'image' => 'hazelnut-butter.jpg'],
            'antep-fistikli-kremasi' => ['name' => ['tr' => 'Antep Fıstığı Ezmesi', 'en' => 'Pistachio Butter', 'de' => 'Pistaziencreme'], 'description' => ['tr' => 'Antep fıstığının karakteristik aromasıyla rafine bir deneyim.', 'en' => 'A refined experience with the distinctive aroma of pistachios.', 'de' => 'Ein raffiniertes Erlebnis mit dem unverwechselbaren Aroma von Pistazien.'], 'slug' => ['tr' => 'antep-fistikli-kremasi', 'en' => 'pistachio-butter', 'de' => 'pistaziencreme'], 'image' => 'pistachio-butter.jpg'],
            'badem-unu' => ['name' => ['tr' => 'Badem Ezmesi', 'en' => 'Almond Butter', 'de' => 'Mandelcreme'], 'description' => ['tr' => 'Özenle seçilmiş bademlerin yumuşak ve karakterli lezzeti.', 'en' => 'The gentle, distinctive flavour of carefully selected almonds.', 'de' => 'Der milde, charaktervolle Geschmack sorgfältig ausgewählter Mandeln.'], 'slug' => ['tr' => 'badem-ezmesi', 'en' => 'almond-butter', 'de' => 'mandelcreme'], 'image' => 'almond-butter.jpg'],
            'yer-fistigi-ezmesi' => ['name' => ['tr' => 'Yer Fıstığı Ezmesi', 'en' => 'Peanut Butter', 'de' => 'Erdnusscreme'], 'description' => ['tr' => 'Yoğun fıstık tadı ve parçacıklı dokusuyla günün her anına eşlik eder.', 'en' => 'A full peanut flavour and crunchy texture for every moment of the day.', 'de' => 'Voller Erdnussgeschmack und eine knackige Textur für jeden Moment des Tages.'], 'slug' => ['tr' => 'yer-fistigi-ezmesi', 'en' => 'peanut-butter', 'de' => 'erdnusscreme'], 'image' => 'peanut-butter.jpg'],
            'seker-ilavesiz-yer-fistigi-ezmesi' => ['name' => ['tr' => 'Şeker İlavesiz Yer Fıstığı Ezmesi', 'en' => 'No Added Sugar Peanut Butter', 'de' => 'Erdnusscreme ohne Zuckerzusatz'], 'description' => ['tr' => 'Şeker ilavesiz formülüyle sade, güçlü ve parçacıklı lezzet.', 'en' => 'A pure, powerful and crunchy recipe with no added sugar.', 'de' => 'Ein purer, kräftiger und knackiger Genuss ohne Zuckerzusatz.'], 'slug' => ['tr' => 'seker-ilavesiz-yer-fistigi-ezmesi', 'en' => 'no-added-sugar-peanut-butter', 'de' => 'erdnusscreme-ohne-zuckerzusatz'], 'image' => 'peanut-butter.jpg'],
            'hindistan-cevizi-ezmesi' => ['name' => ['tr' => 'Hindistan Cevizi Ezmesi', 'en' => 'Coconut Butter', 'de' => 'Kokoscreme'], 'description' => ['tr' => 'Hafif, aromatik ve tropikal bir lezzet.', 'en' => 'A light, aromatic taste with a tropical character.', 'de' => 'Ein leichter, aromatischer Genuss mit tropischem Charakter.'], 'slug' => ['tr' => 'hindistan-cevizi-ezmesi', 'en' => 'coconut-butter', 'de' => 'kokoscreme'], 'image' => 'coconut-butter.jpg'],
        ];

        return collect($products)->map(function (array $product, string $key): array {
            $locale = app()->getLocale();

            return ['id' => $key, 'slug' => $product['slug'][$locale], 'slugs' => $product['slug'], 'name' => $product['name'][$locale], 'category' => __('site.catalog.default_category'), 'category_slug' => null, 'description' => $product['description'][$locale], 'featured' => true, 'accent' => '#d7b66c', 'image' => asset('images/nuttime/'.$product['image']), 'image_alt' => $product['name'][$locale].' - Nuttime', 'gallery' => []];
        })->values()->all();
    }

    /** @return array<int, array<string, mixed>> */
    private function heroSlides(array $products): array
    {
        return collect($products)->map(function (array $product): array {
            $asset = match ($product['id'] ?? '') {
                'yer-fistigi-ezmesi' => 'yer-fistigi',
                'findik-kremasi' => 'findik',
                'antep-fistikli-kremasi' => 'antep',
                'badem-unu' => 'badem',
                'seker-ilavesiz-yer-fistigi-ezmesi' => 'seker-ilavesiz-yer-fistigi',
                'hindistan-cevizi-ezmesi' => 'hindistan-cevizi',
                default => null,
            };
            $path = $asset ? 'images/nuttime/spylt/nuttime-'.$asset : null;

            return ['slug' => $product['slug'], 'name' => __('site.hero.'.($product['id'] ?? '').'.title', ['product' => $product['name']]), 'category' => __('site.hero.'.($product['id'] ?? '').'.eyebrow', ['product' => $product['name']]), 'description' => $product['description'], 'url' => $this->localizedUrl->route('product', null, ['slug' => $product['slug']]), 'background_image' => $path ? asset($path.'-hero-background.png') : $product['image'], 'ingredient_image' => $path ? asset($path.'-ingredient-elements-transparent.png') : $product['image'], 'product_image' => $path ? asset($path.'-jar-transparent.png') : $product['image']];
        })->all();
    }

    /** @return array<string, mixed> */
    private function settings(): array
    {
        return Schema::hasTable('site_settings') ? SiteSetting::current()->toArray() : ['site_name' => 'Nuttime'];
    }

    /** @param array<string, mixed> $settings @return array<string, mixed> */
    private function factoryLocation(array $settings): array
    {
        $enabled = (bool) ($settings['factory_map_enabled'] ?? false);
        $url = filter_var($settings['factory_google_maps_url'] ?? null, FILTER_VALIDATE_URL);
        $hasCoordinates = is_numeric($settings['factory_map_latitude'] ?? null) && is_numeric($settings['factory_map_longitude'] ?? null);
        $key = config('services.google_maps.embed_api_key');

        return ['enabled' => $enabled && filled($settings['factory_address'] ?? null), 'name' => $settings['factory_name'] ?? '', 'address' => $settings['factory_address'] ?? '', 'url' => $url && str_starts_with($url, 'https://') ? $url : null, 'embed_url' => $enabled && $hasCoordinates && $key ? 'https://www.google.com/maps/embed/v1/place?key='.rawurlencode($key).'&q='.rawurlencode($settings['factory_map_latitude'].','.$settings['factory_map_longitude']) : null];
    }

    /** @return array<int, array<string, mixed>> */
    private function certificates(): array
    {
        if (! Schema::hasTable('certificates')) {
            return [];
        }

        return Certificate::query()->with('translations')->active()->orderBy('sort_order')->get()->map(function (Certificate $certificate): array {
            $translation = $certificate->translationFor(app()->getLocale());
            $image = $translation?->image ?: $certificate->image;

            return [
                'name' => $translation?->name ?: $certificate->name,
                'description' => $translation?->description ?: $certificate->description,
                'image' => $image ? asset('storage/'.$image) : null,
                'document' => $certificate->document_file ? asset('storage/'.$certificate->document_file) : $certificate->document_url,
            ];
        })->all();
    }

    private function fallbackProductImage(?string $name, ?string $category = null): string
    {
        $label = mb_strtolower(($name ?? '').' '.($category ?? ''), 'UTF-8');

        return match (true) {
            str_contains($label, 'antep'), str_contains($label, 'pistachio') => asset('images/nuttime/pistachio-butter.jpg'),
            str_contains($label, 'badem'), str_contains($label, 'almond') => asset('images/nuttime/almond-butter.jpg'),
            str_contains($label, 'hindistan'), str_contains($label, 'coconut') => asset('images/nuttime/coconut-butter.jpg'),
            str_contains($label, 'fındık'), str_contains($label, 'hazelnut') => asset('images/nuttime/hazelnut-butter.jpg'),
            default => asset('images/nuttime/peanut-butter.jpg'),
        };
    }

    /** @param array<string, mixed> $settings @param array<int, array<string, mixed>|null> $schemas @return array<string, mixed> */
    private function seo(string $translationKey, string $canonical, array $settings, array $schemas = []): array
    {
        return $this->seoMetadata->page(__("site.$translationKey.title"), __("site.$translationKey.description"), $canonical, $settings, array_values(array_filter($schemas)), 'website', null, $this->localizedUrl->alternatives(str_replace('meta.', '', $translationKey)));
    }
}
