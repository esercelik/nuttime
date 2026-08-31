@props(['eyebrow' => null, 'title', 'link' => null, 'linkText' => null, 'intro' => null])
<div class="section-header"><div><p class="eyebrow">{{ $eyebrow }}</p><h2>{{ $title }}</h2>@if($intro)<p class="section-intro">{{ $intro }}</p>@endif</div>@if($link)<a class="text-link" href="{{ $link }}">{{ $linkText ?? 'Tümünü gör' }} <span>↗</span></a>@endif</div>
