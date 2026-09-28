@extends('layouts.app')
@section('content')
@if($page === 'certificates')<x-page-hero class="page-hero--certificates" :kicker="__('site.static.certificates_kicker')" :title="__('site.static.certificates_title')" :copy="__('site.static.certificates_copy')" rich-title /><section class="certificate-archive"><div class="container"><div class="certificate-archive__intro"><span>nt</span><p>{{ __('site.static.certificates_copy') }}</p></div>@forelse($certificates as $index => $certificate)<article class="certificate-archive__item"><span class="certificate-archive__number">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>@if($certificate['image'])<x-certificate-preview :src="$certificate['image']" :alt="$certificate['name']" />@else<div class="quality-rail__mark">nt</div>@endif<div><p class="kicker">{{ __('site.static.certificate') }}</p><h2><x-safe-rich-text :value="$certificate['name']" /></h2>@if($certificate['description'])<p><x-safe-rich-text :value="$certificate['description']" /></p>@endif</div>@if($certificate['document'])<a class="arrow-link" href="{{ $certificate['document'] }}" target="_blank" rel="noopener noreferrer">{{ __('site.actions.view_document') }} <span>↗</span></a>@endif</article>@empty<p class="empty-state">{{ __('site.static.certificates_empty') }}</p>@endforelse</div></section>
@else
    <x-page-hero class="page-hero--about" :kicker="__('site.static.about_kicker')" :title="__('site.static.about_title')" :copy="__('site.static.about_copy')" rich-title />

    <section class="about-editorial">
        <div class="container">
            <div class="about-editorial__content">
                <article class="about-editorial__copy about-editorial__copy--who">
                    <p class="kicker">{{ __('site.static.who_kicker') }}</p>
                    <h2>{{ __('site.static.who_title') }}</h2>
                    <div class="about-editorial__prose">
                        <p>{{ __('site.static.who_copy_one') }}</p>
                        <p>{{ __('site.static.who_copy_two') }}</p>
                    </div>
                </article>

                <article class="about-editorial__copy about-editorial__copy--mission">
                    <p class="kicker">{{ __('site.static.mission_kicker') }}</p>
                    <h2>{{ __('site.static.mission_title') }}</h2>
                    <div class="about-editorial__prose">
                        <p>{{ __('site.static.mission_copy_one') }}</p>
                        <p>{{ __('site.static.mission_copy_two') }}</p>
                        <p>{{ __('site.static.mission_copy_three') }}</p>
                        <p>{{ __('site.static.mission_donation_before') }} <strong>{{ __('site.static.mission_donation_name') }}</strong> {{ __('site.static.mission_donation_after') }}</p>
                    </div>
                    <p class="about-editorial__quote">{{ __('site.static.mission_quote') }}</p>
                </article>
            </div>

        </div>
    </section>

    <x-final-cta />
@endif
@endsection
