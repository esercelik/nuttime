@extends('layouts.app')
@section('content')
<x-page-hero class="page-hero--contact" :kicker="__('site.contact.kicker')" :title="__('site.contact.title')" :copy="__('site.contact.copy')" rich-title />
<section class="contact-page"><div class="container contact-page__grid"><aside class="contact-page__channels"><p class="kicker">{{ __('site.contact.section') }}</p><div class="contact-page__channel"><span>E</span><a href="mailto:{{ $settings['email'] ?? 'hello@nuttime.com.tr' }}">{{ $settings['email'] ?? 'hello@nuttime.com.tr' }}</a></div><div class="contact-page__channel"><span>T</span><a href="tel:{{ $settings['phone'] ?? '' }}">{{ $settings['phone'] ?? '+90 212 123 45 67' }}</a></div>@if(!empty($settings['address']))<p class="contact-page__address"><x-safe-rich-text :value="$settings['address']" /></p>@endif</aside><form method="POST" action="{{ app(\App\Support\LocalizedUrl::class)->route('contact.store') }}" class="contact-page__form" x-data="{ submitting: false }" @submit="submitting = true">@csrf @if(session('success'))<div class="success" role="status">{{ session('success') }}</div>@endif<div class="honeypot"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div><div class="contact-page__field"><label for="contact-name">{{ __('site.contact.name') }}</label><input id="contact-name" name="name" value="{{ old('name') }}" autocomplete="name" required>@error('name')<small>{{ $message }}</small>@enderror</div><div class="contact-page__field"><label for="contact-email">{{ __('site.contact.email') }}</label><input id="contact-email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<small>{{ $message }}</small>@enderror</div><div class="contact-page__field"><label for="contact-message">{{ __('site.contact.message') }}</label><textarea id="contact-message" name="message" rows="6" required>{{ old('message') }}</textarea>@error('message')<small>{{ $message }}</small>@enderror</div><button class="button button--ink" type="submit" :disabled="submitting" :aria-busy="submitting"><span>{{ __('site.actions.send_message') }}</span><i x-show="!submitting" aria-hidden="true">↗</i><i x-show="submitting" x-cloak aria-hidden="true">…</i></button></form></div></section>

<section class="container about-editorial__contact" aria-labelledby="company-information-title">
    <div><p class="kicker">{{ __('site.static.company_kicker') }}</p><h2 id="company-information-title">{{ __('site.static.company_title') }}</h2></div>
    <dl>
        <div><dt>{{ __('site.static.phone_label') }}</dt><dd><a href="tel:+905351006030">+90 535 100 60 30</a><a href="tel:+902642735943">+90 264 273 59 43</a><a href="tel:+905353024579">+90 535 302 45 79</a></dd></div>
        <div><dt>{{ __('site.static.location_label') }}</dt><dd>Söğütlü / SAKARYA</dd></div>
        <div><dt>{{ __('site.static.address_label') }}</dt><dd>Sakarya 3. Organize Sanayi Bölgesi<br>Soğucak OSB Mah. No:81<br>Söğütlü / Sakarya / TÜRKİYE</dd></div>
    </dl>
</section>

<x-factory-location :factory="$factory" :settings="$settings" variant="contact" />
@endsection
