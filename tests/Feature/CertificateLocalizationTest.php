<?php

namespace Tests\Feature;

use App\Models\Certificate;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

final class CertificateLocalizationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_certificates_use_the_localized_content_and_image(): void
    {
        $certificate = Certificate::query()->create([
            'name' => 'Default certificate',
            'description' => 'Default description',
            'image' => 'media/certificates/default.jpg',
            'is_active' => true,
        ]);
        $certificate->translations()->createMany([
            ['locale' => 'tr', 'name' => 'Türkçe sertifika', 'description' => 'Türkçe açıklama', 'image' => 'media/certificates/tr.jpg'],
            ['locale' => 'en', 'name' => 'English certificate', 'description' => 'English description', 'image' => 'media/certificates/en.jpg'],
            ['locale' => 'de', 'name' => 'Deutsches Zertifikat', 'description' => 'Deutsche Beschreibung', 'image' => 'media/certificates/de.jpg'],
        ]);

        $this->get(route('site.de.certificates'))
            ->assertOk()
            ->assertSee('Deutsches Zertifikat')
            ->assertSee('Deutsche Beschreibung')
            ->assertSee('/storage/media/certificates/de.jpg', false)
            ->assertDontSee('Default certificate');
    }
}
