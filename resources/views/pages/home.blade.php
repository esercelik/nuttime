@extends('layouts.app')

@section('content')
<section class="home-hero">
    <img src="{{ asset('images/nuttime/brand-moment.jpg') }}" alt="Yer fıstıkları arasında Nuttime yer fıstığı ezmesi" width="1920" height="1280" fetchpriority="high">
    <div class="home-hero__shade"></div>
    <div class="container home-hero__copy">
        <p class="kicker">DOĞADAN GELEN İYİ FİKİR</p>
        <h1>Gerçek lezzet,<br><em>iyi his.</em></h1>
        <p>Özenle seçilmiş kuruyemişlerle hazırlanan yoğun ve karakterli ezmeler.</p>
        <a class="button" href="{{ url('/urunlerimiz') }}">Seçkiyi keşfet <span>↗</span></a>
    </div>
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
