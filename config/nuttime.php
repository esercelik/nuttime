<?php

return [
    'locales' => [
        'tr' => ['label' => 'Türkçe', 'html' => 'tr-TR', 'og' => 'tr_TR', 'paths' => ['products' => 'urunler', 'product' => 'urunler/{slug}', 'category' => 'kategori/{slug}', 'about' => 'hakkimizda', 'certificates' => 'sertifikalar', 'contact' => 'iletisim', 'contents' => 'icerikler', 'content' => 'icerikler/{slug}']],
        'en' => ['label' => 'English', 'html' => 'en', 'og' => 'en_US', 'paths' => ['products' => 'products', 'product' => 'products/{slug}', 'category' => 'categories/{slug}', 'about' => 'about-us', 'certificates' => 'certificates', 'contact' => 'contact', 'contents' => 'stories', 'content' => 'stories/{slug}']],
        'de' => ['label' => 'Deutsch', 'html' => 'de', 'og' => 'de_DE', 'paths' => ['products' => 'produkte', 'product' => 'produkte/{slug}', 'category' => 'kategorien/{slug}', 'about' => 'uber-uns', 'certificates' => 'zertifikate', 'contact' => 'kontakt', 'contents' => 'geschichten', 'content' => 'geschichten/{slug}']],
    ],
    'default_locale' => 'tr',
    'fallback_locale' => 'en',
    'preference_cookie' => 'nuttime_locale',
    'country_headers' => ['CF-IPCountry', 'X-Vercel-IP-Country', 'X-Country-Code'],
    'timezone' => 'Europe/Istanbul',
    'brand' => ['name' => 'Nuttime', 'colors' => ['ink' => '#171714', 'gold' => '#D8B768', 'cream' => '#F7F3E8', 'text' => '#25231F']],
];
