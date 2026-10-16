@extends('layouts.app')

@section('content')
<x-page-hero :kicker="__('site.nav.contents')" :title="__('site.nav.contents')" :copy="__('site.meta.contents.description')" />
<section class="catalog-page content-index">
    <div class="container">
        <div class="catalog-page__grid">
            @forelse($contents as $content)
                <a class="catalog-card" href="{{ app(\App\Support\LocalizedUrl::class)->route('content', null, ['slug' => $content['slug']]) }}">
                    @if($content['image'])
                        <div class="catalog-card__media"><img src="{{ $content['image'] }}" alt="{{ $content['image_alt'] ?: $content['title'] }}" width="900" height="600" loading="lazy"></div>
                    @endif
                    <div class="catalog-card__info"><h3><x-safe-rich-text :value="$content['title']" /></h3><span><x-safe-rich-text :value="$content['excerpt']" /></span></div>
                </a>
            @empty
                <a class="catalog-card catalog-card--story" href="{{ app(\App\Support\LocalizedUrl::class)->route('about') }}">
                    <div class="catalog-card__media"><img src="{{ asset('images/nuttime/brand-story.jpg') }}" alt="Nuttime" width="900" height="600" loading="lazy"></div>
                    <div class="catalog-card__info"><p class="kicker">{{ __('site.home.story_kicker') }}</p><h3>{{ __('site.static.who_title') }}</h3><span>{{ __('site.home.story_copy') }}</span><span class="arrow-link">{{ __('site.actions.discover_story') }} ↗</span></div>
                </a>
            @endforelse
        </div>
    </div>
</section>
<x-final-cta />
@endsection
