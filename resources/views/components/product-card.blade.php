@props(['product', 'variant' => 'default', 'priority' => false, 'index' => 0])
<a {{ $attributes->class('catalog-card catalog-card--'.$variant) }} href="{{ app(\App\Support\LocalizedUrl::class)->route('product', null, ['slug' => $product['slug']]) }}">
    @if(!empty($product['image']))
    <div class="catalog-card__media" style="--product-tint:{{ $product['accent'] ?? '#d8b768' }}">
        <img src="{{ $product['image'] }}" alt="{{ $product['image_alt'] ?? $product['name'] ?? 'Nuttime' }}" width="1707" height="2560" loading="{{ $priority ? 'eager' : 'lazy' }}" @if($priority) fetchpriority="high" @endif decoding="async">
        <span class="catalog-card__index" aria-hidden="true">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
        <span class="catalog-card__arrow" aria-hidden="true">↗</span>
    </div>
    @endif
    <div class="catalog-card__info">
        <p>{{ $product['category'] ?? 'Nuttime' }}</p>
        <h3>{{ $product['name'] ?? 'Nuttime' }}</h3>
        @if(!empty($product['description']))
            <span class="catalog-card__summary">{{ \Illuminate\Support\Str::limit($product['description'], 94) }}</span>
        @endif
        <span class="catalog-card__action">{{ __('site.actions.view_product') }} <b>↗</b></span>
    </div>
</a>
