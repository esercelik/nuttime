@props(['kicker', 'title', 'copy' => null, 'richTitle' => false])
<section {{ $attributes->class('page-hero') }}><div class="container page-hero__inner"><p class="kicker">{{ $kicker }}</p><h1>@if($richTitle){!! $title !!}@else{{ $title }}@endif</h1>@if($copy)<p>{{ $copy }}</p>@endif</div></section>
