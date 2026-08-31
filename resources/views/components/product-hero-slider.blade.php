@props(['slides' => []])

@if(count($slides))
    <section class="product-hero" aria-label="Öne çıkan ürünler" aria-roledescription="carousel" tabindex="0" data-product-hero data-autoplay="6500">
        <div class="product-hero__slides">
            @foreach($slides as $index => $slide)
                <article class="product-hero__slide product-hero--{{ $slide['theme'] }} {{ $index === 0 ? 'is-active' : '' }}" aria-roledescription="slide" aria-label="{{ $index + 1 }} / {{ count($slides) }}" aria-hidden="{{ $index === 0 ? 'false' : 'true' }}" @if($index !== 0) hidden @endif data-product-hero-slide>
                    <img class="product-hero__background" src="{{ $slide['background_image'] }}" alt="" width="1707" height="1280" loading="{{ $index < 2 ? 'eager' : 'lazy' }}" fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}" decoding="async" data-product-hero-background>
                    <div class="product-hero__gradient" aria-hidden="true"></div>
                    <div class="product-hero__backdrop-type" aria-hidden="true">{{ $slide['name'] }}</div>
                    <div class="product-hero__decorations" aria-hidden="true">
                        <span data-product-hero-decoration></span><span data-product-hero-decoration></span><span data-product-hero-decoration></span>
                    </div>

                    <div class="product-hero__visual-anchor">
                        <div class="product-hero__scroll-visual" data-product-hero-scroll-visual>
                            <div class="product-hero__visual" data-product-hero-visual>
                                <div class="product-hero__jar-float" data-product-hero-jar-float>
                                    <img class="product-hero__jar {{ $slide['product_is_photo'] ? 'product-hero__jar--photo' : '' }}" src="{{ $slide['product_image'] }}" alt="{{ $slide['name'] }}" width="1024" height="1536" loading="{{ $index < 2 ? 'eager' : 'lazy' }}" fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}" decoding="async" data-product-hero-jar>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container product-hero__copy" data-product-hero-copy>
                        <div class="product-hero__copy-content" data-product-hero-copy-content>
                            <p class="product-hero__eyebrow">{{ $slide['category'] }}</p>
                            <h1><span class="product-hero__headline-line" data-product-hero-headline>{{ $slide['name'] }}</span></h1>
                            <p class="product-hero__description">{{ $slide['description'] }}</p>
                            <div class="product-hero__actions" data-product-hero-actions>
                                <a class="button product-hero__primary-action" href="{{ $slide['url'] }}">Ürünü İncele <span>↗</span></a>
                                <a class="product-hero__secondary-action" href="{{ route('products') }}">Tüm Ürünler <span>↗</span></a>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if(count($slides) > 1)
            <div class="product-hero__controls" aria-label="Ürün slayt kontrolleri">
                <button type="button" aria-label="Önceki ürün" data-product-hero-previous>←</button>
                <div class="product-hero__pagination" aria-label="Slayt seçimi">
                    @foreach($slides as $index => $slide)
                        <button type="button" class="{{ $index === 0 ? 'is-active' : '' }}" aria-label="{{ $slide['name'] }} slaydını göster" aria-current="{{ $index === 0 ? 'true' : 'false' }}" data-product-hero-pagination="{{ $index }}"></button>
                    @endforeach
                </div>
                <span class="product-hero__counter" aria-live="off"><b data-product-hero-current>01</b> / {{ str_pad((string) count($slides), 2, '0', STR_PAD_LEFT) }}</span>
                <span class="product-hero__progress" aria-hidden="true"><span data-product-hero-progress></span></span>
                <button type="button" aria-label="Sonraki ürün" data-product-hero-next>→</button>
            </div>
        @endif

        <a class="product-hero__scroll-hint" href="#home-banners"><span></span>Aşağı Kaydır</a>
    </section>
@else
    <section class="product-hero product-hero--fallback" aria-label="Nuttime ürünleri">
        <div class="container product-hero__copy"><div class="product-hero__copy-content"><p class="product-hero__eyebrow">NUTTIME</p><h1>İyi olanı<br>keşfet.</h1><a class="button" href="{{ route('products') }}">Ürünleri İncele <span>↗</span></a></div></div>
    </section>
@endif
