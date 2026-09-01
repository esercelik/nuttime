@props(['product', 'variant' => 'default'])
<a class="catalog-card catalog-card--{{ $variant }}" href="{{ app(\App\Support\LocalizedUrl::class)->route('product', null, ['slug' => $product['slug']]) }}">
    @if(!empty($product['image']))
    <div class="catalog-card__media" style="--product-tint:{{ $product['accent'] ?? '#d8b768' }}">
        <img src="{{ $product['image'] }}" alt="{{ $product['name'] ?? 'Nuttime' }}" width="1707" height="2560" loading="lazy" decoding="async">
        <span class="catalog-card__arrow">↗</span>
    </div>
    @endif
    <div class="catalog-card__info"><p>{{ $product['category'] ?? 'Nuttime' }}</p><h3>{{ $product['name'] ?? 'Nuttime' }}</h3><span>{{ __('site.actions.view_product') }}</span></div>
</a>
