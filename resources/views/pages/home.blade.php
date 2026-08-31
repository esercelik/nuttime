@extends('layouts.app')

@section('content')
@php
    $heroSlides = [
        [
            'eyebrow' => 'NUTTIME • YOĞUN LEZZET',
            'headline' => ['ANTEP FISTIĞININ', 'EN YOĞUN HALİ'],
            'description' => 'Yüzde 42 Antep fıstığı ve parçacıklı dokusuyla her kaşıkta gerçek fıstık lezzeti.',
            'product_url' => route('product', ['slug' => 'antep-fistikli-kremasi']),
            'background' => asset('images/nuttime/spylt/nuttime-antep-hero-background.png'),
            'background_size' => [1672, 941],
            'product_image' => asset('images/nuttime/spylt/nuttime-antep-jar-transparent.png'),
            'product_size' => [1024, 1536],
            'product_class' => '',
        ],
        [
            'eyebrow' => 'NUTTIME • KLASİK SEÇKİ',
            'headline' => ['FINDIĞIN', 'YALIN HALİ'],
            'description' => 'Yüzde 45 fındıkla hazırlanan yoğun, dengeli ve her kaşıkta karakterini gösteren bir klasik.',
            'product_url' => route('product', ['slug' => 'findik-kremasi']),
            'background' => asset('images/nuttime/brand-moment.jpg'),
            'background_size' => [1920, 1280],
            'product_image' => asset('images/nuttime/hazelnut-butter.jpg'),
            'product_size' => [1707, 2560],
            'product_class' => 'product-hero__jar--photo',
        ],
        [
            'eyebrow' => 'NUTTIME • GÜNLÜK RİTÜEL',
            'headline' => ['KENDİ GİBİ', 'YOĞUN'],
            'description' => 'Parçacıklı yer fıstığı ezmesiyle günün her anına eşlik eden güçlü ve gerçek lezzet.',
            'product_url' => route('product', ['slug' => 'yer-fistigi-ezmesi']),
            'background' => asset('images/nuttime/brand-story.jpg'),
            'background_size' => [1707, 2560],
            'product_image' => asset('images/nuttime/peanut-butter.jpg'),
            'product_size' => [1707, 2560],
            'product_class' => 'product-hero__jar--photo',
        ],
    ];
@endphp

<section class="product-hero" aria-label="Öne çıkan ürünler" aria-roledescription="carousel" tabindex="0" data-product-hero data-autoplay="6500">
    <div class="product-hero__slides">
        @foreach($heroSlides as $index => $slide)
            <article class="product-hero__slide {{ $index === 0 ? 'is-active' : '' }}" aria-roledescription="slide" aria-label="{{ $index + 1 }} / {{ count($heroSlides) }}" aria-hidden="{{ $index === 0 ? 'false' : 'true' }}" @if($index !== 0) hidden @endif data-product-hero-slide>
                <img class="product-hero__background" src="{{ $slide['background'] }}" alt="" width="{{ $slide['background_size'][0] }}" height="{{ $slide['background_size'][1] }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}" decoding="async" data-product-hero-background>
                <div class="product-hero__veil" aria-hidden="true"></div>

                <div class="product-hero__visual-anchor" aria-hidden="true">
                    <div class="product-hero__scroll-visual" data-product-hero-scroll-visual>
                        <div class="product-hero__visual" data-product-hero-visual>
                            <div class="product-hero__jar-float" data-product-hero-jar-float>
                                <img class="product-hero__jar {{ $slide['product_class'] }}" src="{{ $slide['product_image'] }}" alt="" width="{{ $slide['product_size'][0] }}" height="{{ $slide['product_size'][1] }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}" decoding="async" data-product-hero-jar>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container product-hero__copy" data-product-hero-copy>
                    <div class="product-hero__copy-content" data-product-hero-copy-content>
                        <p class="product-hero__eyebrow">{{ $slide['eyebrow'] }}</p>
                        <h1>
                            @foreach($slide['headline'] as $line)
                                <span class="product-hero__headline-line" data-product-hero-headline>{{ $line }}</span>
                            @endforeach
                        </h1>
                        <p class="product-hero__description">{{ $slide['description'] }}</p>
                        <div class="product-hero__actions" data-product-hero-actions>
                            <a class="button product-hero__primary-action" href="{{ $slide['product_url'] }}">Ürünleri Keşfet <span>↗</span></a>
                            <a class="product-hero__secondary-action" href="{{ route('products') }}">Tüm Ürünler <span>↗</span></a>
                        </div>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="product-hero__controls" aria-label="Ürün slayt kontrolleri">
        <button type="button" aria-label="Önceki ürün" data-product-hero-previous>←</button>
        <div class="product-hero__pagination" aria-label="Slayt seçimi">
            @foreach($heroSlides as $index => $slide)
                <button type="button" class="{{ $index === 0 ? 'is-active' : '' }}" aria-label="{{ $index + 1 }}. slaytı göster" aria-current="{{ $index === 0 ? 'true' : 'false' }}" data-product-hero-pagination="{{ $index }}"></button>
            @endforeach
        </div>
        <span class="product-hero__counter" aria-live="polite"><b data-product-hero-current>01</b> / {{ str_pad((string) count($heroSlides), 2, '0', STR_PAD_LEFT) }}</span>
        <button type="button" aria-label="Sonraki ürün" data-product-hero-next>→</button>
    </div>

    <a class="product-hero__scroll-hint" href="#home-banners"><span></span>Aşağı Kaydır</a>
</section>

<section id="home-banners" class="hero-banners" aria-label="Nuttime seçkileri">
    <a class="hero-banner hero-banner--pistachio" href="{{ route('product', ['slug' => 'antep-fistikli-kremasi']) }}">
        <img src="{{ asset('images/nuttime/collection-banner.jpg') }}" alt="Nuttime Antep fıstığı ezmesi seçkisi" width="1900" height="500" loading="lazy" decoding="async">
        <span class="hero-banner__veil"></span>
        <span class="hero-banner__copy"><small>YENİ NESİL LEZZET</small><strong>Antep fıstığını<br>yeniden keşfet.</strong><em>İncele ↗</em></span>
    </a>
    <a class="hero-banner hero-banner--spread" href="{{ route('products') }}">
        <img src="{{ asset('images/nuttime/spread-banner.jpg') }}" alt="Nuttime ezme seçkisi" width="1900" height="500" loading="lazy" decoding="async">
        <span class="hero-banner__veil"></span>
        <span class="hero-banner__copy"><small>HER GÜNE EŞLİK EDER</small><strong>Bir kaşıkta<br>iyi his.</strong><em>Tüm ürünler ↗</em></span>
    </a>
</section>

<section class="home-intro">
    <div class="container home-intro__inner">
        <p class="kicker">NUTTIME</p>
        <p>İyi kuruyemişleri, yalın içeriklerle <em>günün en keyifli anına</em> dönüştürüyoruz.</p>
        <a class="arrow-link" href="{{ url('/hakkimizda') }}">Hikâyemizi keşfet <span>↗</span></a>
    </div>
</section>

@php($featured = collect($products)->filter(fn ($product) => $product['featured'] ?? false)->take(3))
@if($featured->isNotEmpty())
<section class="feature-showcase home-section">
    <div class="container">
        <x-section-heading kicker="ÖZENLE SEÇTİK" title="Öne çıkan<br><em>lezzetler.</em>" href="{{ url('/urunlerimiz') }}" />
        <div class="feature-showcase__grid">
            @foreach($featured as $key => $product)
                <x-product-card :product="$product" :variant="$key === 0 ? 'hero' : 'mini'" />
            @endforeach
        </div>
    </div>
</section>
@endif

@if(count($categories))
<section class="category-showcase home-section">
    <div class="container">
        <x-section-heading kicker="KATEGORİLER" title="İyi olanı<br><em>bul.</em>" />
        <div class="category-showcase__grid">
            @foreach($categories as $index => $category)
                <x-category-card :category="$category" :index="$index" />
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="brand-moment" aria-label="Nuttime ürün seçkisi">
    <img src="{{ asset('images/nuttime/collection-banner.jpg') }}" alt="Fındık, Hindistan cevizi, Antep fıstığı ve badem Nuttime ezmeleri" width="1900" height="500" loading="lazy">
    <div class="brand-moment__shade"></div>
    <div class="container brand-moment__copy">
        <p class="kicker">BİR KAVANOZDAN DAHA FAZLASI</p>
        <p>Her zevke, her ritme<br><em>iyi eşlik eder.</em></p>
    </div>
</section>

<section class="brand-story">
    <div class="brand-story__image">
        <img src="{{ asset('images/nuttime/brand-story.jpg') }}" alt="Nuttime fındık ezmesi ve fındıklı ekmek" width="1707" height="2560" loading="lazy">
    </div>
    <div class="brand-story__copy">
        <p class="kicker">BİZ KİMİZ?</p>
        <h2>İyi ürün,<br><em>iyi his,</em><br>iyi bir hayat.</h2>
        <p>Nuttime, özenle seçilmiş kuruyemişlerin doğal karakterini her gün yeniden keşfetmeniz için hazırlar.</p>
        <a class="arrow-link arrow-link--light" href="{{ url('/hakkimizda') }}">Hikâyemizi keşfet <span>↗</span></a>
    </div>
</section>

@php($certificatesWithImages = collect($certificates)->filter(fn ($certificate) => !empty($certificate['image'])))
@if($certificatesWithImages->isNotEmpty())
<section class="quality-rail home-section">
    <div class="container">
        <x-section-heading kicker="GÜVENLE ÜRETİYORUZ" title="Kalite, her<br><em>detayda.</em>" href="{{ url('/sertifikalarimiz') }}" />
        <div class="quality-rail__items">
            @foreach($certificatesWithImages as $certificate)
            <article>
                <img src="{{ $certificate['image'] }}" alt="{{ $certificate['name'] }}" width="260" height="160" loading="lazy">
                <div><h3>{{ $certificate['name'] }}</h3>@if($certificate['description'])<p>{{ $certificate['description'] }}</p>@endif</div>
                @if($certificate['document'])<a href="{{ $certificate['document'] }}" target="_blank" rel="noopener">Belgeyi aç ↗</a>@endif
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

<x-factory-location :factory="$factory" />

@if(!empty($settings['instagram']) && $settings['instagram'] !== '#')
<section class="social-callout">
    <div class="container">
        <p class="kicker">GÜNLÜK İLHAM</p>
        <h2>Nuttime'ı<br><em>yakından takip edin.</em></h2>
        <a class="arrow-link arrow-link--light" href="{{ $settings['instagram'] }}" target="_blank" rel="noopener">Instagram'da aç <span>↗</span></a>
    </div>
</section>
@endif

<x-final-cta />
@endsection
