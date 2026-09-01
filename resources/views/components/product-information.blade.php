@props(['product'])

@php
    $ingredients = $product['ingredients'] ?? null;
    $allergenInformation = $product['allergen_information'] ?? null;
    $nutritionFacts = collect($product['nutrition_facts'] ?? [])->filter(static fn (mixed $value): bool => filled($value));
    $nutritionFields = __('site.product.nutrition_fields');
@endphp

@if(filled($ingredients) || filled($allergenInformation) || $nutritionFacts->isNotEmpty())
    <section class="product-information">
        <div class="container product-information__grid">
            @if(filled($ingredients) || filled($allergenInformation))
                <article class="product-information__ingredients">
                    <p class="kicker">{{ __('site.product.ingredients_kicker') }}</p>
                    <h2>{{ __('site.product.ingredients') }}</h2>
                    @if(filled($ingredients))
                        <p>{{ $ingredients }}</p>
                    @endif
                    @if(filled($allergenInformation))
                        <p class="product-information__allergens"><strong>{{ __('site.product.allergens') }}</strong>{{ $allergenInformation }}</p>
                    @endif
                </article>
            @endif

            @if($nutritionFacts->isNotEmpty())
                <article class="product-information__nutrition">
                    <p class="kicker">{{ __('site.product.nutrition_kicker') }}</p>
                    <h2>{{ __('site.product.nutrition') }}</h2>
                    <dl>
                        @foreach($nutritionFacts as $field => $value)
                            <div>
                                <dt>{{ $nutritionFields[$field] ?? $field }}</dt>
                                <dd>{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </article>
            @endif
        </div>
    </section>
@endif
