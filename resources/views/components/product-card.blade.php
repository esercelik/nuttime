@props(['product', 'variant' => 'default'])
<a class="catalog-card catalog-card--{{ $variant }}" href="{{ url('/urunler/'.$product['slug']) }}">
    @if(!empty($product['image']))
    <div class="catalog-card__media" style="--product-tint:{{ $product['accent'] ?? '#d8b768' }}">
        <img src="{{ $product['image'] }}" alt="{{ $product['name']['tr'] ?? 'Nuttime ürünü' }}" width="900" height="1200" loading="lazy">
        <span class="catalog-card__arrow">↗</span>
    </div>
    @endif
    <div class="catalog-card__info"><p>{{ $product['category'] ?? 'Nuttime' }}</p><h3>{{ $product['name']['tr'] ?? 'Nuttime ürünü' }}</h3><span>Ürünü incele</span></div>
</a>
