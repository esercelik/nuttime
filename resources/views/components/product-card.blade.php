@props(['product'])
<a class="product-card" href="{{ url('/urunler/'.$product['slug']) }}">
    <div class="product-image" style="--accent:{{ $product['accent'] ?? '#d7b66c' }}">
        @if(!empty($product['image'])) <img src="{{ $product['image'] }}" alt="{{ $product['name']['tr'] ?? 'Nuttime ürünü' }}" width="520" height="520" loading="lazy"> @else <div class="image-placeholder">nut<span>time</span></div> @endif
        <span>↗</span>
    </div>
    <small>{{ $product['category'] ?? 'Nuttime seçkisi' }}</small>
    <h3>{{ $product['name']['tr'] ?? 'Nuttime ürünü' }}</h3>
    @if(!empty($product['description']))<p>{{ $product['description'] }}</p>@endif
    <b class="card-link">Ürünü keşfet <span>↗</span></b>
</a>
