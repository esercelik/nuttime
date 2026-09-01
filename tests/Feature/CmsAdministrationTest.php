<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Media;
use App\Models\Menu;
use App\Models\PageSection;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\Slider;
use App\Models\User;
use App\Support\CmsContentRepository;
use App\Support\CmsInitialContentSeeder;
use App\Support\MediaUploadMetadata;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class CmsAdministrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_slider_settings_control_the_rendered_carousel(): void
    {
        $slider = Slider::query()->create([
            'key' => 'home',
            'name' => 'Yönetilen slider',
            'status' => 'published',
            'is_active' => true,
            'settings' => ['autoplay_ms' => 9100, 'loop' => false, 'show_arrows' => false, 'show_counter' => false, 'show_progress' => false, 'swipe' => false],
        ]);
        $item = $slider->items()->create(['status' => 'published', 'is_active' => true, 'sort_order' => 1]);
        $item->translations()->create(['locale' => 'tr', 'title' => 'Yönetilen slayt']);
        app(CmsContentRepository::class)->forgetHomeSlider();

        $this->get(route('site.tr.home'))
            ->assertSee('data-autoplay="9100"', false)
            ->assertSee('data-loop="false"', false)
            ->assertSee('data-swipe="false"', false)
            ->assertDontSee('data-product-hero-previous', false)
            ->assertDontSee('data-product-hero-current', false)
            ->assertDontSee('data-product-hero-progress', false);
    }

    public function test_published_home_sections_render_in_order_while_drafts_are_ignored(): void
    {
        $second = $this->createSection('second', 20, 'İkinci yönetilen bölüm');
        $first = $this->createSection('first', 10, 'İlk yönetilen bölüm');
        $this->createSection('draft', 1, 'Taslak bölüm', 'draft');
        app(CmsContentRepository::class)->forgetHomeSections();

        $response = $this->get(route('site.tr.home'));

        $response
            ->assertSee('İlk yönetilen bölüm')
            ->assertSee('İkinci yönetilen bölüm')
            ->assertDontSee('Taslak bölüm')
            ->assertDontSee('id="home-banners"', false);
        $this->assertLessThan(strpos($response->getContent(), $second->translationFor('tr')->title), strpos($response->getContent(), $first->translationFor('tr')->title));
    }

    public function test_menus_render_translations_nested_items_and_ignore_invalid_routes(): void
    {
        $menu = Menu::query()->create(['key' => 'header-primary', 'name' => 'Header', 'location' => 'header', 'is_active' => true]);
        $parent = $menu->items()->create(['link_type' => 'internal', 'route_name' => 'products', 'is_active' => true, 'sort_order' => 1]);
        $parent->translations()->create(['locale' => 'en', 'label' => 'Shop']);
        $child = $parent->children()->create(['menu_id' => $menu->id, 'link_type' => 'internal', 'route_name' => 'contact', 'is_active' => true, 'sort_order' => 1]);
        $child->translations()->create(['locale' => 'en', 'label' => 'Talk to us']);
        $invalid = $menu->items()->create(['link_type' => 'internal', 'route_name' => 'not-a-route', 'is_active' => true, 'sort_order' => 2]);
        $invalid->translations()->create(['locale' => 'en', 'label' => 'Broken']);
        app(CmsContentRepository::class)->forgetMenus();

        $this->get(route('site.en.home'))
            ->assertSee('Shop')
            ->assertSee('Talk to us')
            ->assertDontSee('Broken')
            ->assertSee(route('site.en.products'), false)
            ->assertSee(route('site.en.contact'), false);
    }

    public function test_media_metadata_comes_from_file_contents_and_duplicate_uploads_are_detected(): void
    {
        Storage::fake('public');
        $path = UploadedFile::fake()->image('transparent.png', 32, 16)->store('media', 'public');

        $metadata = app(MediaUploadMetadata::class)->forStoredFile('public', $path);
        $media = Media::query()->create(['disk' => 'public', 'path' => $path, 'original_name' => 'transparent.png', ...$metadata]);

        $this->assertSame('image/png', $media->mime_type);
        $this->assertSame(32, $media->width);
        $this->assertSame(16, $media->height);
        $this->assertSame(64, strlen($media->checksum));
        $this->assertTrue(Media::query()->where('checksum', $metadata['checksum'])->exists());
    }

    public function test_referenced_media_cannot_be_deleted_and_unused_media_removes_its_file(): void
    {
        Storage::fake('public');
        $path = UploadedFile::fake()->image('product.png')->store('media', 'public');
        $metadata = app(MediaUploadMetadata::class)->forStoredFile('public', $path);
        $media = Media::query()->create(['disk' => 'public', 'path' => $path, 'original_name' => 'product.png', ...$metadata]);
        Product::factory()->create(['main_image' => $path]);

        try {
            $media->delete();
            $this->fail('Referenced media was deleted.');
        } catch (ValidationException) {
            Storage::disk('public')->assertExists($path);
        }

        Product::query()->update(['main_image' => null]);
        $media->delete();
        Storage::disk('public')->assertMissing($path);
    }

    public function test_contact_messages_can_be_read_answered_and_archived(): void
    {
        $message = ContactMessage::query()->create(['name' => 'Ayşe', 'email' => 'ayse@example.test', 'message' => 'Merhaba', 'locale' => 'tr']);

        $message->markRead();
        $message->markAnswered();
        $message->archive();

        $this->assertTrue($message->fresh()->is_read);
        $this->assertTrue($message->fresh()->is_answered);
        $this->assertNotNull($message->fresh()->archived_at);
    }

    public function test_roles_are_enforced_by_policies(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $manager = User::factory()->create(['role' => 'manager']);
        $editor = User::factory()->create(['role' => 'editor']);
        $translator = User::factory()->create(['role' => 'translator']);
        $viewer = User::factory()->create(['role' => 'viewer']);
        $product = Product::factory()->create();
        $settings = SiteSetting::current();

        $this->assertTrue(Gate::forUser($superAdmin)->allows('update', $product));
        $this->assertTrue(Gate::forUser($manager)->allows('update', $settings));
        $this->assertTrue(Gate::forUser($editor)->allows('update', $product));
        $this->assertFalse(Gate::forUser($editor)->allows('update', $settings));
        $this->assertFalse(Gate::forUser($translator)->allows('update', $product));
        $this->assertFalse(Gate::forUser($viewer)->allows('update', $product));
        $this->assertFalse(Gate::forUser($manager)->allows('viewAny', User::class));
    }

    public function test_page_section_changes_invalidate_only_the_section_cache(): void
    {
        Cache::put('cms.home.sections.tr', ['stale-section'], now()->addHour());
        Cache::put('cms.home.slider.tr', ['fresh-slider'], now()->addHour());

        $this->createSection('cache-section', 1, 'Cache bölümü');

        $this->assertNull(Cache::get('cms.home.sections.tr'));
        $this->assertSame(['fresh-slider'], Cache::get('cms.home.slider.tr'));
    }

    public function test_slider_and_menu_changes_invalidate_only_their_own_caches(): void
    {
        Cache::put('cms.home.sections.tr', ['fresh-section'], now()->addHour());
        Cache::put('cms.home.slider.tr', ['stale-slider'], now()->addHour());
        $slider = Slider::query()->create(['key' => 'home', 'name' => 'Slider', 'status' => 'draft']);

        $this->assertSame(['fresh-section'], Cache::get('cms.home.sections.tr'));
        $this->assertNull(Cache::get('cms.home.slider.tr'));

        Cache::put('cms.menu.header-primary.tr', ['stale-menu'], now()->addHour());
        Cache::put('cms.home.sections.tr', ['still-fresh'], now()->addHour());
        $menu = Menu::query()->create(['key' => 'header-primary', 'name' => 'Header', 'location' => 'header']);

        $this->assertNull(Cache::get('cms.menu.header-primary.tr'));
        $this->assertSame(['still-fresh'], Cache::get('cms.home.sections.tr'));
    }

    public function test_initial_cms_transfer_is_idempotent_and_preserves_existing_records(): void
    {
        Product::factory()->create(['is_active' => true]);

        app(CmsInitialContentSeeder::class)->seed();
        app(CmsInitialContentSeeder::class)->seed();

        $this->assertSame(3, Menu::query()->count());
        $this->assertSame(8, PageSection::query()->where('page_key', 'home')->count());
        $this->assertSame(1, Slider::query()->where('key', 'home')->count());
    }

    private function createSection(string $key, int $order, string $title, string $status = 'published'): PageSection
    {
        $section = PageSection::query()->create(['page_key' => 'home', 'key' => $key, 'type' => 'intro', 'status' => $status, 'is_active' => true, 'sort_order' => $order]);
        $section->translations()->create(['locale' => 'tr', 'title' => $title]);

        return $section;
    }
}
