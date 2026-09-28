<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Media;
use App\Models\Product;
use App\Support\InitialCertificateImporter;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class InitialCertificateImportTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_bundled_certificates_are_imported_into_the_media_library_and_product_page(): void
    {
        $storage = Storage::fake('public');
        $product = Product::factory()->create();

        $this->artisan('certificates:import-initial')
            ->expectsOutput('Certificates created: 3, translations created: 9, media created: 3, files copied: 3')
            ->assertSuccessful();

        $this->assertDatabaseCount('certificates', 3);
        $this->assertDatabaseCount('certificate_translations', 9);
        $this->assertDatabaseCount('media', 3);

        foreach (Media::all() as $media) {
            $storage->assertExists($media->path);
            $this->assertSame(file_get_contents(public_path('images/nuttime/certificates/'.basename($media->path))), $storage->get($media->path));
            $this->assertSame('image/jpeg', $media->mime_type);
        }

        $this->get(route('site.tr.product', ['slug' => $product->slug]))
            ->assertSee('BRCGS Gıda Güvenliği Sertifikası (İngilizce belge)')
            ->assertSee('BRCGS Gıda Güvenliği Sertifikası (Almanca belge)')
            ->assertSee('BRCGS Gıda Güvenliği Sertifikası (Sürüm 8, arşiv)')
            ->assertSee('27 Ekim 2024 tarihinde sona ermiştir.')
            ->assertSee('href="'.asset('storage/media/certificates/brcgs-food-safety-01-183-2015879-en-e1d5041b914a.jpg').'"', false);
    }

    public function test_reimport_preserves_managed_content_and_restores_only_missing_files_and_translations(): void
    {
        $storage = Storage::fake('public');
        $importer = app(InitialCertificateImporter::class);
        $importer->import();
        $certificate = Certificate::query()->where('sort_order', 10)->firstOrFail();
        $certificate->update(['name' => 'Managed certificate', 'is_active' => false, 'description' => 'Managed description']);
        $certificate->translations()->where('locale', 'tr')->firstOrFail()->update(['name' => 'Yönetilen belge', 'image' => 'media/custom.jpg']);
        $certificate->translations()->where('locale', 'de')->delete();
        $media = Media::query()->where('path', $certificate->image)->firstOrFail();
        $media->update(['title' => 'Managed media']);
        $storage->put($certificate->image, 'Existing file contents');
        $missingPath = Certificate::query()->where('sort_order', 20)->value('image');
        $storage->delete($missingPath);

        $result = $importer->import();

        $this->assertSame(['created' => 0, 'translated' => 1, 'media' => 0, 'copied' => 1], $result);
        $this->assertDatabaseCount('certificates', 3);
        $this->assertDatabaseCount('certificate_translations', 9);
        $this->assertDatabaseCount('media', 3);
        $this->assertDatabaseHas('certificates', ['id' => $certificate->id, 'name' => 'Managed certificate', 'is_active' => false, 'description' => 'Managed description']);
        $this->assertDatabaseHas('certificate_translations', ['certificate_id' => $certificate->id, 'locale' => 'tr', 'name' => 'Yönetilen belge', 'image' => 'media/custom.jpg']);
        $this->assertSame('Managed media', $media->fresh()->title);
        $this->assertSame('Existing file contents', $storage->get($certificate->image));
        $this->assertSame(file_get_contents(public_path('images/nuttime/certificates/'.basename($missingPath))), $storage->get($missingPath));
    }
}
