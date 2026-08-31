<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SiteController extends Controller
{
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
                'featured' => $product->is_featured,
                'accent' => '#d7b66c',
                'image' => $product->main_image
                    ? asset('storage/'.$product->main_image)
                    : $this->fallbackProductImage($product->name, $product->category?->name),
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
            ['slug' => 'yer-fistigi-ezmesi', 'name' => ['tr' => 'Yer Fıstığı Ezmesi', 'en' => 'Peanut Butter', 'de' => 'Erdnusscreme'], 'category' => 'Nut Creams', 'description' => 'Yoğun fıstık tadı ve parçacıklı dokusuyla günün her anına eşlik eder.', 'featured' => false, 'accent' => '#a97845', 'image' => asset('images/nuttime/peanut-butter.jpg'), 'gallery' => []],
        ];
    }

    private function categories(): array
    {
        if (! Schema::hasTable('categories')) {
            return $this->fallbackCategories();
        }

        $categories = Category::query()->where('is_active', true)->orderBy('sort_order')->get()->map(fn (Category $category) => [
            'name' => $category->name ?? 'Nut Creams', 'slug' => $category->slug ?? 'nut-creams', 'description' => $category->description ?? '',
            'image' => $category->image ? asset('storage/'.$category->image) : $this->fallbackProductImage($category->name, $category->name),
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
        [$featuredProducts, $otherProducts] = collect($products)
            ->filter(fn (array $product) => filled($product['slug'] ?? null) && filled($product['image'] ?? null))
            ->partition(fn (array $product) => (bool) ($product['featured'] ?? false));

        return $featuredProducts
            ->merge($otherProducts)
            ->unique('slug')
            ->values()
            ->map(function (array $product): array {
                $name = $product['name']['tr'] ?? 'Nuttime ürünü';
                $theme = $this->heroTheme(Str::lower(implode(' ', [$product['slug'] ?? '', $name, $product['category'] ?? ''])));
                $isPistachio = $theme === 'pistachio';

                return [
                    'slug' => $product['slug'],
                    'name' => $name,
                    'category' => $product['category'] ?? 'Nuttime',
                    'description' => filled($product['description'] ?? null)
                        ? $product['description']
                        : 'Özenle seçilmiş kuruyemişlerle hazırlanan yoğun ve karakterli lezzet.',
                    'url' => route('product', ['slug' => $product['slug']]),
                    'theme' => $theme,
                    'background_image' => $isPistachio
                        ? asset('images/nuttime/spylt/nuttime-antep-hero-background.png')
                        : $product['image'],
                    'product_image' => $isPistachio
                        ? asset('images/nuttime/spylt/nuttime-antep-jar-transparent.png')
                        : $product['image'],
                    'product_is_photo' => ! $isPistachio,
                ];
            })
            ->all();
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

        return view('pages.home', ['products' => $products, 'heroSlides' => $this->heroSlides($products), 'categories' => $this->categories(), 'certificates' => $this->certificates(), 'factory' => $this->factoryLocation(), 'settings' => $this->settings()]);
    }

    public function products()
    {
        return view('products.index', ['products' => $this->catalog(), 'settings' => $this->settings()]);
    }

    public function product(string $slug)
    {
        $product = collect($this->catalog())->firstWhere('slug', $slug);
        abort_unless($product, 404);

        return view('products.show', ['product' => $product, 'related' => collect($this->catalog())->reject(fn ($item) => $item['slug'] === $slug)->take(2)->all(), 'settings' => $this->settings()]);
    }

    public function category(string $slug)
    {
        $category = collect($this->categories())->firstWhere('slug', $slug);
        abort_unless($category, 404);
        $products = collect($this->catalog())->filter(fn ($product) => $product['category'] === $category['name'])->values()->all();

        return view('pages.category', compact('category', 'products'));
    }

    public function page(string $page)
    {
        return view('pages.static', ['page' => $page, 'certificates' => $page === 'certificates' ? $this->certificates() : []]);
    }

    public function contact()
    {
        return view('pages.contact', ['factory' => $this->factoryLocation(), 'settings' => $this->settings()]);
    }

    public function storeContact(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:160'], 'message' => ['required', 'string', 'max:4000'], 'website' => ['nullable', 'size:0']]);
        ContactMessage::create($request->only(['name', 'email', 'phone', 'subject', 'message']) + ['locale' => app()->getLocale()]);

        return back()->with('success', 'Mesajınız için teşekkür ederiz. Ekibimiz en kısa sürede size dönüş yapacaktır.');
    }
}
