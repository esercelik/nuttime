@props(['section', 'products' => [], 'categories' => [], 'certificates' => [], 'factory' => [], 'settings' => []])

@php($safeUrl = static fn (?string $url): ?string => str_starts_with((string) $url, '/') || (filter_var($url, FILTER_VALIDATE_URL) && str_starts_with((string) $url, 'https://')) ? $url : null)
@php($buttonUrl = $safeUrl($section['button_url'] ?? null))

@switch($section['type'])
    @case('banner')
        @if(!empty($section['desktop_image']))
            <section class="brand-moment" @if(!empty($section['background_color'])) style="background-color: {{ $section['background_color'] }}" @endif>
                <img src="{{ $section['desktop_image'] }}" alt="{{ $section['title'] ?? '' }}" width="1900" height="500" loading="lazy" decoding="async">
                @if(($section['settings']['overlay'] ?? true))<div class="brand-moment__shade"></div>@endif
                <div class="container brand-moment__copy"><p class="kicker">{{ $section['eyebrow'] }}</p><p>{{ $section['title'] }}</p>@if($buttonUrl)<a class="arrow-link arrow-link--light" href="{{ $buttonUrl }}">{{ $section['button_label'] ?: __('site.actions.learn_more') }} <span>↗</span></a>@endif</div>
            </section>
        @endif
        @break

    @case('intro')
        <section class="home-intro"><div class="container home-intro__inner"><p class="kicker">{{ $section['eyebrow'] }}</p><p>{{ $section['title'] }}</p>@if($section['description'])<p class="section-copy">{{ $section['description'] }}</p>@endif @if($buttonUrl)<a class="arrow-link" href="{{ $buttonUrl }}">{{ $section['button_label'] ?: __('site.actions.learn_more') }} <span>↗</span></a>@endif</div></section>
        @break

    @case('featured_products')
        @php($featuredProducts = collect($products)->filter(fn (array $product): bool => $product['featured'] ?? false)->take($section['settings']['limit'] ?? 3))
        @if($featuredProducts->isNotEmpty())<section class="feature-showcase home-section"><div class="container"><x-section-heading :kicker="$section['eyebrow']" :title="$section['title']" :href="$buttonUrl" :link-text="$section['button_label']" /><div class="feature-showcase__grid">@foreach($featuredProducts as $key => $product)<x-product-card :product="$product" :index="$key" :variant="$key === 0 ? 'hero' : 'mini'" />@endforeach</div></div></section>@endif
        @break

    @case('categories')
        @php($managedCategories = collect($categories)->take($section['settings']['limit'] ?? 6))
        @if($managedCategories->isNotEmpty())<section class="category-showcase home-section"><div class="container"><x-section-heading :kicker="$section['eyebrow']" :title="$section['title']" /><div class="category-showcase__grid">@foreach($managedCategories as $index => $category)<x-category-card :category="$category" :index="$index" />@endforeach</div></div></section>@endif
        @break

    @case('story')
        <section class="brand-story">@if($section['desktop_image'])<div class="brand-story__image"><img src="{{ $section['desktop_image'] }}" alt="{{ $section['title'] ?? '' }}" width="1707" height="2560" loading="lazy" decoding="async"></div>@endif<div class="brand-story__copy"><p class="kicker">{{ $section['eyebrow'] }}</p><h2>{{ $section['title'] }}</h2><p>{{ $section['description'] }}</p>@if($buttonUrl)<a class="arrow-link arrow-link--light" href="{{ $buttonUrl }}">{{ $section['button_label'] ?: __('site.actions.learn_more') }} <span>↗</span></a>@endif</div></section>
        @break

    @case('certificates')
        @php($managedCertificates = collect($certificates)->filter(fn (array $certificate): bool => !empty($certificate['image'])))
        @if($managedCertificates->isNotEmpty())<section class="quality-rail home-section"><div class="container"><x-section-heading :kicker="$section['eyebrow']" :title="$section['title']" /><div class="quality-rail__items">@foreach($managedCertificates as $certificate)<article><img src="{{ $certificate['image'] }}" alt="{{ $certificate['name'] }}" width="260" height="160" loading="lazy" decoding="async"><div><h3>{{ $certificate['name'] }}</h3><p>{{ $certificate['description'] }}</p></div></article>@endforeach</div></div></section>@endif
        @break

    @case('factory')
        <x-factory-location :factory="$factory" />
        @break

    @case('social')
        @php($network = $section['settings']['network'] ?? 'instagram')
        @php($socialUrl = $safeUrl($settings[$network] ?? null))
        @if($socialUrl)<section class="social-callout"><div class="container"><p class="kicker">{{ $section['eyebrow'] }}</p><h2>{{ $section['title'] }}</h2><a class="arrow-link arrow-link--light" href="{{ $socialUrl }}" target="_blank" rel="noopener noreferrer">{{ $section['button_label'] ?: ucfirst($network) }} <span>↗</span></a></div></section>@endif
        @break

    @case('cta')
    @case('custom')
        <section class="closing-cta"><div class="container closing-cta__inner"><div><p class="kicker">{{ $section['eyebrow'] }}</p><h2>{{ $section['title'] }}</h2>@if($section['description'])<p>{{ $section['description'] }}</p>@endif</div>@if($buttonUrl)<a class="button button--dark" href="{{ $buttonUrl }}">{{ $section['button_label'] ?: __('site.actions.learn_more') }} <span>↗</span></a>@endif</div></section>
        @break
@endswitch
