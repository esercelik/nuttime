@props(['src', 'alt', 'width' => 300, 'height' => 190])

<div class="certificate-preview" x-data="{ open: false, opener: null }" @keydown.escape.window="if (open) { open = false; document.body.classList.remove('has-lightbox'); $nextTick(() => opener?.focus()); }">
    <button type="button" class="certificate-preview__trigger" @click="opener = $el; open = true; document.body.classList.add('has-lightbox'); $nextTick(() => $refs.close.focus())" aria-haspopup="dialog">
        <img src="{{ $src }}" alt="{{ $alt }}" width="{{ $width }}" height="{{ $height }}" loading="lazy" decoding="async">
    </button>

    <div x-cloak x-show="open" x-transition.opacity class="certificate-lightbox" role="dialog" aria-modal="true" aria-label="{{ $alt }}" @click.self="open = false; document.body.classList.remove('has-lightbox'); $nextTick(() => opener?.focus())">
        <div class="certificate-lightbox__content">
            <button type="button" class="certificate-lightbox__close" x-ref="close" @click="open = false; document.body.classList.remove('has-lightbox'); $nextTick(() => opener?.focus())" aria-label="{{ __('site.actions.close') }}">×</button>
            <img src="{{ $src }}" alt="{{ $alt }}" width="{{ $width }}" height="{{ $height }}">
        </div>
    </div>
</div>
