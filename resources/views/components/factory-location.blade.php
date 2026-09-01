@props(['factory', 'variant' => 'homepage', 'settings' => []])

@php($isContactVariant = $variant === 'contact')

@if($factory['enabled'] ?? false)
<section class="factory-location factory-location--{{ $isContactVariant ? 'contact' : 'homepage' }}">
    <div class="container factory-grid">
        <div class="factory-copy">
            <p class="kicker">{{ __('site.factory.kicker') }}</p>
            <h2>{{ $factory['name'] ?: __('site.factory.fallback_name') }}</h2>

            @if($isContactVariant)
                <dl class="factory-details">
                    <div>
                        <dt>{{ __('site.factory.address_label') }}</dt>
                        <dd>{{ $factory['address'] }}</dd>
                    </div>
                    @if(filled($settings['phone'] ?? null))
                        <div>
                            <dt>{{ __('site.factory.phone_label') }}</dt>
                            <dd><a href="tel:{{ $settings['phone'] }}">{{ $settings['phone'] }}</a></dd>
                        </div>
                    @endif
                    @if(filled($settings['email'] ?? null))
                        <div>
                            <dt>{{ __('site.factory.email_label') }}</dt>
                            <dd><a href="mailto:{{ $settings['email'] }}">{{ $settings['email'] }}</a></dd>
                        </div>
                    @endif
                </dl>
            @else
                <p>{{ $factory['address'] }}</p>
            @endif

            @if($factory['url'])
                <a class="button" href="{{ $factory['url'] }}" target="_blank" rel="noopener noreferrer">{{ __('site.actions.get_directions') }} <span>↗</span></a>
            @endif
        </div>

        <div class="map-frame{{ $factory['embed_url'] ? '' : ' map-frame--fallback' }}">
            @if($factory['embed_url'])
                <iframe src="{{ $factory['embed_url'] }}" title="{{ $factory['name'] ?: __('site.factory.map_title') }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
            @else
                <div class="map-fallback">
                    <span aria-hidden="true">⌖</span>
                    <p>{!! __('site.factory.map_copy') !!}</p>
                    @if($factory['url'])
                        <a class="arrow-link" href="{{ $factory['url'] }}" target="_blank" rel="noopener noreferrer">{{ __('site.actions.view_map') }} <span>↗</span></a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
@endif
