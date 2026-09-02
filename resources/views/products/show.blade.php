@extends('layouts.app')

@section('content')
@php
    $galleryItems = collect([[
        'url' => $product['image'] ?? null,
        'path' => $product['image_path'] ?? null,
    ]])
        ->merge(collect($product['gallery'] ?? [])->values()->map(fn (string $image, int $index): array => [
            'url' => $image,
            'path' => data_get($product['gallery_paths'] ?? [], $index),
        ]))
        ->filter(fn (array $image): bool => filled($image['url']))
        ->unique('url')
        ->values();
@endphp
@php($featureTags = collect($product['feature_tags'] ?? [])->filter()->values())
@php($technicalDetails = collect([__('site.product.detail_label') => $product['weight_grams'] ? $product['weight_grams'].' g' : null, 'SKU' => $product['sku'] ?? null, '%' => $product['primary_ingredient_percentage'] ? rtrim(rtrim((string) $product['primary_ingredient_percentage'], '0'), '.').'%' : null])->filter())

<nav class="container breadcrumb" aria-label="{{ __('site.product.breadcrumbs') }}">@foreach($breadcrumbs as $item)<a href="{{ $item['item'] }}">{{ $item['name'] }}</a>@if(! $loop->last)<span aria-hidden="true">/</span>@endif @endforeach</nav>

<section class="product-stage">
    <div class="container product-stage__grid">
        <div
            class="product-stage__visual"
            x-data="{
                active: 0,
                count: {{ $galleryItems->count() }},
                touchStart: null,
                previous() { this.active = (this.active - 1 + this.count) % this.count },
                next() { this.active = (this.active + 1) % this.count },
                beginTouch(event) { this.touchStart = event.touches[0].clientX },
                endTouch(event) {
                    if (this.touchStart === null) return
                    const distance = event.changedTouches[0].clientX - this.touchStart
                    if (Math.abs(distance) > 40) distance < 0 ? this.next() : this.previous()
                    this.touchStart = null
                },
            }"
        >
            <div class="product-stage__surface" style="--product-tint:{{ $product['accent'] ?? '#d8b768' }}" role="region" aria-roledescription="carousel" aria-label="{{ $product['name'] }}" tabindex="0" x-on:keydown.arrow-left.prevent="previous()" x-on:keydown.arrow-right.prevent="next()" x-on:touchstart.passive="beginTouch($event)" x-on:touchend="endTouch($event)">
                @foreach($galleryItems as $index => $image)
                    <div class="product-stage__slide" x-show="active === {{ $index }}" x-transition.opacity.duration.250ms @if($index > 0) x-cloak @endif>
                        @if(filled($image['path']))
                            <x-optimized-image :src="$image['path']" :alt="$product['image_alt'] ?: $product['name']" width="1707" height="2560" sizes="(min-width: 900px) 55vw, 100vw" :loading="$index === 0 ? 'eager' : 'lazy'" :fetchpriority="$index === 0 ? 'high' : 'auto'" :class="'product-stage__image'" />
                        @else
                            <img src="{{ $image['url'] }}" alt="{{ $product['image_alt'] ?: $product['name'] }}" width="1707" height="2560" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}" decoding="async">
                        @endif
                    </div>
                @endforeach
                <span class="product-stage__monogram" aria-hidden="true">nt</span>
            </div>

            @if($galleryItems->count() > 1)
                <div class="product-stage__gallery-controls">
                    <button type="button" x-on:click="previous()" aria-label="{{ __('site.product.image', ['number' => '←']) }}">←</button>
                    <span aria-live="polite"><span x-text="String(active + 1).padStart(2, '0')"></span> / {{ str_pad((string) $galleryItems->count(), 2, '0', STR_PAD_LEFT) }}</span>
                    <button type="button" x-on:click="next()" aria-label="{{ __('site.product.image', ['number' => '→']) }}">→</button>
                </div>
                <div class="product-stage__thumbs" role="tablist" aria-label="{{ $product['name'] }}">
                    @foreach($galleryItems as $index => $image)
                        <button type="button" class="product-stage__thumbnail" x-on:click="active = {{ $index }}" :class="{ 'is-active': active === {{ $index }} }" :aria-selected="active === {{ $index }}" aria-label="{{ __('site.product.image', ['number' => $index + 1]) }}">
                            @if(filled($image['path']))
                                <x-optimized-image :src="$image['path']" alt="" width="1707" height="2560" sizes="80px" />
                            @else
                                <img src="{{ $image['url'] }}" alt="" width="1707" height="2560" loading="lazy" decoding="async">
                            @endif
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="product-stage__copy">
            <p class="kicker">{{ $product['category'] }}</p>
            <h1>{{ $product['name'] }}</h1>
            @if($featureTags->isNotEmpty())
                <ul class="product-stage__tags" aria-label="{{ $product['name'] }}"><li>{{ $featureTags->implode(' · ') }}</li></ul>
            @endif
            <p class="product-stage__lead">{{ $product['description'] }}</p>
            @if($technicalDetails->isNotEmpty())
                <dl class="product-stage__facts">@foreach($technicalDetails as $label => $value)<div><dt>{{ $label }}</dt><dd>{{ $value }}</dd></div>@endforeach</dl>
            @endif
            @if(!empty($settings['whatsapp']))
                <a class="button button--ink" href="https://wa.me/{{ preg_replace('/\D/', '', $settings['whatsapp']) }}">{{ __('site.product.whatsapp') }} <span>↗</span></a>
            @else
                <a class="button button--ink" href="{{ app(\App\Support\LocalizedUrl::class)->route('contact') }}">{{ __('site.actions.contact_us') }} <span>↗</span></a>
            @endif
        </div>
    </div>
</section>

<x-product-information :product="$product" />
<x-product-packaging :product="$product" />
@endsection
