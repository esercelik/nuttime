<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\SiteSetting;
use App\Support\LocalizedUrl;
use App\Support\SeoMetadata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

final class ContentController extends Controller
{
    public function __construct(
        private SeoMetadata $seoMetadata,
        private LocalizedUrl $localizedUrl,
    ) {}

    public function index(): View
    {
        $settings = $this->settings();
        $contents = Content::query()->published()->latest('published_at')->get()->map(fn (Content $content): array => $this->content($content))->all();

        return view('contents.index', [
            'contents' => $contents,
            'seo' => $this->seoMetadata->page(__('site.meta.contents.title'), __('site.meta.contents.description'), $this->localizedUrl->route('contents'), $settings, [], 'website', null, $this->localizedUrl->alternatives('contents')),
        ]);
    }

    public function show(string $slug): View|RedirectResponse
    {
        $contents = Content::query()->published()->get();
        $content = $contents->map(fn (Content $item): array => $this->content($item))->firstWhere('slug', $slug);
        abort_unless($content, 404);

        $settings = $this->settings();
        $url = $this->localizedUrl->route('content', null, ['slug' => $content['slug']]);
        $breadcrumbs = $this->seoMetadata->breadcrumbs([
            ['name' => __('site.nav.home'), 'url' => $this->localizedUrl->route('home')],
            ['name' => __('site.nav.contents'), 'url' => $this->localizedUrl->route('contents')],
            ['name' => $content['title'], 'url' => $url],
        ]);

        return view('contents.show', [
            'content' => $content,
            'seo' => $this->seoMetadata->page($content['seo_title'] ?: $content['title'], $content['seo_description'] ?: ($content['excerpt'] ?: $content['body']), $url, $settings, [$breadcrumbs], 'article', $content['image'], $this->localizedUrl->alternatives('content', $content['slugs'])),
        ]);
    }

    /** @return array<string, mixed> */
    private function content(Content $content): array
    {
        $locale = app()->getLocale();
        $translations = collect($content->translations ?? [])->keyBy('locale');
        $translation = $translations->get($locale) ?? $translations->get('en') ?? $translations->get('tr') ?? [];
        $slugs = collect(array_keys(config('nuttime.locales')))->mapWithKeys(function (string $locale) use ($content, $translations): array {
            return [$locale => $translations->get($locale)['slug'] ?? $translations->get('en')['slug'] ?? $content->slug];
        })->all();

        return [
            'slug' => $slugs[$locale],
            'slugs' => $slugs,
            'title' => $translation['title'] ?? $content->title,
            'excerpt' => $translation['excerpt'] ?? $content->excerpt,
            'body' => $translation['body'] ?? $content->body,
            'seo_title' => $translation['seo_title'] ?? $content->seo_title,
            'seo_description' => $translation['seo_description'] ?? $content->seo_description,
            'image' => $content->cover_image ? asset('storage/'.$content->cover_image) : null,
            'image_alt' => $content->cover_image_alt,
            'published_at' => $content->published_at,
        ];
    }

    /** @return array<string, mixed> */
    private function settings(): array
    {
        return Schema::hasTable('site_settings') ? SiteSetting::current()->toArray() : ['site_name' => 'Nuttime'];
    }
}
