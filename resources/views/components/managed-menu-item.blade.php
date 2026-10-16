@props(['item'])

@php
    $certificatePaths = collect(config('nuttime.locales'))->map(fn (array $configuration, string $locale): string => '/'.$locale.'/'.$configuration['paths']['certificates']);
    $isCertificateLink = $certificatePaths->contains(rtrim(parse_url($item['url'], PHP_URL_PATH) ?? '', '/'));
@endphp

<span class="managed-menu-item">
    <a href="{{ $isCertificateLink ? rtrim($item['url'], '/').'/' : $item['url'] }}" @if($item['new_tab']) target="_blank" rel="noopener noreferrer" @endif>{{ $item['label'] }}</a>
    @if(!empty($item['children']))
        <span class="managed-menu-item__children">
            @foreach($item['children'] as $child)
                <x-managed-menu-item :item="$child" />
            @endforeach
        </span>
    @endif
</span>
