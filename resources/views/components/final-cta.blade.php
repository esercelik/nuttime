<section class="closing-cta">
    <x-optimized-image src="images/nuttime/spread-banner.jpg" alt="Kaşıkta Nuttime yer fıstığı ezmesi" width="1900" height="500" sizes="100vw" />
    <div class="closing-cta__shade"></div>
    <div class="container closing-cta__inner">
        <div><p class="kicker">{{ __('site.final_cta.kicker') }}</p><h2>{!! __('site.final_cta.title') !!}</h2></div>
        <div><a class="button" href="{{ app(\App\Support\LocalizedUrl::class)->route('contact') }}">{{ __('site.actions.contact_us') }} <span>↗</span></a><a class="arrow-link arrow-link--light" href="{{ app(\App\Support\LocalizedUrl::class)->route('products') }}">{{ __('site.actions.all_products') }} <span>↗</span></a></div>
    </div>
</section>
