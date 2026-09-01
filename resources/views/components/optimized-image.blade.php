@props([
    'src',
    'alt' => '',
    'width',
    'height',
    'sizes' => '100vw',
    'loading' => 'lazy',
    'fetchpriority' => 'auto',
])

<picture>
    <source
        type="image/avif"
        srcset="{{ asset($src.'.640.avif') }} 640w, {{ asset($src.'.1200.avif') }} 1200w, {{ asset($src.'.'.$width.'.avif') }} {{ $width }}w"
        sizes="{{ $sizes }}"
    >
    <source
        type="image/webp"
        srcset="{{ asset($src.'.640.webp') }} 640w, {{ asset($src.'.1200.webp') }} 1200w, {{ asset($src.'.'.$width.'.webp') }} {{ $width }}w"
        sizes="{{ $sizes }}"
    >
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
