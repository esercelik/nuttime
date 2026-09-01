<!doctype html>
<html lang="{{ config('nuttime.locales.'.app()->getLocale().'.html', app()->getLocale()) }}" dir="{{ config('nuttime.locales.'.app()->getLocale().'.direction', 'ltr') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $seo['title'] ?? 'Nuttime' }}</title>
    <meta name="description" content="{{ $seo['description'] ?? '' }}">
    <meta name="robots" content="{{ $seo['robots'] ?? 'index,follow' }}">
    <link rel="canonical" href="{{ $seo['canonical'] ?? url()->current() }}">
    @foreach(($seo['alternates'] ?? []) as $locale => $alternate)
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ $alternate }}">
    @endforeach
    @if(!empty($seo['alternates']))
        <link rel="alternate" hreflang="x-default" href="{{ $seo['alternates'][config('nuttime.default_locale')] }}">
    @endif
    <meta property="og:title" content="{{ $seo['title'] ?? 'Nuttime' }}">
    <meta property="og:description" content="{{ $seo['description'] ?? '' }}">
    <meta property="og:url" content="{{ $seo['canonical'] ?? url()->current() }}">
    <meta property="og:type" content="{{ $seo['type'] ?? 'website' }}">
    <meta property="og:locale" content="{{ $seo['locale'] ?? 'tr_TR' }}">
    <meta property="og:site_name" content="{{ $settings['site_name'] ?? 'Nuttime' }}">
    @if(!empty($seo['image']))<meta property="og:image" content="{{ $seo['image'] }}">@endif
    <meta name="twitter:card" content="summary_large_image">
    @if(!empty($seo['twitter_handle']))<meta name="twitter:site" content="{{ $seo['twitter_handle'] }}">@endif
    @foreach(($seo['schemas'] ?? []) as $schema)
        <script type="application/ld+json">@json($schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)</script>
    @endforeach
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php($localizedRoute = static fn (string $name, array $parameters = []): string => app(\App\Support\LocalizedUrl::class)->route($name, null, $parameters))
@php($alternateUrls = $seo['alternates'] ?? [])
@php($currentLocale = app()->getLocale())
@php($currentLocaleConfiguration = config('nuttime.locales.'.$currentLocale))
@php($managedHeaderNavigation = app(\App\Support\CmsContentRepository::class)->menu('header-primary', app()->getLocale()))
@php($managedFooterNavigation = app(\App\Support\CmsContentRepository::class)->menu('footer-primary', app()->getLocale()))
@php($managedLegalNavigation = app(\App\Support\CmsContentRepository::class)->menu('footer-legal', app()->getLocale()))
<body x-data="{menu:false,language:false,compact:false}" @scroll.window="compact=window.scrollY>24" @keydown.escape.window="menu=false; language=false" :class="{'has-menu':menu,'is-compact':compact}">
    <a class="skip-link" href="#main-content">İçeriğe geç</a>
    <header class="masthead">
        <div class="container masthead__inner">
            <a class="wordmark" href="{{ $localizedRoute('home') }}" aria-label="Nuttime {{ __('site.nav.home') }}"><span class="wordmark__mark">n</span>nut<span>time</span><i></i></a>
            <nav class="masthead__nav" aria-label="{{ __('site.nav.home') }}">
                @if(count($managedHeaderNavigation))
                    @foreach($managedHeaderNavigation as $item)<x-managed-menu-item :item="$item" />@endforeach
                @else
                <a href="{{ $localizedRoute('home') }}">{{ __('site.nav.home') }}</a>
                <a href="{{ $localizedRoute('products') }}">{{ __('site.nav.products') }}</a>
                <a href="{{ $localizedRoute('about') }}">{{ __('site.nav.about') }}</a>
                <a href="{{ $localizedRoute('certificates') }}">{{ __('site.nav.certificates') }}</a>
                @endif
            </nav>
            <div class="masthead__actions">
                <div class="language-picker language-picker--desktop" aria-label="{{ __('site.language.choose') }}" @click.outside="language=false">
                    <button type="button" class="language-picker__trigger" @click="language=!language" :aria-expanded="language.toString()" aria-haspopup="listbox">
                        <span aria-hidden="true">{{ $currentLocaleConfiguration['flag'] }}</span><span>{{ $currentLocaleConfiguration['short'] }}</span><i aria-hidden="true">⌄</i>
                    </button>
                    <div x-cloak x-show="language" x-transition.origin.top.right class="language-picker__menu" role="listbox" aria-label="{{ __('site.language.choose') }}">
                        @foreach(config('nuttime.locales') as $locale => $configuration)
                            <form method="POST" action="{{ route('locale.preference') }}">
                                @csrf
                                <input type="hidden" name="locale" value="{{ $locale }}">
                                <input type="hidden" name="redirect_to" value="{{ $alternateUrls[$locale] ?? app(\App\Support\LocalizedUrl::class)->route('home', $locale) }}">
                                <button type="submit" class="language-picker__option {{ $currentLocale === $locale ? 'is-active' : '' }}" lang="{{ $locale }}" role="option" aria-selected="{{ $currentLocale === $locale ? 'true' : 'false' }}"><span aria-hidden="true">{{ $configuration['flag'] }}</span><span>{{ $configuration['label'] }}</span></button>
                            </form>
                        @endforeach
                    </div>
                </div>
                <a class="masthead__contact" href="{{ $localizedRoute('contact') }}">{{ __('site.nav.contact') }} <span>↗</span></a>
                <button class="menu-toggle" @click="menu=!menu" :aria-expanded="menu.toString()" aria-label="{{ __('site.actions.open_menu') }}"><span></span><span></span></button>
            </div>
        </div>
        <div x-show="menu" x-transition.opacity class="mobile-drawer" x-cloak>
            <nav aria-label="{{ __('site.nav.home') }}">
                @if(count($managedHeaderNavigation))
                    @foreach($managedHeaderNavigation as $item)<x-managed-menu-item :item="$item" />@endforeach
                @else
                <a href="{{ $localizedRoute('home') }}">{{ __('site.nav.home') }}</a>
                <a href="{{ $localizedRoute('products') }}">{{ __('site.nav.products') }}</a>
                <a href="{{ $localizedRoute('about') }}">{{ __('site.nav.about') }}</a>
                <a href="{{ $localizedRoute('certificates') }}">{{ __('site.nav.certificates') }}</a>
                <a href="{{ $localizedRoute('contact') }}">{{ __('site.nav.contact') }} ↗</a>
                @endif
            </nav>
            <div class="language-picker language-picker--mobile" aria-label="{{ __('site.language.choose') }}">
                <p>{{ __('site.language.choose') }}</p>
                <div class="language-picker__list">
                    @foreach(config('nuttime.locales') as $locale => $configuration)
                        <form method="POST" action="{{ route('locale.preference') }}">@csrf<input type="hidden" name="locale" value="{{ $locale }}"><input type="hidden" name="redirect_to" value="{{ $alternateUrls[$locale] ?? app(\App\Support\LocalizedUrl::class)->route('home', $locale) }}"><button type="submit" class="language-picker__option {{ $currentLocale === $locale ? 'is-active' : '' }}" lang="{{ $locale }}"><span aria-hidden="true">{{ $configuration['flag'] }}</span><span>{{ $configuration['label'] }}</span></button></form>
                    @endforeach
                </div>
            </div>
        </div>
    </header>
    <main id="main-content">@yield('content')</main>
    <footer class="site-footer">
        <div class="container site-footer__top">
            <div class="site-footer__brand"><a class="wordmark wordmark--light" href="{{ $localizedRoute('home') }}"><span class="wordmark__mark">n</span>nut<span>time</span><i></i></a><p>{{ $settings['footer_description'] ?? __('site.footer.description') }}</p></div>
            <a class="footer-email" href="mailto:{{ $settings['email'] ?? 'hello@nuttime.com.tr' }}">{{ $settings['email'] ?? 'hello@nuttime.com.tr' }} <span>↗</span></a>
        </div>
        <div class="container site-footer__links">
            <div><small>{{ __('site.footer.explore') }}</small>@if(count($managedFooterNavigation))@foreach($managedFooterNavigation as $item)<x-managed-menu-item :item="$item" />@endforeach @else<a href="{{ $localizedRoute('products') }}">{{ __('site.nav.products') }}</a><a href="{{ $localizedRoute('about') }}">{{ __('site.nav.about') }}</a><a href="{{ $localizedRoute('certificates') }}">{{ __('site.nav.certificates') }}</a>@endif</div>
            <div><small>{{ __('site.footer.contact') }}</small><a href="tel:{{ $settings['phone'] ?? '' }}">{{ $settings['phone'] ?? '+90 212 123 45 67' }}</a><a href="{{ $localizedRoute('contact') }}">{{ __('site.footer.reach_us') }}</a></div>
            <div><small>{{ __('site.footer.social') }}</small><div class="social-links">@if(!empty($settings['instagram']))<a href="{{ $settings['instagram'] }}" target="_blank" rel="noopener noreferrer">Instagram ↗</a>@endif @if(!empty($settings['facebook']))<a href="{{ $settings['facebook'] }}" target="_blank" rel="noopener noreferrer">Facebook ↗</a>@endif @if(!empty($settings['youtube']))<a href="{{ $settings['youtube'] }}" target="_blank" rel="noopener noreferrer">YouTube ↗</a>@endif</div></div>
        </div>
        <div class="container site-footer__bottom"><span>© {{ date('Y') }} Nuttime</span><span>@if(count($managedLegalNavigation))@foreach($managedLegalNavigation as $item)<x-managed-menu-item :item="$item" />@endforeach @else{{ __('site.footer.tagline') }}@endif</span></div>
    </footer>
</body>
</html>
