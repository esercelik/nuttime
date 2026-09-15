@extends('layouts.app')

@section('content')
@php
    $featuredProducts = collect($products)->filter(fn (array $product): bool => $product['featured'] ?? false)->take(4);
    $featuredProducts = $featuredProducts->isNotEmpty() ? $featuredProducts : collect($products)->take(4);
    $storyProduct = collect($products)->firstWhere('source_slug', 'hindistan-cevizi-ezmesi') ?? collect($products)->first();
    $storyJar = $storyProduct ? app(\App\Support\NuttimeProductMedia::class)->cardImagePath($storyProduct['source_slug'] ?? '') : null;
@endphp

<x-product-hero-slider :slides="$heroSlides" :settings="$heroSliderSettings" />

<div id="home-content" class="v2-home">
    <div class="v2-flavour-marquee" aria-hidden="true">
        <div><span>NUTTIME</span><i>●</i><span>{{ strip_tags(__('site.home.featured_title')) }}</span><i>●</i><span>NUTTIME</span><i>●</i><span>{{ strip_tags(__('site.home.featured_title')) }}</span><i>●</i></div>
    </div>

    <section class="v2-manifesto">
        <div class="container v2-manifesto__inner">
            <p class="kicker">NUTTIME / 2026</p>
            <h2><x-safe-rich-text :value="__('site.home.intro')" /></h2>
            <a class="v2-round-link" href="{{ app(\App\Support\LocalizedUrl::class)->route('about') }}" aria-label="{{ __('site.actions.discover_story') }}">↗</a>
        </div>
    </section>

    @if($storyProduct && $storyJar)
        <section class="v2-scroll-story" data-v2-scroll-story aria-label="{{ $storyProduct['name'] }}">
            <div class="v2-scroll-story__sticky">
                <div class="v2-scroll-story__orb v2-scroll-story__orb--one" data-v2-orb></div>
                <div class="v2-scroll-story__orb v2-scroll-story__orb--two" data-v2-orb></div>
                <div class="v2-scroll-story__type" aria-hidden="true">NUTTIME</div>
                <div class="v2-scroll-story__copy v2-scroll-story__copy--start" data-v2-story-copy>
                    <p class="kicker"><x-safe-rich-text :value="$storyProduct['category'] ?? 'Nuttime'" /></p>
                    <h2><x-safe-rich-text :value="$storyProduct['name']" /></h2>
                    <p><x-safe-rich-text :value="$storyProduct['description']" /></p>
                </div>
                <div class="v2-scroll-story__product" data-v2-story-product>
                    <x-optimized-image :src="$storyJar" :alt="$storyProduct['name']" width="1312" height="1199" sizes="(max-width: 900px) 78vw, 48vw" loading="eager" fetchpriority="high" />
                </div>
                <div class="v2-scroll-story__spoon" data-v2-story-spoon>
                    <x-optimized-image src="images/nuttime/v2/coconut-wooden-spoon.png" :alt="$storyProduct['name']" width="1707" height="2560" sizes="(max-width: 900px) 88vw, 34vw" />
                </div>
                <div class="v2-scroll-story__copy v2-scroll-story__copy--end" data-v2-story-end>
                    <p class="kicker">{{ __('site.home.moment_kicker') }}</p>
                    <h2><x-safe-rich-text :value="__('site.home.moment_title')" /></h2>
                    <a class="button button--ink" href="{{ app(\App\Support\LocalizedUrl::class)->route('product', null, ['slug' => $storyProduct['slug']]) }}">{{ __('site.actions.view_product') }} <span>↗</span></a>
                </div>
                <div class="v2-scroll-progress" aria-hidden="true"><span data-v2-scroll-progress></span></div>
            </div>
        </section>
    @endif

    @if($featuredProducts->isNotEmpty())
        <section class="v2-products home-section">
            <div class="container">
                <x-section-heading :kicker="__('site.home.featured_kicker')" :title="__('site.home.featured_title')" :href="app(\App\Support\LocalizedUrl::class)->route('products')" :link-text="__('site.actions.all_products')" rich-title />
                <div class="v2-products__rail">
                    @foreach($featuredProducts as $index => $product)
                        <x-product-card :product="$product" :index="$index" :priority="$index < 2" variant="v2" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="v2-photo-break">
        <x-optimized-image src="images/nuttime/v2/coconut-metal-spoon.png" alt="Nuttime" width="1707" height="2560" sizes="100vw" />
        <div class="v2-photo-break__shade"></div>
        <div class="container v2-photo-break__copy"><p class="kicker">{{ __('site.home.banner_two_kicker') }}</p><h2><x-safe-rich-text :value="__('site.home.banner_two_title')" /></h2></div>
    </section>

    <x-home-banners :products="$products" />

    @if(count($categories))
        <section class="category-showcase home-section"><div class="container"><x-section-heading :kicker="__('site.home.categories_kicker')" :title="__('site.home.categories_title')" rich-title /><div class="category-showcase__grid">@foreach($categories as $index => $category)<x-category-card :category="$category" :index="$index" />@endforeach</div></div></section>
    @endif

    @if(collect($homeSections ?? [])->isNotEmpty())
        <div class="v2-managed-sections">@foreach($homeSections as $section)<x-home-managed-section :section="$section" :products="$products" :categories="$categories" :factory="$factory" :settings="$settings" />@endforeach</div>
    @else
        <section class="brand-story"><div class="brand-story__image"><x-optimized-image src="images/nuttime/v2/coconut-front.png" alt="Nuttime" width="1707" height="2560" sizes="(max-width: 900px) 100vw, 57vw" /></div><div class="brand-story__copy"><p class="kicker">{{ __('site.home.story_kicker') }}</p><h2><x-safe-rich-text :value="__('site.home.story_title')" /></h2><p>{{ __('site.home.story_copy') }}</p><a class="arrow-link arrow-link--light" href="{{ app(\App\Support\LocalizedUrl::class)->route('about') }}">{{ __('site.actions.discover_story') }} <span>↗</span></a></div></section>
        <x-factory-location :factory="$factory" />
        <x-final-cta />
    @endif
</div>
@endsection
