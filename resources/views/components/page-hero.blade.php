@props(['kicker', 'title', 'copy' => null, 'richTitle' => false])
<section {{ $attributes->class('page-hero') }}><div class="container page-hero__inner"><p class="kicker"><x-safe-rich-text :value="$kicker" /></p><h1><x-safe-rich-text :value="$title" /></h1>@if($copy)<p><x-safe-rich-text :value="$copy" /></p>@endif</div></section>
