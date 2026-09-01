@extends('layouts.app')
@section('content')
<x-page-hero kicker="NUTTIME" :title="__('site.home.featured_title')" :copy="__('site.meta.products.description')" />
<section class="catalog-page"><div class="container"><div class="catalog-page__bar"><span>{{ __('site.catalog.products_count', ['count' => count($products)]) }}</span><a href="#catalog">{{ __('site.actions.discover') }} ↓</a></div><div id="catalog" class="catalog-page__grid">@forelse($products as $index => $product)<x-product-card :product="$product" :variant="$index === 0 ? 'hero' : 'default'" />@empty<p class="empty-state">{{ __('site.catalog.empty') }}</p>@endforelse</div></div></section><x-final-cta />
@endsection
