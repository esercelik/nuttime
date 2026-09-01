<?php

namespace App\Support;

final class NuttimeProductMedia
{
    /**
     * @return array<int, array{source: string, destination: string}>
     */
    public function imports(): array
    {
        return [
            ['source' => 'findik_1.b8a5dbf-scaled.jpg', 'destination' => 'images/nuttime/products/hazelnut/jar.jpg'],
            ['source' => 'findik_acik.9ef2d99-scaled.jpg', 'destination' => 'images/nuttime/products/hazelnut/open-jar.jpg'],
            ['source' => 'findik_ekmek_1.a64bb25-scaled.jpg', 'destination' => 'images/nuttime/products/hazelnut/bread.jpg'],
            ['source' => 'findik_detay.8768d06.jpg', 'destination' => 'images/nuttime/products/hazelnut/detail.jpg'],
            ['source' => 'a_fistigi_1.6385035-scaled.jpg', 'destination' => 'images/nuttime/products/pistachio/jar.jpg'],
            ['source' => 'a_fistigi_2.35747b4-scaled.jpg', 'destination' => 'images/nuttime/products/pistachio/open-jar.jpg'],
            ['source' => 'a_fistigi_3.8ab30cd.jpg', 'destination' => 'images/nuttime/products/pistachio/detail.jpg'],
            ['source' => 'a_fistigi_kasik.2dee820-scaled.jpg', 'destination' => 'images/nuttime/products/pistachio/spoon.jpg'],
            ['source' => 'badem_1.9fcfb8c-scaled.jpg', 'destination' => 'images/nuttime/products/almond/jar.jpg'],
            ['source' => 'badem_2.d816ca4-scaled.jpg', 'destination' => 'images/nuttime/products/almond/open-jar.jpg'],
            ['source' => 'badem_3.c6c76b4-scaled.jpg', 'destination' => 'images/nuttime/products/almond/detail.jpg'],
            ['source' => 'badem_tahta_kasik.7428287-scaled.jpg', 'destination' => 'images/nuttime/products/almond/spoon.jpg'],
            ['source' => 'fistik_3.34eba41-scaled.jpg', 'destination' => 'images/nuttime/products/peanut/jar.jpg'],
            ['source' => 'fistik_4.21f67e1-scaled.jpg', 'destination' => 'images/nuttime/products/peanut/open-jar.jpg'],
            ['source' => 'fistik_detay.8f91e7f.jpg', 'destination' => 'images/nuttime/products/peanut/detail.jpg'],
            ['source' => 'fistik_1.65fa58a.jpg', 'destination' => 'images/nuttime/products/peanut/lifestyle.jpg'],
            ['source' => 'h_cevizi_kapali_1.f235566-scaled.jpg', 'destination' => 'images/nuttime/products/coconut/jar.jpg'],
            ['source' => 'h_cevizi_kapali_2.bacd09b-scaled.jpg', 'destination' => 'images/nuttime/products/coconut/open-jar.jpg'],
            ['source' => 'h_cevizi_kasik.46965fc-scaled.jpg', 'destination' => 'images/nuttime/products/coconut/spoon.jpg'],
            ['source' => 'h_cevizi_tahta_kasik.a1279bd-scaled.jpg', 'destination' => 'images/nuttime/products/coconut/wooden-spoon.jpg'],
        ];
    }

    /** @return array<int, string> */
    public function galleryPaths(string $productId): array
    {
        $directory = match ($productId) {
            'findik-kremasi' => 'hazelnut',
            'antep-fistikli-kremasi' => 'pistachio',
            'badem-unu' => 'almond',
            'yer-fistigi-ezmesi' => 'peanut',
            'hindistan-cevizi-ezmesi' => 'coconut',
            default => null,
        };

        if ($directory === null) {
            return [];
        }

        $distinctCoverPaths = match ($productId) {
            'antep-fistikli-kremasi' => ['images/nuttime/pistachio-butter.jpg'],
            'yer-fistigi-ezmesi' => ['images/nuttime/peanut-butter.jpg'],
            default => [],
        };

        return collect($distinctCoverPaths)
            ->concat(
                collect($this->imports())
                    ->filter(fn (array $item): bool => str_starts_with($item['destination'], "images/nuttime/products/{$directory}/"))
                    ->pluck('destination'),
            )
            ->filter(fn (string $path): bool => file_exists(public_path($path)))
            ->values()
            ->all();
    }
}
