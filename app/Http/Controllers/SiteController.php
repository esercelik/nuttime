<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Support\SeoMetadata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SiteController extends Controller
{
    public function __construct(private SeoMetadata $seoMetadata) {}

    private function catalog(): array
    {
        $managedProducts = Schema::hasTable('products')
            ? Product::query()->with('category')->active()->orderBy('sort_order')->get()
            : collect();

        if ($managedProducts->isNotEmpty()) {
            return $managedProducts->map(fn (Product $product) => [
                'slug' => $product->slug,
                'name' => ['tr' => $product->name, 'en' => $product->name, 'de' => $product->name],
                'category' => $product->category?->name ?? 'Nut Creams',
                'description' => $product->short_description ?? $product->description ?? '',
                'seo_title' => $product->seo_title,
                'seo_description' => $product->seo_description,
                'seo_canonical' => $product->seo_canonical,
                'previous_slugs' => $product->previous_slugs ?? [],
                'sku' => $product->sku,
                'price' => $product->price,
                'stock' => $product->stock,
                'stock_tracking' => $product->stock_tracking,
                'featured' => $product->is_featured,
                'accent' => '#d7b66c',
                'image' => $product->main_image
                    ? asset('storage/'.$product->main_image)
                    : $this->fallbackProductImage($product->name, $product->category?->name),
                'image_alt' => $product->main_image_alt ?: trim($product->name.' - '.($product->category?->name ?? 'Nuttime')),
                'gallery' => collect($product->additional_images ?? [])
                    ->filter()
                    ->map(fn (string $image) => asset('storage/'.$image))
                    ->values()
                    ->all(),
            ])->all();
        }

        return [
            ['slug' => 'findik-kremasi', 'name' => ['tr' => 'Fındık Ezmesi', 'en' => 'Hazelnut Butter', 'de' => 'Haselnusscreme'], 'category' => 'Nut Creams', 'description' => 'Özenle seçilmiş fındıklarla hazırlanan pürüzsüz, yoğun ve dengeli lezzet.', 'featured' => true, 'accent' => '#c6a36e', 'image' => asset('images/nuttime/hazelnut-butter.jpg'), 'gallery' => []],
            ['slug' => 'antep-fistikli-kremasi', 'name' => ['tr' => 'Antep Fıstığı Ezmesi', 'en' => 'Pistachio Butter', 'de' => 'Pistaziencreme'], 'category' => 'Nut Creams', 'description' => 'Antep fıstığının karakteristik aromasıyla rafine bir deneyim.', 'featured' => true, 'accent' => '#a6ad76', 'image' => asset('images/nuttime/pistachio-butter.jpg'), 'gallery' => []],
            ['slug' => 'badem-unu', 'name' => ['tr' => 'Badem Ezmesi', 'en' => 'Almond Butter', 'de' => 'Mandelcreme'], 'category' => 'Nut Creams', 'description' => 'Özenle seçilmiş bademlerin yumuşak ve karakterli lezzeti.', 'featured' => true, 'accent' => '#c8a077', 'image' => asset('images/nuttime/almond-butter.jpg'), 'gallery' => []],
            ['slug' => 'yer-fistigi-ezmesi', 'name' => ['tr' => 'Yer Fıstığı Ezmesi', 'en' => 'Peanut Butter', 'de' => 'Erdnusscreme'], 'category' => 'Nut Creams', 'description' => 'Yoğun fıstık tadı ve parçacıklı dokusuyla günün her anına eşlik eder.', 'featured' => true, 'accent' => '#a97845', 'image' => asset('images/nuttime/peanut-butter.jpg'), 'gallery' => []],
            ['slug' => 'seker-ilavesiz-yer-fistigi-ezmesi', 'name' => ['tr' => 'Şeker İlavesiz Yer Fıstığı Ezmesi', 'en' => 'No Added Sugar Peanut Butter', 'de' => 'Erdnusscreme ohne Zuckerzusatz'], 'category' => 'Nut Creams', 'description' => 'Şeker ilavesiz formülüyle sade, güçlü ve parçacıklı lezzet.', 'featured' => true, 'accent' => '#c5a65a', 'image' => asset('images/nuttime/peanut-butter.jpg'), 'gallery' => []],
            ['slug' => 'hindistan-cevizi-ezmesi', 'name' => ['tr' => 'Hindistan Cevizi Ezmesi', 'en' => 'Coconut Butter', 'de' => 'Kokoscreme'], 'category' => 'Nut Creams', 'description' => 'Hafif, aromatik ve tropikal bir lezzet.', 'featured' => true, 'accent' => '#d9f2f4', 'image' => asset('images/nuttime/coconut-butter.jpg'), 'gallery' => []],
        ];
    }

    private function categories(): array
    {
        if (! Schema::hasTable('categories')) {
            return $this->fallbackCategories();
        }

        $categories = Category::query()->where('is_active', true)->orderBy('sort_order')->get()->map(fn (Category $category) => [
            'name' => $category->name ?? 'Nut Creams', 'slug' => $category->slug ?? 'nut-creams', 'description' => $category->description ?? '',
            'seo_title' => $category->seo_title, 'seo_description' => $category->seo_description, 'seo_canonical' => $category->seo_canonical,
            'image' => $category->image ? asset('storage/'.$category->image) : $this->fallbackProductImage($category->name, $category->name),
            'image_alt' => $category->image_alt ?: trim(($category->name ?? 'Nuttime').' kategorisi'),
        ])->all();

        return $categories ?: $this->fallbackCategories();
    }

    private function fallbackCategories(): array
    {
        return [
            ['name' => 'Nut Creams', 'slug' => 'nut-creams', 'description' => 'Fındık, Antep fıstığı ve bademin yoğun, gerçek lezzeti.', 'image' => asset('images/nuttime/hazelnut-butter.jpg')],
            ['name' => 'Yer Fıstığı', 'slug' => 'yer-fistigi', 'description' => 'Parçacıklı dokusuyla güçlü ve dengeli bir klasik.', 'image' => asset('images/nuttime/peanut-butter.jpg')],
            ['name' => 'Özel Seçki', 'slug' => 'ozel-secki', 'description' => 'Her güne küçük, iyi bir lezzet molası.', 'image' => asset('images/nuttime/pistachio-butter.jpg')],
        ];
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

    private function settings(): array
    {
        return Schema::hasTable('site_settings') ? SiteSetting::current()->toArray() : ['site_name' => 'Nuttime', 'email' => 'hello@nuttime.com.tr', 'phone' => '+90 212 123 45 67', 'whatsapp' => '', 'instagram' => '#', 'facebook' => '#', 'youtube' => '#'];
    }

    private function factoryLocation(): array
    {
        $settings = $this->settings();
        $enabled = (bool) ($settings['factory_map_enabled'] ?? false);
        $url = filter_var($settings['factory_google_maps_url'] ?? null, FILTER_VALIDATE_URL);
        $key = config('services.google_maps.embed_api_key');
        $hasCoordinates = is_numeric($settings['factory_map_latitude'] ?? null) && is_numeric($settings['factory_map_longitude'] ?? null);

        return ['enabled' => $enabled && filled($settings['factory_address'] ?? null), 'name' => $settings['factory_name'] ?? '', 'address' => $settings['factory_address'] ?? '', 'url' => $url && str_starts_with($url, 'https://') ? $url : null, 'embed_url' => $enabled && $hasCoordinates && $key ? 'https://www.google.com/maps/embed/v1/place?key='.rawurlencode($key).'&q='.rawurlencode($settings['factory_map_latitude'].','.$settings['factory_map_longitude']) : null];
    }

    private function certificates(): array
    {
        if (! Schema::hasTable('certificates')) {
            return [];
        }

        return Certificate::query()->active()->orderBy('sort_order')->get()->map(fn (Certificate $certificate) => [
            'name' => $certificate->name, 'description' => $certificate->description, 'image' => $certificate->image ? asset('storage/'.$certificate->image) : null,
            'document' => $certificate->document_file ? asset('storage/'.$certificate->document_file) : $certificate->document_url,
        ])->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $products
     * @return array<int, array<string, mixed>>
     */
    private function heroSlides(array $products): array
    {
        $assets = [
            'yer-fistigi-ezmesi' => ['key' => 'yer-fistigi', 'eyebrow' => 'NUTTIME • %52 YER FISTIĞI', 'title' => 'YER FISTIĞININ EN YOĞUN HALİ'],
            'findik-kremasi' => ['key' => 'findik', 'eyebrow' => 'NUTTIME • %45 FINDIK', 'title' => 'FINDIĞIN KAVRULMUŞ ZENGİNLİĞİ'],
            'antep-fistikli-kremasi' => ['key' => 'antep', 'eyebrow' => 'NUTTIME • %42 ANTEP FISTIĞI', 'title' => 'ANTEP FISTIĞININ EN YOĞUN HALİ'],
            'badem-unu' => ['key' => 'badem', 'eyebrow' => 'NUTTIME • %45 BADEM', 'title' => 'BADEMİN ZARİF VE YOĞUN DOKUSU'],
            'seker-ilavesiz-yer-fistigi-ezmesi' => ['key' => 'seker-ilavesiz-yer-fistigi', 'eyebrow' => 'NUTTIME • ŞEKER İLAVESİZ • %72', 'title' => 'DAHA SADE, DAHA YOĞUN FISTIK'],
            'hindistan-cevizi-ezmesi' => ['key' => 'hindistan-cevizi', 'eyebrow' => 'NUTTIME • %42 HİNDİSTAN CEVİZİ', 'title' => 'FERAH VE KREMAMSI BİR DOKU'],
        ];

        return collect($assets)->map(function (array $asset, string $slug) use ($products): ?array {
            $product = collect($products)->firstWhere('slug', $slug);

            if (! $product) {
                return null;
            }

            $path = 'images/nuttime/spylt/nuttime-'.$asset['key'];

            return [
                'slug' => $slug, 'name' => $asset['title'], 'category' => $asset['eyebrow'], 'description' => $product['description'],
                'url' => route(app()->getLocale() === 'tr' ? 'product' : 'localized.product', app()->getLocale() === 'tr' ? ['slug' => $slug] : ['locale' => app()->getLocale(), 'slug' => $slug]),
                'background_image' => asset($path.'-hero-background.png'), 'ingredient_image' => asset($path.'-ingredient-elements-transparent.png'), 'product_image' => asset($path.'-jar-transparent.png'),
            ];
        })->filter()->concat(collect($products)->reject(fn (array $product): bool => array_key_exists($product['slug'], $assets))->map(function (array $product): array {
            return [
                'slug' => $product['slug'], 'name' => $product['name']['tr'], 'category' => $product['category'], 'description' => $product['description'],
                'url' => route('product', ['slug' => $product['slug']]), 'background_image' => $product['image'], 'ingredient_image' => $product['image'], 'product_image' => $product['image'],
            ];
        }))->values()->all();
    }

    private function heroTheme(string $label): string
    {
        if (Str::contains($label, ['antep', 'pistachio'])) {
            return 'pistachio';
        }

        if (Str::contains($label, ['fındık', 'findik', 'hazelnut'])) {
            return 'hazelnut';
        }

        if (Str::contains($label, ['badem', 'almond'])) {
            return 'almond';
        }

        if (Str::contains($label, ['çikolata', 'cikolata', 'chocolate', 'kakao', 'cocoa'])) {
            return 'cocoa';
        }

        if (Str::contains($label, ['bal', 'honey'])) {
            return 'honey';
        }

        if (Str::contains($label, ['fıstık', 'fistik', 'peanut'])) {
            return 'peanut';
        }

        $themes = ['peanut', 'almond', 'cocoa', 'honey'];

        return $themes[(int) sprintf('%u', crc32($label)) % count($themes)];
    }

    public function home()
    {
        $products = $this->catalog();
        $settings = $this->settings();
        $schemas = array_filter([$this->seoMetadata->organization($settings), $this->seoMetadata->website(), $this->seoMetadata->localBusiness($settings)]);

        return view('pages.home', ['products' => $products, 'heroSlides' => $this->heroSlides($products), 'categories' => $this->categories(), 'certificates' => $this->certificates(), 'factory' => $this->factoryLocation(), 'settings' => $settings, 'seo' => $this->seoMetadata->page($settings['seo_title'] ?? 'Nuttime', $settings['seo_description'] ?? 'Kuruyemiş üreticisi Nuttime ile yalın ve yoğun lezzetleri keşfedin.', $this->siteRoute('home'), $settings, $schemas)]);
    }

    public function products()
    {
        $settings = $this->settings();

        return view('products.index', ['products' => $this->catalog(), 'settings' => $settings, 'seo' => $this->seoMetadata->page('Nuttime Ürünleri', 'Nuttime kuruyemiş ezmeleri ve özenle hazırlanan ürün seçkisini keşfedin.', $this->siteRoute('products'), $settings)]);
    }

    public function product(string $slug)
    {
        $product = collect($this->catalog())->firstWhere('slug', $slug);

        if (! $product) {
            $product = collect($this->catalog())->first(fn (array $catalogProduct): bool => in_array($slug, $catalogProduct['previous_slugs'] ?? [], true));

            if ($product) {
                return redirect()->to($this->siteRoute('product', ['slug' => $product['slug']]), 301);
            }
        }

        abort_unless($product, 404);

        $settings = $this->settings();
        $productUrl = $this->canonical($product['seo_canonical'] ?? null, $this->siteRoute('product', ['slug' => $product['slug']]));
        $breadcrumbs = $this->seoMetadata->breadcrumbs([
            ['name' => 'Ana Sayfa', 'url' => $this->siteRoute('home')],
            ['name' => 'Ürünler', 'url' => $this->siteRoute('products')],
            ['name' => $product['name']['tr'], 'url' => $productUrl],
        ]);

        return view('products.show', ['product' => $product, 'related' => collect($this->catalog())->reject(fn ($item) => $item['slug'] === $product['slug'])->take(2)->all(), 'settings' => $settings, 'breadcrumbs' => $breadcrumbs['itemListElement'], 'seo' => $this->seoMetadata->page($product['seo_title'] ?: $product['name']['tr'], $product['seo_description'] ?: $product['description'], $productUrl, $settings, [$breadcrumbs, $this->seoMetadata->product($product, $settings, $productUrl)], 'product', $product['image'])]);
    }

    public function category(string $slug)
    {
        $category = collect($this->categories())->firstWhere('slug', $slug);
        abort_unless($category, 404);
        $products = collect($this->catalog())->filter(fn ($product) => $product['category'] === $category['name'])->values()->all();
        $settings = $this->settings();
        $categoryUrl = $this->canonical($category['seo_canonical'] ?? null, $this->siteRoute('category', ['slug' => $category['slug']]));
        $breadcrumbs = $this->seoMetadata->breadcrumbs([
            ['name' => 'Ana Sayfa', 'url' => $this->siteRoute('home')],
            ['name' => 'Kategoriler', 'url' => $this->siteRoute('products')],
            ['name' => $category['name'], 'url' => $categoryUrl],
        ]);

        return view('pages.category', compact('category', 'products') + ['breadcrumbs' => $breadcrumbs['itemListElement'], 'seo' => $this->seoMetadata->page($category['seo_title'] ?: $category['name'], $category['seo_description'] ?: $category['description'], $categoryUrl, $settings, [$breadcrumbs], 'website', $category['image'])]);
    }

    public function page(string $page)
    {
        $settings = $this->settings();
        $details = $page === 'certificates'
            ? ['title' => 'Nuttime Sertifikaları', 'description' => 'Nuttime kalite belgeleri ve üretim standartlarını inceleyin.', 'route' => 'certificates']
            : ['title' => 'Nuttime Hakkında', 'description' => 'Kuruyemiş üreticisi Nuttime’ın üretim yaklaşımını, kalite odağını ve özel markalı üretim kabiliyetini keşfedin.', 'route' => 'about'];

        return view('pages.static', ['page' => $page, 'certificates' => $page === 'certificates' ? $this->certificates() : [], 'seo' => $this->seoMetadata->page($details['title'], $details['description'], $this->siteRoute($details['route']), $settings)]);
    }

    public function contact()
    {
        $settings = $this->settings();

        return view('pages.contact', ['factory' => $this->factoryLocation(), 'settings' => $settings, 'seo' => $this->seoMetadata->page('Nuttime İletişim', 'Toptan kuruyemiş, özel markalı üretim ve iş birliği talepleriniz için Nuttime ile iletişime geçin.', $this->siteRoute('contact'), $settings, array_filter([$this->seoMetadata->localBusiness($settings)]))]);
    }

    public function storeContact(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:160'], 'message' => ['required', 'string', 'max:4000'], 'website' => ['nullable', 'size:0']]);
        ContactMessage::create($request->only(['name', 'email', 'phone', 'subject', 'message']) + ['locale' => app()->getLocale()]);

        return back()->with('success', 'Mesajınız için teşekkür ederiz. Ekibimiz en kısa sürede size dönüş yapacaktır.');
    }

    private function siteRoute(string $name, array $parameters = []): string
    {
        return route(app()->getLocale() === 'tr' ? $name : 'localized.'.$name, app()->getLocale() === 'tr' ? $parameters : ['locale' => app()->getLocale()] + $parameters);
    }

    private function canonical(?string $preferredUrl, string $fallback): string
    {
        return filter_var($preferredUrl, FILTER_VALIDATE_URL) ? $preferredUrl : $fallback;
    }
}
