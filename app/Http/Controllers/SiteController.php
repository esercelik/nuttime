<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

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
                'image' => $product->main_image ? asset('storage/'.$product->main_image) : 'https://images.unsplash.com/photo-1599599810694-b5ac8dd71e6d?auto=format&fit=crop&w=1200&q=85',
            ])->all();
        }

        return [
            ['slug' => 'findik-kremasi', 'name' => ['tr' => 'Fındık Kreması', 'en' => 'Hazelnut Cream', 'de' => 'Haselnusscreme'], 'category' => 'Nut Creams', 'description' => 'Özenle seçilmiş fındıklarla hazırlanan pürüzsüz, yoğun ve dengeli lezzet.', 'accent' => '#d7b66c', 'image' => 'https://images.unsplash.com/photo-1599599810694-b5ac8dd71e6d?auto=format&fit=crop&w=1200&q=85'],
            ['slug' => 'antep-fistikli-kremasi', 'name' => ['tr' => 'Antep Fıstıklı Krema', 'en' => 'Pistachio Cream', 'de' => 'Pistaziencreme'], 'category' => 'Nut Creams', 'description' => 'Antep fıstığının karakteristik aromasıyla rafine bir deneyim.', 'accent' => '#aac09e', 'image' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?auto=format&fit=crop&w=1200&q=85'],
            ['slug' => 'badem-unu', 'name' => ['tr' => 'Badem Unu', 'en' => 'Almond Flour', 'de' => 'Mandelmehl'], 'category' => 'Ingredients', 'description' => 'Mutfakta yaratıcı tarifler için ince öğütülmüş, doğal badem.', 'accent' => '#c9a87f', 'image' => 'https://images.unsplash.com/photo-1508747703725-719777637510?auto=format&fit=crop&w=1200&q=85'],
        ];
    }

    private function categories(): array
    {
        if (! Schema::hasTable('categories')) {
            return [['name' => 'Nut Creams', 'slug' => 'nut-creams', 'description' => 'Natural, creamy and full of character.', 'image' => 'https://images.unsplash.com/photo-1599599810694-b5ac8dd71e6d?auto=format&fit=crop&w=900&q=80']];
        }

        $categories = Category::query()->where('is_active', true)->orderBy('sort_order')->get()->map(fn (Category $category) => [
            'name' => $category->name ?? 'Nut Creams', 'slug' => $category->slug ?? 'nut-creams', 'description' => $category->description ?? '',
            'image' => $category->image ? asset('storage/'.$category->image) : 'https://images.unsplash.com/photo-1508747703725-719777637510?auto=format&fit=crop&w=900&q=80',
        ])->all();

        return $categories ?: [['name' => 'Nut Creams', 'slug' => 'nut-creams', 'description' => 'Natural, creamy and full of character.', 'image' => 'https://images.unsplash.com/photo-1599599810694-b5ac8dd71e6d?auto=format&fit=crop&w=900&q=80']];
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

    public function home()
    {
        return view('pages.home', ['products' => $this->catalog(), 'categories' => $this->categories(), 'certificates' => $this->certificates(), 'factory' => $this->factoryLocation(), 'settings' => $this->settings()]);
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
