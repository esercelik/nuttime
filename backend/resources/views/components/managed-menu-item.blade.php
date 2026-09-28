@props(['item'])

@php
    $certificatePaths = collect(config('nuttime.locales'))->map(fn (array $configuration, string $locale): string => '/'.$locale.'/'.$configuration['paths']['certificates'])->push('/sertifikalarimiz');
    $isCertificateLink = $certificatePaths->contains(rtrim(parse_url($item['url'], PHP_URL_PATH) ?? '', '/'));
@endphp

@if(!$isCertificateLink)

<span class="managed-menu-item">
    <a href="{{ $item['url'] }}" @if($item['new_tab']) target="_blank" rel="noopener noreferrer" @endif>{{ $item['label'] }}</a>
    @if(!empty($item['children']))
        <span class="managed-menu-item__children">
            @foreach($item['children'] as $child)
                <x-managed-menu-item :item="$child" />
            @endforeach
        </span>
    @endif
</span>

@endif
