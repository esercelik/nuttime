@props(['kicker', 'title', 'copy' => null, 'href' => null, 'linkText' => 'Tümünü keşfet', 'richTitle' => false])
<div {{ $attributes->class('section-heading') }}>
    <div>
        <p class="kicker"><x-safe-rich-text :value="$kicker" /></p>
        <h2><x-safe-rich-text :value="$title" /></h2>
    </div>
    @if($copy)<p class="section-copy"><x-safe-rich-text :value="$copy" /></p>@endif
    @if($href)<a class="arrow-link" href="{{ $href }}"><x-safe-rich-text :value="$linkText" /> <span>↗</span></a>@endif
</div>
