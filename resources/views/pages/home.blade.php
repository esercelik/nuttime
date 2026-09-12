@extends('layouts.app')

@section('content')
<x-product-hero-slider :slides="$heroSlides" :settings="$heroSliderSettings" />
<div id="home-content"></div>

@if(collect($homeSections ?? [])->isNotEmpty())
    @php($needsBanners = !collect($homeSections)->contains('type', 'banner'))
    @if($needsBanners && !collect($homeSections)->contains('type', 'intro'))
        <x-home-banners :products="$products" />
    @endif
    @foreach($homeSections as $section)
        <x-home-managed-section :section="$section" :products="$products" :categories="$categories" :factory="$factory" :settings="$settings" />
        @if($needsBanners && $section['type'] === 'intro')
            <x-home-banners :products="$products" />
            @php($needsBanners = false)
        @endif
    @endforeach
@else
<section class="home-intro"><div class="container home-intro__inner"><p class="kicker">NUTTIME</p><p><x-safe-rich-text :value="__('site.home.intro')" /></p><a class="arrow-link" href="{{ app(\App\Support\LocalizedUrl::class)->route('about') }}">{{ __('site.actions.discover_story') }} <span>↗</span></a></div></section>

<x-home-banners :products="$products" />

@php($featured = collect($products)->filter(fn (array $product): bool => $product['featured'] ?? false)->take(3))
@if($featured->isNotEmpty())
<section class="feature-showcase home-section"><div class="container"><x-section-heading :kicker="__('site.home.featured_kicker')" :title="__('site.home.featured_title')" :href="app(\App\Support\LocalizedUrl::class)->route('products')" :link-text="__('site.actions.all_products')" rich-title /><div class="feature-showcase__grid">@foreach($featured as $key => $product)<x-product-card :product="$product" :index="$key" :variant="$key === 0 ? 'hero' : 'mini'" />@endforeach</div></div></section>
@endif

@if(count($categories))
<section class="category-showcase home-section"><div class="container"><x-section-heading :kicker="__('site.home.categories_kicker')" :title="__('site.home.categories_title')" rich-title /><div class="category-showcase__grid">@foreach($categories as $index => $category)<x-category-card :category="$category" :index="$index" />@endforeach</div></div></section>
@endif

<section class="brand-moment" aria-label="{{ __('site.home.banners_label') }}"><x-optimized-image src="images/nuttime/collection-banner.jpg" alt="Nuttime" width="1900" height="500" /><div class="brand-moment__shade"></div><div class="container brand-moment__copy"><p class="kicker">{{ __('site.home.moment_kicker') }}</p><p><x-safe-rich-text :value="__('site.home.moment_title')" /></p></div></section>

<section class="brand-story"><div class="brand-story__image"><x-optimized-image src="images/nuttime/brand-story.jpg" alt="Nuttime" width="1707" height="2560" sizes="(max-width: 900px) 100vw, 57vw" /></div><div class="brand-story__copy"><p class="kicker">{{ __('site.home.story_kicker') }}</p><h2><x-safe-rich-text :value="__('site.home.story_title')" /></h2><p>{{ __('site.home.story_copy') }}</p><a class="arrow-link arrow-link--light" href="{{ app(\App\Support\LocalizedUrl::class)->route('about') }}">{{ __('site.actions.discover_story') }} <span>↗</span></a></div></section>

<x-factory-location :factory="$factory" />

@if(!empty($settings['instagram']) && $settings['instagram'] !== '#')
<section class="social-callout"><div class="container"><p class="kicker">{{ __('site.home.social_kicker') }}</p><h2><x-safe-rich-text :value="__('site.home.social_title')" /></h2><a class="arrow-link arrow-link--light" href="{{ $settings['instagram'] }}" target="_blank" rel="noopener">{{ __('site.home.instagram') }} <span>↗</span></a></div></section>
@endif

<x-final-cta />
@endif
@endsection
