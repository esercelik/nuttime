@props(['kicker', 'title', 'copy' => null, 'href' => null, 'linkText' => 'Tümünü keşfet'])
<div class="section-heading">
    <div>
        <p class="kicker">{{ $kicker }}</p>
        <h2>{!! $title !!}</h2>
    </div>
    @if($copy)<p class="section-copy">{{ $copy }}</p>@endif
    @if($href)<a class="arrow-link" href="{{ $href }}">{{ $linkText }} <span>↗</span></a>@endif
</div>
