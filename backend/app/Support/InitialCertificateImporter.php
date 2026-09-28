<?php

namespace App\Support;

use App\Models\Certificate;
use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

final class InitialCertificateImporter
{
    public function __construct(private MediaUploadMetadata $mediaMetadata) {}

    /** @return array{created: int, translated: int, media: int, copied: int} */
    public function import(): array
    {
        return DB::transaction(function (): array {
            $created = $translated = $media = $copied = 0;
            $storage = Storage::disk('public');

            foreach ($this->certificates() as $definition) {
                $path = 'media/certificates/'.$definition['filename'];

                if (! $storage->exists($path)) {
                    $contents = File::get(public_path('images/nuttime/certificates/'.$definition['filename']));

                    if (! $storage->put($path, $contents)) {
                        throw new RuntimeException('Unable to store certificate: '.$path);
                    }

                    $copied++;
                }

                if (! Media::query()->where('disk', 'public')->where('path', $path)->exists()) {
                    Media::query()->create([
                        'disk' => 'public',
                        'path' => $path,
                        'original_name' => $definition['filename'],
                        'folder' => 'certificates',
                        'title' => $definition['name'],
                        'alt_texts' => array_map(fn (array $translation): string => $translation['name'], $definition['translations']),
                        ...$this->mediaMetadata->forStoredFile('public', $path),
                    ]);
                    $media++;
                }

                $certificate = Certificate::query()->firstOrCreate(['image' => $path], [
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'issuer' => 'TÜV Rheinland Cert GmbH',
                    'certificate_number' => '01 183 2015879',
                    'issued_at' => $definition['issued_at'],
                    'expires_at' => $definition['expires_at'],
                    'sort_order' => $definition['sort_order'],
                    'is_active' => true,
                ]);
                $created += $certificate->wasRecentlyCreated ? 1 : 0;

                foreach ($definition['translations'] as $locale => $translation) {
                    $record = $certificate->translations()->firstOrCreate(['locale' => $locale], $translation);
                    $translated += $record->wasRecentlyCreated ? 1 : 0;
                }
            }

            return compact('created', 'translated', 'media', 'copied');
        });
    }

    /**
     * @return array<int, array{filename: string, name: string, description: string, issued_at: string, expires_at: string, sort_order: int, translations: array<string, array{name: string, description: string}>}>
     */
    private function certificates(): array
    {
        $currentDescriptions = [
            'tr' => 'BRC Küresel Gıda Güvenliği Standardı, Sürüm 9 (Ağustos 2022). Geçerlilik: 27 Ekim 2026.',
            'en' => 'Global Standard Food Safety, Issue 9 (August 2022). Valid until 27 October 2026.',
            'de' => 'Globaler Standard Lebensmittelsicherheit, Version 9 (August 2022). Gültig bis 27. Oktober 2026.',
        ];

        return [
            [
                'filename' => 'brcgs-food-safety-01-183-2015879-en-e1d5041b914a.jpg',
                'name' => 'BRCGS Food Safety Certificate (English document)',
                'description' => 'Global Standard Food Safety, Issue 9 (August 2022).',
                'issued_at' => '2025-10-23',
                'expires_at' => '2026-10-27',
                'sort_order' => 10,
                'translations' => [
                    'tr' => ['name' => 'BRCGS Gıda Güvenliği Sertifikası (İngilizce belge)', 'description' => $currentDescriptions['tr']],
                    'en' => ['name' => 'BRCGS Food Safety Certificate (English document)', 'description' => $currentDescriptions['en']],
                    'de' => ['name' => 'BRCGS Zertifikat Lebensmittelsicherheit (Englisches Dokument)', 'description' => $currentDescriptions['de']],
                ],
            ],
            [
                'filename' => 'brcgs-food-safety-01-183-2015879-de-d6c827cf19bd.jpg',
                'name' => 'BRCGS Zertifikat Lebensmittelsicherheit (German document)',
                'description' => 'Globaler Standard Lebensmittelsicherheit, Version 9 (August 2022).',
                'issued_at' => '2025-10-23',
                'expires_at' => '2026-10-27',
                'sort_order' => 20,
                'translations' => [
                    'tr' => ['name' => 'BRCGS Gıda Güvenliği Sertifikası (Almanca belge)', 'description' => $currentDescriptions['tr']],
                    'en' => ['name' => 'BRCGS Food Safety Certificate (German document)', 'description' => $currentDescriptions['en']],
                    'de' => ['name' => 'BRCGS Zertifikat Lebensmittelsicherheit (Deutsches Dokument)', 'description' => $currentDescriptions['de']],
                ],
            ],
            [
                'filename' => 'brcgs-food-safety-01-183-2015879-tr-ca2924e5ddfe.jpg',
                'name' => 'BRCGS Food Safety Certificate (Issue 8, archived)',
                'description' => 'BRC Global Standard for Food Safety, Issue 8. This certificate expired on 27 October 2024.',
                'issued_at' => '2023-08-15',
                'expires_at' => '2024-10-27',
                'sort_order' => 30,
                'translations' => [
                    'tr' => ['name' => 'BRCGS Gıda Güvenliği Sertifikası (Sürüm 8, arşiv)', 'description' => 'Bu sertifikanın geçerliliği 27 Ekim 2024 tarihinde sona ermiştir.'],
                    'en' => ['name' => 'BRCGS Food Safety Certificate (Issue 8, archived)', 'description' => 'This certificate expired on 27 October 2024.'],
                    'de' => ['name' => 'BRCGS Zertifikat Lebensmittelsicherheit (Version 8, Archiv)', 'description' => 'Dieses Zertifikat ist am 27. Oktober 2024 abgelaufen.'],
                ],
            ],
        ];
    }
}
