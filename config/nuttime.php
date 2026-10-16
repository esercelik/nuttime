<?php

return [
    'locales' => [
        'fr' => ['label' => 'Français', 'short' => 'FR', 'flag' => '🇫🇷', 'html' => 'fr-FR', 'og' => 'fr_FR', 'direction' => 'ltr', 'paths' => ['products' => 'produits', 'product' => 'produits/{slug}', 'category' => 'categories/{slug}', 'about' => 'a-propos', 'certificates' => 'certificats', 'contact' => 'contact', 'contents' => 'histoires', 'content' => 'histoires/{slug}']],
        'de' => ['label' => 'Deutsch', 'short' => 'DE', 'flag' => '🇩🇪', 'html' => 'de-DE', 'og' => 'de_DE', 'direction' => 'ltr', 'paths' => ['products' => 'produkte', 'product' => 'produkte/{slug}', 'category' => 'kategorien/{slug}', 'about' => 'uber-uns', 'certificates' => 'zertifikate', 'contact' => 'kontakt', 'contents' => 'geschichten', 'content' => 'geschichten/{slug}']],
        'en' => ['label' => 'English', 'short' => 'EN', 'flag' => '🇬🇧', 'html' => 'en', 'og' => 'en_US', 'direction' => 'ltr', 'paths' => ['products' => 'products', 'product' => 'products/{slug}', 'category' => 'categories/{slug}', 'about' => 'about-us', 'certificates' => 'certificates', 'contact' => 'contact', 'contents' => 'stories', 'content' => 'stories/{slug}']],
    ],
    'default_locale' => 'fr',
    'fallback_locale' => 'fr',
    'preference_cookie' => 'nuttime_locale',
    'country_headers' => ['CF-IPCountry', 'X-Vercel-IP-Country', 'X-Country-Code'],
    'timezone' => 'Europe/Istanbul',
    'brand' => ['name' => 'Nuttime', 'colors' => ['ink' => '#171714', 'gold' => '#D8B768', 'cream' => '#F7F3E8', 'text' => '#25231F']],
];
