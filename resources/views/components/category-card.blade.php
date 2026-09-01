@props(['category', 'index' => 0])
<a class="category-tile category-tile--{{ $index % 4 }}" href="{{ url('/kategori/'.$category['slug']) }}">
    @if(!empty($category['image']))
    <img src="{{ $category['image'] }}" alt="{{ $category['name'] }}" width="1200" height="800" loading="lazy" decoding="async">
    @endif
    <div class="category-tile__veil"></div><div class="category-tile__caption"><p>0{{ $index + 1 }} / KATEGORİ</p><h3>{{ $category['name'] }}</h3><span>↗</span></div>
</a>
