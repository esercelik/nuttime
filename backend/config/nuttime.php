<?php

return [
    'locales' => [
        'tr' => ['label' => 'Türkçe', 'short' => 'TR', 'flag' => '🇹🇷', 'html' => 'tr-TR', 'og' => 'tr_TR', 'direction' => 'ltr', 'paths' => ['products' => 'urunler', 'product' => 'urunler/{slug}', 'category' => 'kategori/{slug}', 'about' => 'hakkimizda', 'certificates' => 'sertifikalar', 'contact' => 'iletisim', 'contents' => 'icerikler', 'content' => 'icerikler/{slug}']],
        'en' => ['label' => 'English', 'short' => 'EN', 'flag' => '🇬🇧', 'html' => 'en', 'og' => 'en_US', 'direction' => 'ltr', 'paths' => ['products' => 'products', 'product' => 'products/{slug}', 'category' => 'categories/{slug}', 'about' => 'about-us', 'certificates' => 'certificates', 'contact' => 'contact', 'contents' => 'stories', 'content' => 'stories/{slug}']],
        'de' => ['label' => 'Deutsch', 'short' => 'DE', 'flag' => '🇩🇪', 'html' => 'de-DE', 'og' => 'de_DE', 'direction' => 'ltr', 'paths' => ['products' => 'produkte', 'product' => 'produkte/{slug}', 'category' => 'kategorien/{slug}', 'about' => 'uber-uns', 'certificates' => 'zertifikate', 'contact' => 'kontakt', 'contents' => 'geschichten', 'content' => 'geschichten/{slug}']],
        'fr' => ['label' => 'Français', 'short' => 'FR', 'flag' => '🇫🇷', 'html' => 'fr-FR', 'og' => 'fr_FR', 'direction' => 'ltr', 'paths' => ['products' => 'produits', 'product' => 'produits/{slug}', 'category' => 'categories/{slug}', 'about' => 'a-propos', 'certificates' => 'certificats', 'contact' => 'contact', 'contents' => 'histoires', 'content' => 'histoires/{slug}']],
        'es' => ['label' => 'Español', 'short' => 'ES', 'flag' => '🇪🇸', 'html' => 'es-ES', 'og' => 'es_ES', 'direction' => 'ltr', 'paths' => ['products' => 'productos', 'product' => 'productos/{slug}', 'category' => 'categorias/{slug}', 'about' => 'sobre-nosotros', 'certificates' => 'certificados', 'contact' => 'contacto', 'contents' => 'historias', 'content' => 'historias/{slug}']],
        'it' => ['label' => 'Italiano', 'short' => 'IT', 'flag' => '🇮🇹', 'html' => 'it-IT', 'og' => 'it_IT', 'direction' => 'ltr', 'paths' => ['products' => 'prodotti', 'product' => 'prodotti/{slug}', 'category' => 'categorie/{slug}', 'about' => 'chi-siamo', 'certificates' => 'certificati', 'contact' => 'contatti', 'contents' => 'storie', 'content' => 'storie/{slug}']],
        'ru' => ['label' => 'Русский', 'short' => 'RU', 'flag' => '🇷🇺', 'html' => 'ru-RU', 'og' => 'ru_RU', 'direction' => 'ltr', 'paths' => ['products' => 'products', 'product' => 'products/{slug}', 'category' => 'categories/{slug}', 'about' => 'about', 'certificates' => 'certificates', 'contact' => 'contact', 'contents' => 'stories', 'content' => 'stories/{slug}']],
        'ar' => ['label' => 'العربية', 'short' => 'AR', 'flag' => '🇸🇦', 'html' => 'ar', 'og' => 'ar_AR', 'direction' => 'rtl', 'paths' => ['products' => 'products', 'product' => 'products/{slug}', 'category' => 'categories/{slug}', 'about' => 'about', 'certificates' => 'certificates', 'contact' => 'contact', 'contents' => 'stories', 'content' => 'stories/{slug}']],
        'zh' => ['label' => '中文', 'short' => 'ZH', 'flag' => '🇨🇳', 'html' => 'zh-CN', 'og' => 'zh_CN', 'direction' => 'ltr', 'paths' => ['products' => 'products', 'product' => 'products/{slug}', 'category' => 'categories/{slug}', 'about' => 'about', 'certificates' => 'certificates', 'contact' => 'contact', 'contents' => 'stories', 'content' => 'stories/{slug}']],
        'pt' => ['label' => 'Português', 'short' => 'PT', 'flag' => '🇵🇹', 'html' => 'pt-PT', 'og' => 'pt_PT', 'direction' => 'ltr', 'paths' => ['products' => 'produtos', 'product' => 'produtos/{slug}', 'category' => 'categorias/{slug}', 'about' => 'sobre-nos', 'certificates' => 'certificados', 'contact' => 'contacto', 'contents' => 'historias', 'content' => 'historias/{slug}']],
    ],
    'default_locale' => 'tr',
    'fallback_locale' => 'tr',
    'preference_cookie' => 'nuttime_locale',
    'country_headers' => ['CF-IPCountry', 'X-Vercel-IP-Country', 'X-Country-Code'],
    'timezone' => 'Europe/Istanbul',
    'brand' => ['name' => 'Nuttime', 'colors' => ['ink' => '#171714', 'gold' => '#D8B768', 'cream' => '#F7F3E8', 'text' => '#25231F']],
];
