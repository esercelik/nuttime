@extends('layouts.app')

@section('content')
<x-page-hero :kicker="__('site.nav.contents')" :title="__('site.nav.contents')" :copy="__('site.meta.contents.description')" />
<section class="catalog-page"><div class="container"><div class="catalog-page__grid">@forelse($contents as $content)<a class="catalog-card" href="{{ app(\App\Support\LocalizedUrl::class)->route('content', null, ['slug' => $content['slug']]) }}"><div class="catalog-card__media">@if($content['image'])<img src="{{ $content['image'] }}" alt="{{ $content['image_alt'] ?: $content['title'] }}" width="900" height="600" loading="lazy">@endif</div><div class="catalog-card__info"><h3><x-safe-rich-text :value="$content['title']" /></h3><span><x-safe-rich-text :value="$content['excerpt']" /></span></div></a>@empty<p class="empty-state">{{ __('site.catalog.empty') }}</p>@endforelse</div></div></section>
@endsection
