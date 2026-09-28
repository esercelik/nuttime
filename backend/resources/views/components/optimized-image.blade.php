@props([
    'src',
    'alt' => '',
    'width',
    'height',
    'sizes' => '100vw',
    'loading' => 'lazy',
    'fetchpriority' => 'auto',
])

@php
    $responsiveWidths = collect([640, 1200, $width])->unique()->filter();
    $sourceSet = static fn (string $format): string => $responsiveWidths
        ->filter(fn (int $candidate): bool => file_exists(public_path($src.'.'.$candidate.'.'.$format)))
        ->map(fn (int $candidate): string => asset($src.'.'.$candidate.'.'.$format).' '.$candidate.'w')
        ->implode(', ');
@endphp

<picture>
    @if($avifSources = $sourceSet('avif'))
        <source type="image/avif" srcset="{{ $avifSources }}" sizes="{{ $sizes }}">
    @endif
    @if($webpSources = $sourceSet('webp'))
        <source type="image/webp" srcset="{{ $webpSources }}" sizes="{{ $sizes }}">
    @endif
    <img
        {{ $attributes }}
        src="{{ asset($src) }}"
        alt="{{ $alt }}"
        width="{{ $width }}"
        height="{{ $height }}"
        sizes="{{ $sizes }}"
        loading="{{ $loading }}"
        fetchpriority="{{ $fetchpriority }}"
        decoding="async"
    >
</picture>
