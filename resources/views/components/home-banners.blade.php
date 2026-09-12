@props(['products' => [], 'firstBanner' => null, 'secondBanner' => null])
@php($bannerProduct = collect($products)->firstWhere('source_slug', 'antep-fistikli-kremasi') ?? collect($products)->first())

<section id="home-banners" class="hero-banners" aria-label="{{ __('site.home.banners_label') }}">
    <a class="hero-banner hero-banner--pistachio" href="{{ $firstBanner['button_url'] ?? ($bannerProduct ? app(\App\Support\LocalizedUrl::class)->route('product', null, ['slug' => $bannerProduct['slug']]) : app(\App\Support\LocalizedUrl::class)->route('products')) }}">
        @if($firstBanner['desktop_image'] ?? false)<img src="{{ $firstBanner['desktop_image'] }}" alt="{{ $firstBanner['title'] ?? __('site.home.banner_one_title') }}" width="1900" height="500" loading="lazy" decoding="async">@else<x-optimized-image src="images/nuttime/collection-banner.jpg" alt="{{ __('site.home.banner_one_title') }}" width="1900" height="500" sizes="(max-width: 900px) 100vw, 58vw" />@endif
        <span class="hero-banner__veil"></span>
        <span class="hero-banner__copy"><small><x-safe-rich-text :value="$firstBanner['eyebrow'] ?? __('site.home.banner_one_kicker')" /></small><strong><x-safe-rich-text :value="$firstBanner['title'] ?? __('site.home.banner_one_title')" /></strong><em><x-safe-rich-text :value="$firstBanner['button_label'] ?? __('site.actions.learn_more')" /> ↗</em></span>
    </a>
    <a class="hero-banner hero-banner--spread" href="{{ $secondBanner['button_url'] ?? app(\App\Support\LocalizedUrl::class)->route('products') }}">
        @if($secondBanner['desktop_image'] ?? false)<img src="{{ $secondBanner['desktop_image'] }}" alt="{{ $secondBanner['title'] ?? __('site.home.banner_two_title') }}" width="1900" height="500" loading="lazy" decoding="async">@else<x-optimized-image src="images/nuttime/spread-banner.jpg" alt="{{ __('site.home.banner_two_title') }}" width="1900" height="500" sizes="(max-width: 900px) 100vw, 42vw" />@endif
        <span class="hero-banner__veil"></span>
        <span class="hero-banner__copy"><small><x-safe-rich-text :value="$secondBanner['eyebrow'] ?? __('site.home.banner_two_kicker')" /></small><strong><x-safe-rich-text :value="$secondBanner['title'] ?? __('site.home.banner_two_title')" /></strong><em><x-safe-rich-text :value="$secondBanner['button_label'] ?? __('site.actions.all_products')" /> ↗</em></span>
    </a>
</section>
