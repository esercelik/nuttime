@props(['kicker', 'title', 'copy' => null])
<section class="page-hero"><div class="container"><p class="kicker">{{ $kicker }}</p><h1>{!! $title !!}</h1>@if($copy)<p>{{ $copy }}</p>@endif</div></section>
