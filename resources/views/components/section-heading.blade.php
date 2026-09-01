@props(['kicker', 'title', 'copy' => null, 'href' => null, 'linkText' => 'Tümünü keşfet', 'richTitle' => false])
<div {{ $attributes->class('section-heading') }}>
    <div>
        <p class="kicker">{{ $kicker }}</p>
        <h2>@if($richTitle){!! $title !!}@else{{ $title }}@endif</h2>
    </div>
    @if($copy)<p class="section-copy">{{ $copy }}</p>@endif
    @if($href)<a class="arrow-link" href="{{ $href }}">{{ $linkText }} <span>↗</span></a>@endif
</div>
