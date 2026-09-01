@extends('layouts.app')
@section('content')
<section class="category-intro"><img src="{{ $category['image'] }}" alt="{{ $category['name'] }}" width="1800" height="900"><div class="category-intro__shade"></div><div class="container category-intro__copy"><p class="kicker">{{ __('site.catalog.category_count', ['count' => str_pad((string) count($products), 2, '0', STR_PAD_LEFT)]) }}</p><h1>{{ $category['name'] }}</h1><p>{{ $category['description'] }}</p></div></section><section class="catalog-page"><div class="container"><div class="catalog-page__grid">@forelse($products as $index => $product)<x-product-card :product="$product" :variant="$index === 0 ? 'hero' : 'default'" />@empty<p class="empty-state">{{ __('site.catalog.category_empty') }}</p>@endforelse</div></div></section>
@endsection
