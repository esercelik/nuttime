@props(['section' => [], 'buttonUrl' => null])

<section class="closing-cta">
    @if(filled($section['desktop_image'] ?? null))
        <img src="{{ $section['desktop_image'] }}" alt="{{ strip_tags($section['title'] ?? '') }}" width="1900" height="500" loading="lazy" decoding="async">
    @else
    <x-optimized-image src="images/nuttime/spread-banner.jpg" alt="Kaşıkta Nuttime yer fıstığı ezmesi" width="1900" height="500" sizes="100vw" />
    @endif
    <div class="closing-cta__shade"></div>
    <div class="container closing-cta__inner">
        <div><p class="kicker">{{ $section['eyebrow'] ?? __('site.final_cta.kicker') }}</p><h2><x-safe-rich-text :value="$section['title'] ?? __('site.final_cta.title')" /></h2>@if(filled($section['description'] ?? null))<p><x-safe-rich-text :value="$section['description']" /></p>@endif</div>
        <div><a class="button" href="{{ $buttonUrl ?? app(\App\Support\LocalizedUrl::class)->route('contact') }}">{{ $section['button_label'] ?? __('site.actions.contact_us') }} <span>↗</span></a><a class="arrow-link arrow-link--light" href="{{ app(\App\Support\LocalizedUrl::class)->route('products') }}">{{ __('site.actions.all_products') }} <span>↗</span></a></div>
    </div>
</section>
