@extends('layouts.app')

@section('content')
<x-page-hero :kicker="__('site.nav.contents')" :title="$content['title']" :copy="$content['excerpt']" />
<article class="about-editorial"><div class="container"><div class="about-editorial__lead"><p><x-safe-rich-text :value="$content['excerpt']" /></p></div>@if($content['image'])<div class="about-editorial__media"><img src="{{ $content['image'] }}" alt="{{ $content['image_alt'] ?: $content['title'] }}" width="1400" height="900"></div>@endif<div class="about-editorial__copy"><p><x-safe-rich-text :value="$content['body']" /></p></div></div></article>
@endsection
