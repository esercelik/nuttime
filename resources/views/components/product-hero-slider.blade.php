@props(['slides' => []])

@if(count($slides))
    <section class="product-hero" aria-label="{{ __('site.home.featured_title') }}" aria-roledescription="carousel" tabindex="0" data-product-hero data-autoplay="6500">
        <div class="product-hero__slides">
            @foreach($slides as $index => $slide)
                <article class="product-hero__slide {{ $index === 0 ? 'is-active' : '' }}" aria-roledescription="slide" aria-label="{{ $index + 1 }} / {{ count($slides) }}" aria-hidden="{{ $index === 0 ? 'false' : 'true' }}" @if($index !== 0) hidden @endif data-product-hero-slide>
                    <img class="product-hero__background" src="{{ $slide['background_image'] }}" alt="" width="1920" height="1080" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}" decoding="async" data-product-hero-background>
                    <div class="product-hero__gradient" aria-hidden="true"></div>
                    <div class="product-hero__backdrop-type" aria-hidden="true">{{ $slide['name'] }}</div>
                    <img class="product-hero__ingredients product-hero__ingredients--back" src="{{ $slide['ingredient_image'] }}" alt="" width="1600" height="1200" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" decoding="async" data-product-hero-decoration>
                    <img class="product-hero__ingredients product-hero__ingredients--front" src="{{ $slide['ingredient_image'] }}" alt="" width="1600" height="1200" loading="lazy" decoding="async" data-product-hero-decoration>

                    <div class="product-hero__visual-anchor">
                        <div class="product-hero__scroll-visual" data-product-hero-scroll-visual>
                            <div class="product-hero__visual" data-product-hero-visual>
                                <div class="product-hero__jar-float" data-product-hero-jar-float>
                                    <img class="product-hero__jar" src="{{ $slide['product_image'] }}" alt="{{ $slide['name'] }}" width="1312" height="1199" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}" decoding="async" data-product-hero-jar>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container product-hero__copy" data-product-hero-copy>
                        <div class="product-hero__copy-content" data-product-hero-copy-content>
                            <p class="product-hero__eyebrow">{{ $slide['category'] }}</p>
                            @if($index === 0)<h1><span class="product-hero__headline-line" data-product-hero-headline>{{ $slide['name'] }}</span></h1>@else<h2 class="product-hero__slide-heading"><span class="product-hero__headline-line" data-product-hero-headline>{{ $slide['name'] }}</span></h2>@endif
                            <p class="product-hero__description">{{ $slide['description'] }}</p>
                            <div class="product-hero__actions" data-product-hero-actions>
                                <a class="button product-hero__primary-action" href="{{ $slide['url'] }}">{{ $slide['cta_label'] ?? __('site.actions.view_product') }} <span>↗</span></a>
                                <a class="product-hero__secondary-action" href="{{ app(\App\Support\LocalizedUrl::class)->route('products') }}">{{ __('site.actions.all_products') }} <span>↗</span></a>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if(count($slides) > 1)
            <div class="product-hero__controls" aria-label="{{ __('site.actions.all_products') }}">
                <button type="button" aria-label="{{ __('site.actions.scroll_down') }}" data-product-hero-previous>←</button>
                <div class="product-hero__pagination" aria-label="{{ __('site.actions.all_products') }}">
                    @foreach($slides as $index => $slide)
                        <button type="button" class="{{ $index === 0 ? 'is-active' : '' }}" aria-label="{{ $slide['name'] }} slaydını göster" aria-current="{{ $index === 0 ? 'true' : 'false' }}" data-product-hero-pagination="{{ $index }}"></button>
                    @endforeach
                </div>
                <span class="product-hero__counter" aria-live="off"><b data-product-hero-current>01</b> / {{ str_pad((string) count($slides), 2, '0', STR_PAD_LEFT) }}</span>
                <span class="product-hero__progress" aria-hidden="true"><span data-product-hero-progress></span></span>
                <button type="button" aria-label="{{ __('site.actions.all_products') }}" data-product-hero-next>→</button>
            </div>
        @endif

        <a class="product-hero__scroll-hint" href="#home-banners"><span></span>{{ __('site.actions.scroll_down') }}</a>
    </section>
@else
    <section class="product-hero product-hero--fallback" aria-label="Nuttime">
        <div class="container product-hero__copy"><div class="product-hero__copy-content"><p class="product-hero__eyebrow">NUTTIME</p><h1>{!! __('site.home.featured_title') !!}</h1><a class="button" href="{{ app(\App\Support\LocalizedUrl::class)->route('products') }}">{{ __('site.actions.all_products') }} <span>↗</span></a></div></div>
    </section>
@endif
