@props(['product'])

@php
    $groups = collect($product['packaging_details'] ?? [])
        ->filter(static fn (mixed $value): bool => filled($value))
        ->map(function (mixed $value, string $key): array {
            $parts = explode('.', $key, 2);
            $group = count($parts) === 2 ? $parts[0] : 'general';
            $field = count($parts) === 2 ? $parts[1] : $parts[0];

            return ['group' => $group, 'field' => $field, 'value' => $value];
        })
        ->groupBy('group');
    $groupLabels = __('site.product.packaging_groups');
    $fieldLabels = __('site.product.packaging_fields');
@endphp

@if($groups->isNotEmpty())
    <section class="product-packaging">
        <div class="container">
            <header class="product-packaging__header">
                <p class="kicker">{{ __('site.product.packaging_kicker') }}</p>
                <h2>{{ __('site.product.packaging') }}</h2>
                <p>{{ __('site.product.packaging_copy') }}</p>
            </header>
            <div class="product-packaging__groups">
                @foreach($groups as $group => $items)
                    <article class="product-packaging__group product-packaging__group--{{ $group }}">
                        <h3>{{ $groupLabels[$group] ?? $group }}</h3>
                        <dl>
                            @foreach($items as $item)
                                <div>
                                    <dt>{{ $fieldLabels[$item['field']] ?? $item['field'] }}</dt>
                                    <dd>{{ $item['value'] }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
