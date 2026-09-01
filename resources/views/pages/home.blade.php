@extends('layouts.app')

@section('content')
<x-product-hero-slider :slides="$heroSlides" />

@php($bannerProduct = collect($products)->firstWhere('id', 'antep-fistikli-kremasi') ?? collect($products)->first())
@php($managedSections = collect($homeSections ?? [])->keyBy('key'))
@php($firstBanner = $managedSections->get('banner_one'))
@php($secondBanner = $managedSections->get('banner_two'))
<section id="home-banners" class="hero-banners" aria-label="{{ __('site.home.banners_label') }}">
    <a class="hero-banner hero-banner--pistachio" href="{{ $firstBanner['button_url'] ?? ($bannerProduct ? app(\App\Support\LocalizedUrl::class)->route('product', null, ['slug' => $bannerProduct['slug']]) : app(\App\Support\LocalizedUrl::class)->route('products')) }}">
        @if($firstBanner['desktop_image'] ?? false)<img src="{{ $firstBanner['desktop_image'] }}" alt="{{ $firstBanner['title'] ?? __('site.home.banner_one_title') }}" width="1900" height="500" loading="lazy" decoding="async">@else<x-optimized-image src="images/nuttime/collection-banner.jpg" alt="{{ __('site.home.banner_one_title') }}" width="1900" height="500" sizes="(max-width: 900px) 100vw, 58vw" />@endif
        <span class="hero-banner__veil"></span>
        <span class="hero-banner__copy"><small>{{ $firstBanner['eyebrow'] ?? __('site.home.banner_one_kicker') }}</small><strong>@if($firstBanner['title'] ?? false){{ $firstBanner['title'] }}@else{!! __('site.home.banner_one_title') !!}@endif</strong><em>{{ $firstBanner['button_label'] ?? __('site.actions.learn_more') }} ↗</em></span>
    </a>
    <a class="hero-banner hero-banner--spread" href="{{ $secondBanner['button_url'] ?? app(\App\Support\LocalizedUrl::class)->route('products') }}">
        @if($secondBanner['desktop_image'] ?? false)<img src="{{ $secondBanner['desktop_image'] }}" alt="{{ $secondBanner['title'] ?? __('site.home.banner_two_title') }}" width="1900" height="500" loading="lazy" decoding="async">@else<x-optimized-image src="images/nuttime/spread-banner.jpg" alt="{{ __('site.home.banner_two_title') }}" width="1900" height="500" sizes="(max-width: 900px) 100vw, 42vw" />@endif
        <span class="hero-banner__veil"></span>
        <span class="hero-banner__copy"><small>{{ $secondBanner['eyebrow'] ?? __('site.home.banner_two_kicker') }}</small><strong>@if($secondBanner['title'] ?? false){{ $secondBanner['title'] }}@else{!! __('site.home.banner_two_title') !!}@endif</strong><em>{{ $secondBanner['button_label'] ?? __('site.actions.all_products') }} ↗</em></span>
    </a>
</section>

<section class="home-intro"><div class="container home-intro__inner"><p class="kicker">NUTTIME</p><p>{!! __('site.home.intro') !!}</p><a class="arrow-link" href="{{ app(\App\Support\LocalizedUrl::class)->route('about') }}">{{ __('site.actions.discover_story') }} <span>↗</span></a></div></section>

@php($featured = collect($products)->filter(fn (array $product): bool => $product['featured'] ?? false)->take(3))
@if($featured->isNotEmpty())
<section class="feature-showcase home-section"><div class="container"><x-section-heading :kicker="__('site.home.featured_kicker')" :title="__('site.home.featured_title')" :href="app(\App\Support\LocalizedUrl::class)->route('products')" :link-text="__('site.actions.all_products')" /><div class="feature-showcase__grid">@foreach($featured as $key => $product)<x-product-card :product="$product" :variant="$key === 0 ? 'hero' : 'mini'" />@endforeach</div></div></section>
@endif

@if(count($categories))
<section class="category-showcase home-section"><div class="container"><x-section-heading :kicker="__('site.home.categories_kicker')" :title="__('site.home.categories_title')" /><div class="category-showcase__grid">@foreach($categories as $index => $category)<x-category-card :category="$category" :index="$index" />@endforeach</div></div></section>
@endif

<section class="brand-moment" aria-label="{{ __('site.home.banners_label') }}"><x-optimized-image src="images/nuttime/collection-banner.jpg" alt="Nuttime" width="1900" height="500" /><div class="brand-moment__shade"></div><div class="container brand-moment__copy"><p class="kicker">{{ __('site.home.moment_kicker') }}</p><p>{!! __('site.home.moment_title') !!}</p></div></section>

<section class="brand-story"><div class="brand-story__image"><x-optimized-image src="images/nuttime/brand-story.jpg" alt="Nuttime" width="1707" height="2560" sizes="(max-width: 900px) 100vw, 57vw" /></div><div class="brand-story__copy"><p class="kicker">{{ __('site.home.story_kicker') }}</p><h2>{!! __('site.home.story_title') !!}</h2><p>{{ __('site.home.story_copy') }}</p><a class="arrow-link arrow-link--light" href="{{ app(\App\Support\LocalizedUrl::class)->route('about') }}">{{ __('site.actions.discover_story') }} <span>↗</span></a></div></section>

@php($certificatesWithImages = collect($certificates)->filter(fn (array $certificate): bool => !empty($certificate['image'])))
@if($certificatesWithImages->isNotEmpty())
<section class="quality-rail home-section"><div class="container"><x-section-heading :kicker="__('site.home.quality_kicker')" :title="__('site.home.quality_title')" :href="app(\App\Support\LocalizedUrl::class)->route('certificates')" :link-text="__('site.nav.certificates')" /><div class="quality-rail__items">@foreach($certificatesWithImages as $certificate)<article><img src="{{ $certificate['image'] }}" alt="{{ $certificate['name'] }}" width="260" height="160" loading="lazy" decoding="async"><div><h3>{{ $certificate['name'] }}</h3>@if($certificate['description'])<p>{{ $certificate['description'] }}</p>@endif</div>@if($certificate['document'])<a href="{{ $certificate['document'] }}" target="_blank" rel="noopener">{{ __('site.actions.open_document') }} ↗</a>@endif</article>@endforeach</div></div></section>
@endif

<x-factory-location :factory="$factory" />

@if(!empty($settings['instagram']) && $settings['instagram'] !== '#')
<section class="social-callout"><div class="container"><p class="kicker">{{ __('site.home.social_kicker') }}</p><h2>{!! __('site.home.social_title') !!}</h2><a class="arrow-link arrow-link--light" href="{{ $settings['instagram'] }}" target="_blank" rel="noopener">{{ __('site.home.instagram') }} <span>↗</span></a></div></section>
@endif

<x-final-cta />
@endsection
