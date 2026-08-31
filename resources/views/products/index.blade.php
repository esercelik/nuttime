@extends('layouts.app')
@section('content')
<x-page-hero kicker="NUTTIME SEÇKİSİ" title="Her ana eşlik eden<br><em>iyi lezzetler.</em>" copy="Doğadan gelen yalın ve yoğun tatlar." />
<section class="catalog-page"><div class="container"><div class="catalog-page__bar"><span>{{ count($products) }} ürün</span><a href="#catalog">Seçkiyi keşfet ↓</a></div><div id="catalog" class="catalog-page__grid">@forelse($products as $index => $product)<x-product-card :product="$product" :variant="$index === 0 ? 'hero' : 'default'" />@empty<p class="empty-state">Seçkimiz yakında burada yer alacak.</p>@endforelse</div></div></section><x-final-cta />
@endsection
